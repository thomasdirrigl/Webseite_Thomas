<?php
require __DIR__ . '/inc/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

// Honeypot: Bots fuellen dieses versteckte Feld aus, Menschen nicht.
if (!empty($_POST['website'])) {
    redirect('index.php?status=check');
}

$firstName = trim(preg_replace('/[\p{C}]+/u', '', (string)($_POST['first_name'] ?? '')) ?? '');
$email = trim((string)($_POST['email'] ?? ''));
$consent = ($_POST['consent'] ?? '') === '1';

if ($firstName === '' || mb_strlen($firstName) > 100
    || strlen($email) > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL)
    || !$consent) {
    redirect('index.php?status=invalid');
}

purge_pending();

$pdo = db();
$stmt = $pdo->prepare('SELECT * FROM subscribers WHERE email = ?');
$stmt->execute([$email]);
$existing = $stmt->fetch();

// Bereits bestaetigt: nichts tun, aber dieselbe Meldung zeigen,
// damit nicht ausgelesen werden kann, wer eingetragen ist.
if ($existing && $existing['status'] === 'confirmed') {
    redirect('index.php?status=check');
}

// Schutz vor Mail-Bombing: nicht beliebig oft neu versenden.
if ($existing && $existing['last_mail_at'] > time() - config('resend_cooldown_minutes') * 60) {
    redirect('index.php?status=check');
}

$token = new_token();
$expires = time() + config('token_ttl_hours') * 3600;

if ($existing) {
    $pdo->prepare('UPDATE subscribers SET first_name = ?, confirm_token_hash = ?, confirm_token_expires = ?,
            consent_text = ?, created_at = ?, created_ip = ?, last_mail_at = ? WHERE id = ?')
        ->execute([$firstName, token_hash($token), $expires, CONSENT_TEXT, now_iso(), client_ip(), time(), $existing['id']]);
    $id = (int)$existing['id'];
} else {
    $pdo->prepare('INSERT INTO subscribers (first_name, email, status, confirm_token_hash, confirm_token_expires,
            unsubscribe_token, consent_text, created_at, created_ip, last_mail_at)
            VALUES (?, ?, \'pending\', ?, ?, ?, ?, ?, ?, ?)')
        ->execute([$firstName, $email, token_hash($token), $expires, new_token(), CONSENT_TEXT, now_iso(), client_ip(), time()]);
    $id = (int)$pdo->lastInsertId();
}

if (!send_confirm_mail($email, $firstName, $token)) {
    // Ohne zugestellte Mail kann niemand bestaetigen – Eintrag nicht behalten.
    $pdo->prepare("DELETE FROM subscribers WHERE id = ? AND status = 'pending'")->execute([$id]);
    redirect('index.php?status=mailfail');
}

redirect('index.php?status=check');

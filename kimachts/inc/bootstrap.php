<?php
// Gemeinsame Funktionen fuer die kimachts-Interessentenliste.

declare(strict_types=1);

const CONSENT_TEXT = 'Ich möchte per E-Mail informiert werden, sobald kimachts startet. '
    . 'Meine Einwilligung kann ich jederzeit widerrufen.';

$configFile = __DIR__ . '/config.php';
if (!is_file($configFile)) {
    http_response_code(500);
    exit('Konfiguration fehlt: inc/config.sample.php nach inc/config.php kopieren.');
}
$config = require $configFile;

function config(string $key)
{
    global $config;
    return $config[$key];
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }
    $pdo = new PDO('sqlite:' . config('db_path'));
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA busy_timeout = 5000');
    $pdo->exec('CREATE TABLE IF NOT EXISTS subscribers (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        first_name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE COLLATE NOCASE,
        status TEXT NOT NULL CHECK (status IN (\'pending\', \'confirmed\')),
        confirm_token_hash TEXT,
        confirm_token_expires INTEGER,
        unsubscribe_token TEXT NOT NULL UNIQUE,
        consent_text TEXT NOT NULL,
        created_at TEXT NOT NULL,
        created_ip TEXT,
        confirmed_at TEXT,
        confirmed_ip TEXT,
        last_mail_at INTEGER NOT NULL
    )');
    return $pdo;
}

// Loescht abgelaufene, nie bestaetigte Eintraege (Datenminimierung).
function purge_pending(): void
{
    $cutoff = time() - config('pending_retention_days') * 86400;
    db()->prepare("DELETE FROM subscribers WHERE status = 'pending' AND last_mail_at < ?")
        ->execute([$cutoff]);
}

function new_token(): string
{
    return bin2hex(random_bytes(32));
}

function token_hash(string $token): string
{
    return hash('sha256', $token);
}

function now_iso(): string
{
    return gmdate('Y-m-d\TH:i:s\Z');
}

function client_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '';
}

function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function url(string $path): string
{
    return rtrim(config('base_url'), '/') . '/' . ltrim($path, '/');
}

function redirect(string $path): void
{
    header('Location: ' . url($path), true, 303);
    exit;
}

function send_mail(string $to, string $subject, string $body): bool
{
    $from = config('mail_from');
    $headers = [
        'From: ' . mb_encode_mimeheader(config('mail_from_name'), 'UTF-8') . ' <' . $from . '>',
        'Reply-To: ' . config('reply_to'),
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=utf-8',
        'Content-Transfer-Encoding: 8bit',
    ];
    return mail(
        $to,
        mb_encode_mimeheader($subject, 'UTF-8'),
        $body,
        implode("\r\n", $headers),
        '-f' . $from
    );
}

function send_confirm_mail(string $email, string $firstName, string $token): bool
{
    $link = url('bestaetigen.php?token=' . $token);
    $hours = config('token_ttl_hours');
    $body = "Hallo $firstName,\n\n"
        . "danke für dein Interesse an kimachts!\n\n"
        . "Bitte bestätige deine E-Mail-Adresse über diesen Link:\n\n"
        . "$link\n\n"
        . "Der Link ist $hours Stunden gültig. Erst nach deiner Bestätigung "
        . "nehmen wir dich in die Interessentenliste auf.\n\n"
        . "Falls du dich nicht eingetragen hast, ignoriere diese E-Mail einfach – "
        . "deine Adresse wird dann automatisch wieder gelöscht.\n\n"
        . "Viele Grüße\nThomas von kimachts\n";
    return send_mail($email, 'Bitte bestätige deine Anmeldung bei kimachts', $body);
}

function send_welcome_mail(string $email, string $firstName, string $unsubscribeToken): bool
{
    $link = url('abmelden.php?token=' . $unsubscribeToken);
    $body = "Hallo $firstName,\n\n"
        . "deine Anmeldung ist bestätigt. Wir melden uns, sobald kimachts live geht.\n\n"
        . "Du möchtest doch nicht mehr informiert werden? Hier kannst du dich "
        . "jederzeit wieder austragen – deine Daten werden dann sofort gelöscht:\n\n"
        . "$link\n\n"
        . "Viele Grüße\nThomas von kimachts\n";
    return send_mail($email, 'Willkommen auf der kimachts-Interessentenliste', $body);
}

function render(string $title, string $content): void
{
    header('Content-Type: text/html; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: no-referrer');
    header("Content-Security-Policy: default-src 'self'; frame-ancestors 'none'");
    $impressum = 'impressum.php';
    $datenschutz = 'datenschutz.php';
    $title = h($title);
    echo <<<HTML
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>$title</title>
<meta name="description" content="kimachts startet bald. Trag dich ein und erfahre als Erstes, wann es losgeht.">
<meta name="robots" content="index, follow">
<link rel="stylesheet" href="assets/style.css">
<link rel="icon" href="data:,">
</head>
<body>
<main class="wrap">
  <div class="brand">ki<span>macht</span>s</div>
  $content
</main>
<footer class="footer">
  <a href="$impressum">Impressum</a>
  <a href="$datenschutz">Datenschutz</a>
</footer>
</body>
</html>
HTML;
    exit;
}

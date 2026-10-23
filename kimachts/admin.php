<?php
require __DIR__ . '/inc/bootstrap.php';

// Einfacher, passwortgeschuetzter Ueberblick mit CSV-Export.

$hash = config('admin_password_hash');
if ($hash === '') {
    http_response_code(404);
    exit;
}

session_set_cookie_params(['httponly' => true, 'samesite' => 'Strict', 'secure' => !empty($_SERVER['HTTPS'])]);
session_start();
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = new_token();
}
$csrf = $_SESSION['csrf'];
$postOk = $_SERVER['REQUEST_METHOD'] === 'POST' && hash_equals($csrf, (string)($_POST['csrf'] ?? ''));

if ($postOk && isset($_POST['password'])) {
    sleep(1); // bremst Passwort-Raten
    if (password_verify((string)$_POST['password'], $hash)) {
        session_regenerate_id(true);
        $_SESSION['admin'] = true;
    }
    redirect('admin.php');
}

if (empty($_SESSION['admin'])) {
    render('Admin – kimachts', <<<HTML
  <h1>Admin</h1>
  <form class="signup" method="post" action="admin.php">
    <input type="hidden" name="csrf" value="$csrf">
    <label for="password">Passwort</label>
    <input id="password" name="password" type="password" autocomplete="current-password" required>
    <button type="submit">Anmelden</button>
  </form>
HTML);
}

if ($postOk && isset($_POST['logout'])) {
    $_SESSION = [];
    session_destroy();
    redirect('admin.php');
}

if ($postOk && isset($_POST['delete'])) {
    db()->prepare('DELETE FROM subscribers WHERE id = ?')->execute([(int)$_POST['delete']]);
    redirect('admin.php');
}

purge_pending();

if (($_GET['export'] ?? '') === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="kimachts-interessenten-' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF"); // BOM, damit Excel Umlaute korrekt anzeigt
    fputcsv($out, ['Vorname', 'E-Mail', 'Eingetragen', 'Bestaetigt', 'Abmeldelink'], ';');
    $rows = db()->query("SELECT * FROM subscribers WHERE status = 'confirmed' ORDER BY confirmed_at");
    foreach ($rows as $r) {
        fputcsv($out, [$r['first_name'], $r['email'], $r['created_at'], $r['confirmed_at'],
            url('abmelden.php?token=' . $r['unsubscribe_token'])], ';');
    }
    exit;
}

$counts = db()->query('SELECT status, COUNT(*) AS n FROM subscribers GROUP BY status')->fetchAll(PDO::FETCH_KEY_PAIR);
$confirmed = (int)($counts['confirmed'] ?? 0);
$pending = (int)($counts['pending'] ?? 0);

$tableRows = '';
foreach (db()->query('SELECT * FROM subscribers ORDER BY id DESC') as $r) {
    $state = $r['status'] === 'confirmed' ? 'bestätigt' : 'offen';
    $tableRows .= '<tr><td>' . h($r['first_name']) . '</td><td>' . h($r['email']) . '</td><td>' . $state
        . '</td><td>' . h($r['confirmed_at'] ?? $r['created_at']) . '</td><td>'
        . '<form method="post" action="admin.php" class="inline"><input type="hidden" name="csrf" value="' . $csrf . '">'
        . '<button name="delete" value="' . (int)$r['id'] . '" class="link">löschen</button></form></td></tr>';
}

render('Admin – kimachts', <<<HTML
  <h1>Interessenten</h1>
  <p class="lead"><strong>$confirmed</strong> bestätigt · $pending offen</p>
  <p><a class="button" href="admin.php?export=csv">CSV-Export (bestätigte)</a></p>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Vorname</th><th>E-Mail</th><th>Status</th><th>Datum (UTC)</th><th></th></tr></thead>
      <tbody>$tableRows</tbody>
    </table>
  </div>
  <form method="post" action="admin.php">
    <input type="hidden" name="csrf" value="$csrf">
    <button name="logout" value="1" class="link">Abmelden</button>
  </form>
HTML);

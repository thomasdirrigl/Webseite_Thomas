<?php
require __DIR__ . '/inc/bootstrap.php';

// Bestaetigung erst per Klick auf einen Button (POST). So bestaetigen
// Link-Scanner von Mailprogrammen, die nur GET aufrufen, nicht versehentlich.

$token = (string)($_POST['token'] ?? $_GET['token'] ?? '');
if (!preg_match('/^[0-9a-f]{64}$/', $token)) {
    render('Link ungültig – kimachts', '<h1>Link ungültig</h1><p class="lead">Dieser Bestätigungslink ist unvollständig. Bitte kopiere ihn vollständig aus der E-Mail.</p>');
}

$stmt = db()->prepare("SELECT * FROM subscribers WHERE status = 'pending' AND confirm_token_hash = ?");
$stmt->execute([token_hash($token)]);
$row = $stmt->fetch();

if (!$row || $row['confirm_token_expires'] < time()) {
    render('Link abgelaufen – kimachts', '<h1>Link abgelaufen</h1><p class="lead">Dieser Bestätigungslink ist abgelaufen oder wurde bereits verwendet. <a href="index.php">Trag dich einfach erneut ein.</a></p>');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $t = h($token);
    $name = h($row['first_name']);
    render('Anmeldung bestätigen – kimachts', <<<HTML
  <h1>Hallo $name!</h1>
  <p class="lead">Ein Klick noch, dann bist du auf der Interessentenliste.</p>
  <form method="post" action="bestaetigen.php">
    <input type="hidden" name="token" value="$t">
    <button type="submit">Anmeldung bestätigen</button>
  </form>
HTML);
}

db()->prepare("UPDATE subscribers SET status = 'confirmed', confirm_token_hash = NULL, confirm_token_expires = NULL,
        confirmed_at = ?, confirmed_ip = ? WHERE id = ?")
    ->execute([now_iso(), client_ip(), $row['id']]);

send_welcome_mail($row['email'], $row['first_name'], $row['unsubscribe_token']);

$name = h($row['first_name']);
render('Bestätigt – kimachts', <<<HTML
  <h1>Danke, $name!</h1>
  <p class="lead">Deine Anmeldung ist bestätigt. Wir melden uns, sobald kimachts live geht.</p>
  <p class="small">Einen Link zum Austragen findest du in der Willkommens-Mail.</p>
HTML);

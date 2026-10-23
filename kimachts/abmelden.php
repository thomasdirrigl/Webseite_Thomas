<?php
require __DIR__ . '/inc/bootstrap.php';

// Wie bei der Bestaetigung: Loeschen erst nach Klick (POST).

$token = (string)($_POST['token'] ?? $_GET['token'] ?? '');
$row = false;
if (preg_match('/^[0-9a-f]{64}$/', $token)) {
    $stmt = db()->prepare('SELECT id FROM subscribers WHERE unsubscribe_token = ?');
    $stmt->execute([$token]);
    $row = $stmt->fetch();
}

if (!$row) {
    render('Abmelden – kimachts', '<h1>Nicht gefunden</h1><p class="lead">Zu diesem Link ist kein Eintrag (mehr) gespeichert. Du bist nicht auf der Liste.</p>');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $t = h($token);
    render('Abmelden – kimachts', <<<HTML
  <h1>Austragen?</h1>
  <p class="lead">Wenn du dich austrägst, löschen wir deinen Vornamen und deine E-Mail-Adresse sofort.</p>
  <form method="post" action="abmelden.php">
    <input type="hidden" name="token" value="$t">
    <button type="submit">Ja, austragen</button>
  </form>
HTML);
}

db()->prepare('DELETE FROM subscribers WHERE id = ?')->execute([$row['id']]);

render('Abgemeldet – kimachts', '<h1>Du bist ausgetragen.</h1><p class="lead">Deine Daten wurden gelöscht. Schade, dass du gehst – du kannst dich jederzeit wieder <a href="index.php">eintragen</a>.</p>');

<?php
require __DIR__ . '/inc/bootstrap.php';

$messages = [
    'check' => ['ok', 'Fast geschafft! Wir haben dir eine E-Mail geschickt. Bitte klicke auf den Link darin, um deine Anmeldung zu bestätigen.'],
    'invalid' => ['error', 'Bitte gib deinen Vornamen und eine gültige E-Mail-Adresse an und bestätige die Einwilligung.'],
    'mailfail' => ['error', 'Die Bestätigungs-E-Mail konnte gerade nicht versendet werden. Bitte versuche es später noch einmal.'],
];
$status = $_GET['status'] ?? '';
$notice = '';
if (isset($messages[$status])) {
    [$type, $text] = $messages[$status];
    $notice = '<p class="notice notice-' . $type . '" role="status">' . h($text) . '</p>';
}

$consent = h(CONSENT_TEXT);
$datenschutz = h(config('datenschutz_url'));

$form = $status === 'check' ? '' : <<<HTML
  <form class="signup" method="post" action="anmelden.php" novalidate>
    <label for="first_name">Vorname</label>
    <input id="first_name" name="first_name" type="text" autocomplete="given-name" maxlength="100" required>

    <label for="email">E-Mail-Adresse</label>
    <input id="email" name="email" type="email" autocomplete="email" maxlength="254" required>

    <div class="hp" aria-hidden="true">
      <label for="website">Bitte leer lassen</label>
      <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
    </div>

    <label class="consent">
      <input type="checkbox" name="consent" value="1" required>
      <span>$consent Details in der <a href="$datenschutz">Datenschutzerklärung</a>.</span>
    </label>

    <button type="submit">Jetzt eintragen</button>
    <p class="small">Du erhältst eine E-Mail mit einem Bestätigungslink (Double-Opt-in). Kein Spam, nur die Info zum Start.</p>
  </form>
HTML;

render('kimachts – bald verfügbar', <<<HTML
  <h1>Bald geht's los.</h1>
  <p class="lead">kimachts ist gerade im Endspurt. Trag dich ein und erfahre als Erstes, wann die Plattform live geht.</p>
  $notice
  $form
HTML);

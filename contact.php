<?php
// Einfacher Kontaktformular-Handler fuer all-inkl PHP-Hosting.
// Empfaenger auf die eigene Impressum-Adresse gesetzt.

header('Content-Type: application/json; charset=utf-8');

$recipient = 'info@thomasdirrigl.de';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Honeypot: Bots fuellen dieses versteckte Feld aus, Menschen nicht.
if (!empty($_POST['website'])) {
    // Stiller Erfolg, damit Bots nichts lernen.
    echo json_encode(['ok' => true]);
    exit;
}

// Zeitsperre: main.js sendet die Sekunden seit dem Laden der Seite mit.
// Fehlt der Wert (Bot ohne JavaScript, direkter POST) oder wurde schneller
// als ein Mensch abgeschickt, wird die Anfrage still verworfen.
$elapsed = filter_var($_POST['elapsed'] ?? '', FILTER_VALIDATE_INT);
if ($elapsed === false || $elapsed < 3) {
    echo json_encode(['ok' => true]);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Ungueltige Eingabe']);
    exit;
}

// Inhaltsfilter: Links in Name oder Nachricht kommen praktisch nur von Spam-Bots.
if (preg_match('~https?://|www\.|\[url~i', $name . ' ' . $message)) {
    echo json_encode(['ok' => true]);
    exit;
}

$name = str_replace(["\r", "\n"], '', $name);
$email = str_replace(["\r", "\n"], '', $email);

$subject = 'Neue Kontaktanfrage von der Website';
$body = "Name: $name\nE-Mail: $email\n\nNachricht:\n$message\n";

$headers = [];
$headers[] = 'From: Website Kontaktformular <no-reply@thomasdirrigl.de>';
$headers[] = 'Reply-To: ' . $email;
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-Type: text/plain; charset=utf-8';
$headers[] = 'Content-Transfer-Encoding: 8bit';

$success = mail($recipient, $subject, $body, implode("\r\n", $headers));

if ($success) {
    echo json_encode(['ok' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Mail konnte nicht gesendet werden']);
}

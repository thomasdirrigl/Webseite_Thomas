<?php
// Vorlage: als config.php im selben Ordner speichern und anpassen.
// config.php ist per .gitignore vom Repository ausgeschlossen.

return [
    // Oeffentliche Adresse dieses Ordners, ohne abschliessenden Slash.
    'base_url' => 'https://kimachts.de',

    // SQLite-Datei. Am besten ausserhalb des Web-Roots ablegen,
    // z. B. '/www/htdocs/w0123456/kimachts-data/kimachts.sqlite'.
    // Der Ordner data/ ist zusaetzlich per .htaccess gesperrt.
    'db_path' => __DIR__ . '/../data/kimachts.sqlite',

    // Absender der Bestaetigungs-Mails. Muss ein Postfach/Alias der
    // eigenen Domain sein, sonst landen die Mails im Spam.
    'mail_from' => 'no-reply@kimachts.de',
    'mail_from_name' => 'kimachts',
    'reply_to' => 'info@thomasdirrigl.de',

    'impressum_url' => 'https://thomasdirrigl.de/impressum.html',
    'datenschutz_url' => 'https://thomasdirrigl.de/datenschutz.html#interessentenliste',

    // Passwort-Hash fuer admin.php. Erzeugen mit:
    //   php -r "echo password_hash('DEIN-PASSWORT', PASSWORD_DEFAULT), PHP_EOL;"
    // Leer lassen deaktiviert den Admin-Bereich.
    'admin_password_hash' => '',

    // Gueltigkeit des Bestaetigungslinks.
    'token_ttl_hours' => 48,
    // Unbestaetigte Eintraege werden nach dieser Zeit automatisch geloescht.
    'pending_retention_days' => 7,
    // Mindestabstand fuer eine erneute Bestaetigungs-Mail an dieselbe Adresse.
    'resend_cooldown_minutes' => 10,
];

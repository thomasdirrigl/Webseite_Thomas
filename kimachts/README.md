# kimachts – Interessentenliste

Eintragungsseite (Vorname + E-Mail) mit Double-Opt-in. Läuft mit reinem PHP (≥ 8.0)
und SQLite – kein MySQL-Setup nötig, passt direkt auf das all-inkl-Hosting.

## Ablauf

1. `index.php` – Formular mit Vorname, E-Mail und Einwilligungs-Checkbox.
2. `anmelden.php` – speichert den Eintrag als *offen* und schickt eine Mail mit Bestätigungslink (48 h gültig).
3. `bestaetigen.php` – nach Klick auf „Anmeldung bestätigen“ wird der Eintrag *bestätigt*, es folgt eine Willkommens-Mail mit Abmeldelink.
4. `abmelden.php` – löscht den Eintrag sofort.
5. `admin.php` – passwortgeschützte Übersicht, Löschen einzelner Einträge, CSV-Export aller Bestätigten (inkl. persönlichem Abmeldelink für die Start-Mail).

## DSGVO-Details

- Aufnahme in die Liste erst nach bestätigtem Double-Opt-in.
- Bestätigen/Abmelden erst per Button-Klick, damit Link-Scanner von Mailprogrammen nichts auslösen.
- Nachweis gespeichert: Zeitpunkt + IP von Eintragung und Bestätigung, Wortlaut der Einwilligung.
- Unbestätigte Einträge werden nach 7 Tagen automatisch gelöscht.
- Bestätigungs-Token wird nur gehasht gespeichert.
- Keine Cookies, keine externen Ressourcen (Fonts, Tracking) auf der Eintragungsseite.
- Keine Rückmeldung, ob eine Adresse schon eingetragen ist; Mail-Versand an dieselbe Adresse höchstens alle 10 Minuten.
- Abschnitt 6 der `datenschutz.html` beschreibt die Verarbeitung.

## Einrichtung bei all-inkl

1. Im KAS die Subdomain `list.kimachts.de` anlegen und als Ziel den hochgeladenen Ordner `kimachts/` wählen (er ist dann das Document-Root der Subdomain).
2. `inc/config.sample.php` nach `inc/config.php` kopieren und anpassen:
   - `base_url` – `https://list.kimachts.de` (bereits voreingestellt)
   - `mail_from` – Absenderadresse **der eigenen Domain** (im KAS als Postfach oder Weiterleitung anlegen)
   - `impressum_url` / `datenschutz_url`
   - `admin_password_hash` – erzeugen mit
     `php -r "echo password_hash('DEIN-PASSWORT', PASSWORD_DEFAULT), PHP_EOL;"`
   - optional `db_path` auf einen Ordner außerhalb des Web-Roots setzen
3. Der Ordner für die Datenbank (Standard `data/`) muss für PHP beschreibbar sein. Die Datei wird beim ersten Aufruf automatisch angelegt.
4. SSL-Zertifikat (Let's Encrypt) für `list.kimachts.de` im KAS aktivieren (`.htaccess` leitet auf HTTPS um).
5. Testen: selbst eintragen, Mail bestätigen, in `admin.php` prüfen, wieder abmelden.

Läuft kimachts unter einer eigenen Domain, braucht diese ein eigenes Impressum; die
Links im Footer zeigen per Konfiguration auf die vorhandenen Seiten.

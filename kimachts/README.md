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
- Eigene `impressum.php` und `datenschutz.php` unter der Subdomain.

## Einrichtung bei all-inkl

1. Im KAS die Subdomain `list.kimachts.de` anlegen und als Ziel den hochgeladenen Ordner `kimachts/` wählen (er ist dann das Document-Root der Subdomain).
2. `inc/config.sample.php` nach `inc/config.php` kopieren und anpassen:
   - `base_url` – `https://list.kimachts.de` (bereits voreingestellt)
   - `mail_from` – Absenderadresse **der eigenen Domain** (im KAS als Postfach oder Weiterleitung anlegen)
   - `admin_password_hash` – erzeugen mit
     `php -r "echo password_hash('DEIN-PASSWORT', PASSWORD_DEFAULT), PHP_EOL;"`
   - optional `db_path` auf einen Ordner außerhalb des Web-Roots setzen
3. Der Ordner für die Datenbank (Standard `data/`) muss für PHP beschreibbar sein. Die Datei wird beim ersten Aufruf automatisch angelegt.
4. SSL-Zertifikat (Let's Encrypt) für `list.kimachts.de` im KAS aktivieren (`.htaccess` leitet auf HTTPS um).
5. Testen: selbst eintragen, Mail bestätigen, in `admin.php` prüfen, wieder abmelden.


## Aufteilung all-inkl / Hetzner

Die Plattform `kimachts.de` läuft auf dem Hetzner-Cloud-Server, nur die Subdomain
`list.kimachts.de` liegt bei all-inkl. Dafür müssen DNS und Mail passen:

**DNS** (dort, wo die Zone von `kimachts.de` verwaltet wird):

| Name   | Typ        | Ziel                                   |
|--------|------------|----------------------------------------|
| `@`    | A / AAAA   | IP des Hetzner-Servers                 |
| `list` | A / AAAA   | IP des all-inkl-Servers (steht im KAS) |

Die Domain liegt bei all-inkl, `@` (und ggf. `www`) zeigt per A-Record auf Hetzner.
Nach dem Anlegen der Subdomain im KAS unter „Tools → DNS-Einstellungen“ prüfen:

- Zeigt der Platzhalter `*` ebenfalls auf die Hetzner-IP, landet `list` sonst bei
  Hetzner. Dann einen eigenen A-Record `list` mit der all-inkl-Server-IP anlegen
  (die IP steht beim bisherigen `@`-Eintrag bzw. in den Server-Infos im KAS).
- Kontrolle: `nslookup list.kimachts.de` muss die all-inkl-IP liefern, erst dann
  das Let's-Encrypt-Zertifikat im KAS aktivieren.

**Mail:** Die Bestätigungs-Mails verschickt der all-inkl-Server als
`no-reply@kimachts.de`. Damit sie nicht im Spam landen, muss der SPF-Eintrag von
`kimachts.de` all-inkl erlauben, z. B.
`v=spf1 a mx include:spf.kasserver.com ~all`. Der MX-Eintrag bleibt bei all-inkl,
dort liegt auch das Postfach; all-inkl legt diesen SPF-Eintrag standardmäßig an –
nur prüfen, dass er beim Umstellen auf Hetzner nicht gelöscht wurde. Verschickt der Hetzner-Server später
ebenfalls Mails, kommt dessen Mailweg zusätzlich in denselben Eintrag – es darf nur
**einen** SPF-Record geben. Wenn möglich DKIM für `kimachts.de` im KAS aktivieren.

**Datenschutz:** Solange die Liste nur bei all-inkl liegt, stimmt die
`datenschutz.php`. Werden die Adressen später auf den Hetzner-Server übernommen oder
die Start-Mail von dort verschickt, muss Hetzner (Auftragsverarbeitung, Art. 28 DSGVO)
in der Datenschutzerklärung ergänzt werden – idealerweise schon vor dem ersten Eintrag.

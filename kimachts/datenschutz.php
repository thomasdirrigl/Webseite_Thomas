<?php
require __DIR__ . '/inc/bootstrap.php';

render('Datenschutzerklärung – kimachts', <<<'HTML'
  <div class="legal">
    <a href="index.php" class="back">← Zurück zur Eintragung</a>
    <h1>Datenschutzerklärung</h1>

    <h2>1. Verantwortlicher</h2>
    <p>
      Verantwortlicher im Sinne der Datenschutz-Grundverordnung (DSGVO) ist:<br>
      Thomas Dirrigl<br>
      Am Bärenstein 3<br>
      91327 Gößweinstein<br>
      E-Mail: <a href="mailto:info@thomasdirrigl.de">info@thomasdirrigl.de</a>
    </p>

    <h2>2. Hosting und Server-Logfiles</h2>
    <p>
      Diese Website wird bei der ALL-INKL.COM – Neue Medien Münnich (Inhaber: René Münnich), Hauptstraße 68, 02742 Friedersdorf („all-inkl“) gehostet. Beim Aufruf der Website erhebt all-inkl automatisch Informationen in sogenannten Server-Log-Dateien, die Ihr Browser übermittelt (u. a. IP-Adresse, Datum und Uhrzeit der Anfrage, Browsertyp, Betriebssystem, aufgerufene URL). Diese Daten werden nicht mit anderen Datenquellen zusammengeführt.
    </p>
    <p>
      Die Verarbeitung erfolgt auf Grundlage von Art. 6 Abs. 1 lit. f DSGVO aus unserem berechtigten Interesse an einem sicheren und technisch fehlerfreien Betrieb der Website. Mit all-inkl besteht ein Vertrag zur Auftragsverarbeitung gemäß Art. 28 DSGVO. Auch die Daten der Interessentenliste (Abschnitt 4) werden auf den Servern von all-inkl in Deutschland gespeichert, und die E-Mails werden über deren Mailserver versendet.
    </p>

    <h2>3. Cookies, Analyse und externe Inhalte</h2>
    <p>
      Diese Website setzt keine Cookies, verwendet keine Analyse- oder Tracking-Dienste und bindet keine Inhalte von Drittanbietern (z. B. Schriftarten, Videos, Karten) ein.
    </p>

    <h2 id="interessentenliste">4. Interessentenliste (Double-Opt-in)</h2>
    <p>
      Sie können sich auf dieser Seite in eine Interessentenliste eintragen, um per E-Mail über den Start der Plattform kimachts informiert zu werden. Dabei verarbeiten wir Ihren Vornamen (für die persönliche Ansprache) und Ihre E-Mail-Adresse. Die Angaben sind freiwillig.
    </p>
    <p>
      Wir verwenden das Double-Opt-in-Verfahren: Nach der Eintragung erhalten Sie eine E-Mail mit einem Bestätigungslink. Erst wenn Sie diesen aufrufen und die Anmeldung bestätigen, werden Sie in die Liste aufgenommen. So stellen wir sicher, dass sich niemand mit fremden E-Mail-Adressen eintragen kann. Nach der Bestätigung erhalten Sie eine Willkommens-E-Mail mit einem Link zum Austragen.
    </p>
    <p>
      Zum Nachweis Ihrer Einwilligung speichern wir den Zeitpunkt der Eintragung und der Bestätigung, die jeweils verwendete IP-Adresse sowie den Wortlaut der Einwilligungserklärung.
    </p>
    <p>
      Rechtsgrundlage für die Speicherung und den Versand der Information ist Ihre Einwilligung gemäß Art. 6 Abs. 1 lit. a DSGVO. Die Protokollierung des Anmeldeverfahrens erfolgt auf Grundlage unseres berechtigten Interesses am Nachweis der Einwilligung (Art. 6 Abs. 1 lit. f DSGVO i. V. m. Art. 7 Abs. 1 DSGVO).
    </p>
    <p>
      Nicht bestätigte Eintragungen werden nach spätestens 7 Tagen automatisch gelöscht. Bestätigte Daten speichern wir, bis Sie Ihre Einwilligung widerrufen oder der Zweck – die Information über den Start von kimachts – entfallen ist. Eine Weitergabe an Dritte findet nicht statt.
    </p>
    <p>
      Sie können Ihre Einwilligung jederzeit mit Wirkung für die Zukunft widerrufen, entweder über den Abmeldelink in unseren E-Mails oder per Nachricht an die oben genannte E-Mail-Adresse. Ihre Daten werden dann umgehend gelöscht.
    </p>

    <h2>5. Ihre Rechte</h2>
    <p>Sie haben im Rahmen der geltenden gesetzlichen Bestimmungen jederzeit das Recht auf:</p>
    <ul>
      <li>Auskunft über Ihre gespeicherten personenbezogenen Daten (Art. 15 DSGVO)</li>
      <li>Berichtigung unrichtiger personenbezogener Daten (Art. 16 DSGVO)</li>
      <li>Löschung Ihrer bei uns gespeicherten Daten (Art. 17 DSGVO)</li>
      <li>Einschränkung der Datenverarbeitung (Art. 18 DSGVO)</li>
      <li>Datenübertragbarkeit (Art. 20 DSGVO)</li>
      <li>Widerspruch gegen die Verarbeitung Ihrer Daten (Art. 21 DSGVO)</li>
      <li>Widerruf einer erteilten Einwilligung mit Wirkung für die Zukunft (Art. 7 Abs. 3 DSGVO)</li>
    </ul>
    <p>
      Zur Ausübung dieser Rechte genügt eine Nachricht an die oben genannte E-Mail-Adresse. Ihnen steht zudem ein Beschwerderecht bei einer Datenschutz-Aufsichtsbehörde zu, für uns zuständig ist das Bayerische Landesamt für Datenschutzaufsicht (BayLDA), Promenade 18, 91522 Ansbach.
    </p>

    <h2>6. SSL-/TLS-Verschlüsselung</h2>
    <p>
      Diese Seite nutzt aus Sicherheitsgründen eine SSL-/TLS-Verschlüsselung zur Übertragung Ihrer Eingaben. Eine verschlüsselte Verbindung erkennen Sie an „https://“ in der Adresszeile Ihres Browsers.
    </p>

    <h2>7. Änderung dieser Datenschutzerklärung</h2>
    <p>
      Wir behalten uns vor, diese Datenschutzerklärung anzupassen, damit sie stets den aktuellen rechtlichen Anforderungen entspricht oder um Änderungen unserer Leistungen umzusetzen.
    </p>
    <p>Stand: Oktober 2026</p>
  </div>
HTML);

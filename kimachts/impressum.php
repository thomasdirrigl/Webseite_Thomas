<?php
require __DIR__ . '/inc/bootstrap.php';

render('Impressum – kimachts', <<<'HTML'
  <div class="legal">
    <a href="index.php" class="back">← Zurück zur Eintragung</a>
    <h1>Impressum</h1>

    <h2>Angaben gemäß § 5 DDG</h2>
    <p>
      Thomas Dirrigl<br>
      Am Bärenstein 3<br>
      91327 Gößweinstein<br>
      Deutschland
    </p>

    <h2>Kontakt</h2>
    <p>E-Mail: <a href="mailto:info@thomasdirrigl.de">info@thomasdirrigl.de</a></p>

    <h2>Umsatzsteuer-Identifikationsnummer</h2>
    <p>
      Umsatzsteuer-Identifikationsnummer gemäß § 27a Umsatzsteuergesetz:<br>
      DE340323061
    </p>

    <h2>Verantwortlich für den Inhalt nach § 18 Abs. 2 MStV</h2>
    <p>
      Thomas Dirrigl<br>
      Am Bärenstein 3<br>
      91327 Gößweinstein
    </p>

    <h2>Verbraucherstreitbeilegung/Universalschlichtungsstelle</h2>
    <p>Wir sind nicht bereit oder verpflichtet, an Streitbeilegungsverfahren vor einer Verbraucherschlichtungsstelle teilzunehmen.</p>

    <h2>Haftung für Inhalte</h2>
    <p>
      Als Diensteanbieter sind wir gemäß § 7 Abs. 1 DDG für eigene Inhalte auf diesen Seiten nach den allgemeinen Gesetzen verantwortlich. Nach §§ 8 bis 10 DDG sind wir als Diensteanbieter jedoch nicht verpflichtet, übermittelte oder gespeicherte fremde Informationen zu überwachen oder nach Umständen zu forschen, die auf eine rechtswidrige Tätigkeit hinweisen. Verpflichtungen zur Entfernung oder Sperrung der Nutzung von Informationen nach den allgemeinen Gesetzen bleiben hiervon unberührt. Eine diesbezügliche Haftung ist jedoch erst ab dem Zeitpunkt der Kenntnis einer konkreten Rechtsverletzung möglich. Bei Bekanntwerden von entsprechenden Rechtsverletzungen werden wir diese Inhalte umgehend entfernen.
    </p>
  </div>
HTML);

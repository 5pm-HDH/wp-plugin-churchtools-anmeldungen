# Wordpress-Plugin ChurchTools Anmeldung

Dieses Git-Repo soll ein Proof of Concept vorlegen, wie ein mögliches Wordpress-Plugin für ChurchTools Anmeldungen aufgebaut sein könnte.

Wenn du Interesse hast, bei der Umsetzung des Plugins mitzuhelfen melde dich gern bei mir:

* Lukas Dumberger:
  * Email: lukas.dumberger@gmail.com
  * CT-Forum: https://forum.church.tools/user/dumbergerl
  * GitHub: https://github.com/DumbergerL

Beiträge aus dem ChurchTools-Forum
* [Wordpress Formular mit CT verbinden?](https://forum.church.tools/topic/8130/wordpress-formular-mit-ct-verbinden/4)
* [Wordpress Plugin zur direkten Integration von Gruppenhomepages](https://forum.church.tools/topic/7528/wordpress-plugin-zur-direkten-integration-von-gruppenhomepages?_=1643706999789)
   * Ansatz von skipy, der ein Gutenberg-Plugin dazu entwickelt hat. (client-seitiger Aufruf der CT-API)

## Problemstellung und Zielsetzung

Aktuell nutzen viele Webseiten ein IFrame um die ChurchTools-Anmeldungen auf ihrer Webseite einzubinden:

![Beispiel Webseite](docs/Website-IFrame.PNG)

Das Problem ist, dass sich die Darstellung des IFrames nur in mancher Hinsicht anpassen lässt. Ziel ist deshalb eine Plugin für Wordpress zu konzipieren, dass die Daten direkt auf ChurchTools abruft und anschließend in einem eigenen/konfigurierten Format darstellt.

## Idee 1: "Template-Ansatz"

Idee: Die Daten werden vom Plugin aus ChurchTools direkt geladen. Für das Rendern auf der Seite wird eine Template Bibliothek verwendet. Der HTML-Code für die Darstellung auf der Seite wird mit Markern versehen die während dem Render-Prozess mit Daten befüllt werden. Beispiele für solche Marker könnten sein `{{ title }}` für den Titel der Anmeldung, `{{ image_url }}` für den Link zum Bild, etc.:

![Anmeldung bearbeiten](docs/MockUpDetail2.jpeg)

Für die Abfrage der Daten ist lediglich die Url zur ChurchTools installation notwendig und den "Link" der Gruppen-Homepage (GroupHomepage). Weiter Optionen wie Caching sind denkbar:

![Anmeldung bearbeiten](docs/MockUpDetail1.jpeg)

Die Einbindung auf einer Seite erfolgt mittels Short-Code:

![Uebersicht](docs/MockUpOverview.jpeg)

**Bibliotheken:**

* [CT-Api Wrapper](https://github.com/5pm-HDH/churchtools-api) für den Datentransfer
* [Twig](https://twig.symfony.com/) als mögliche Template-Engine

## Idee 2: "Client-seitiger API-Aufruf"

Wurde bereits in einem ChurchTools-Forum Beitrag vorgestellt: https://forum.church.tools/topic/7528/wordpress-plugin-zur-direkten-integration-von-gruppenhomepages?_=1643706999789 Es gibt allerdings bisher kein weiterführendes Projekt hierzu.

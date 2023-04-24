# Wordpress-Plugin für ChurchTools-Anmeldung

Mit diesem Wordpress-Plugin kannst du das von ChurchTools zur Verfügung gestellte iFrame für die Anmeldungen ersetzen durch einen eigenen Template-basierten Ansatz.

## Installation

1. Download Plugin in ZIP-Format: [wp-plugin-churchtools-anmeldungen.zip](https://github.com/5pm-HDH/wp-plugin-churchtools-anmeldungen/raw/main/wp-plugin-churchtools-anmeldungen.zip)
2. In Wordpress-Menü "Plugins" - Button "Installieren"
3. "Plugin hochladen" und ZIP-Datei hochladen

## Konfiguration des Plugins

### (1) ChurchTools Public-Group einrichten

1. In ChurchTools muss eine Public-Group angelegt werden.
2. Aus der Public-Group lässt sich ein Code zum einbetten abrufen:

![Screenshot-ChurchTools](./assets/churchtools-public-group.PNG)

Beispiel IFrame-Code:

```html
<iframe
        style="border-width:0" 
        data-src="https://kl4.church.tools/grouphomepage/5oIid23Slge5hii5dsycP87MmEqzVU5y?embedded=true" 
        class=" lazyloaded"
        src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" 
        width="100%" height="900px">
</iframe>
```

...aus dem Attribut `data-src` resultieren die Konfigurationswerte:

- **API-Url**: https://kl4.church.tools/ (Domain ohne Pfade)
- **Group Hash**: `5oIid23Slge5hii5dsycP87MmEqzVU5y` (Gruppen-Hash in Public-URL)

### (2) Konfiguration des WP-Plugin

Screenshot aus WordPress-Admin Bereich:

![Screenshot](./assets/screenshot.PNG)


## Dependencies

* [CT-Api Wrapper](https://github.com/5pm-HDH/churchtools-api) für den Datentransfer
* [Twig](https://twig.symfony.com/) als Template-Engine
* [Parsedown](https://github.com/erusev/parsedown) für das Parsen des Beschreibungstext (siehe Twig-Filter `{{ information.note|markdown }}`)
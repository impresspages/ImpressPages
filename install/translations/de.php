<?php


define('IP_PHP_VERSION', 'PHP version >= 5.3');
define('IP_MOD_REWRITE', 'Apache module "mod_rewrite"');
define('IP_GD_LIB', 'GD Graphics Library');
define('IP_MAGIC_QUOTES', 'Magic quotes (abgeschaltet Pflicht)');

define('IP_OK', 'Ja');
define('IP_ERROR', 'Nein');
define('IP_WRITABLE', 'beschreibbar');
define('IP_CHECK_AGAIN', 'Nochmals Überprüfen');
define('IP_BACK', 'Zurück');
define('IP_NEXT', 'Nächstes');
define('IP_ACCEPT', 'Akzeptieren');
define('IP_INSTALLATION', 'ImpressPages CMS Installations Wizard');
define('IP_VERSION', 'Version ' . TARGET_VERSION);
define('IP_SUBDIRECTORIES', '(Integriere Unterordner und Dateien)');
define('IP_OPTIONAL', '(Optional)');



define('IP_STEP_LANGUAGE', 'Sprache wählen');
define('IP_STEP_CHECK', 'System check');
define('IP_STEP_LICENSE', 'Lizens');
define('IP_STEP_DB', 'Datenbank');
define('IP_STEP_CONFIGURATION', 'Konfiguration');
define('IP_STEP_COMPLETED', 'Fertig');
define('IP_STEP_LANGUAGE_LONG', 'Wähle eine Oberflächen Sprache');
define('IP_STEP_CHECK_LONG', 'System check');
define('IP_STEP_LICENSE_LONG', 'ImpressPages Legal Notices');
define('IP_STEP_DB_LONG', 'Datenbank Installation');
define('IP_STEP_CONFIGURATION_LONG', 'System Konfiguration');
define('IP_STEP_COMPLETED_LONG', 'ImpressPages CMS ist erfolgreich Installiert worden.');





define('IP_DB_SERVER', 'Datenbank Host (eg. localhost)');
define('IP_DB_USER', 'Benutzername');
define('IP_DB_PASS', 'Passwort');
define('IP_DB_DB', 'Datenbank');
define('IP_DB_PREFIX', 'Tabellen Präfix (wenn nicht Standart, den Unterstrich nicht vergessen).');
define('IP_DB_DATA_WARNING', 'Warnung!!! Alle alten Tabelle mit der selben Präfix werden gelöscht!');
define('IP_DB_ERROR_ALL_FIELDS', 'Bitte die Felder ausfüllen');
define('IP_DB_ERROR_CONNECT', 'Kann nicht zu Datenbank verbinden');
define('IP_DB_ERROR_DB', 'Die angegeben Datenbank existiert nicht');
define('IP_DB_ERROR_QUERY', 'Unknown SQL error');
define('IP_DB_ERROR_LONG_PREFIX', 'Präfix darf nicht länger als 7 Zeichen sein');
define('IP_DB_ERROR_INCORRECT_PREFIX', 'Bei einer Präfix können keine Speziellen zeichen verwendet werden');
define('IP_DB_ERROR_EMAIL', 'Falsche E-mail Adresse'); 
define('IP_CONFIG_ERROR_CONFIG', 'Die "/ip_config.php" ist nicht beschreibar. Bitte auf chmod 0666 setzen.');
define('IP_CONFIG_ERROR_ROBOTS', 'Die "/robots.txt" ist nicht beschreibar. Bitte auf chmod 0666 setzen.');
define('IP_CONFIG_ERROR_EMAIL', 'Bitte gebt eine Emailadresse an');
define('IP_CONFIG_ERROR_SITE_NAME', 'Bitte gebt einen Webseiten namen ein.');
define('IP_CONFIG_ERROR_SITE_EMAIL', 'Bitte gebt eine richtige Email an.');
define('IP_CONFIG_ERROR_LOGIN', 'Hier den Admin Benutzername und Passwort festlegen.');
define('IP_CONFIG_ERROR_TIME_ZONE', 'Bitte eine Zeitzone auswählen.');


define('IP_CONFIG_SITE_NAME', 'Webseiten Name');
define('IP_CONFIG_SITE_EMAIL', 'webseiten E-mail adresse');
define('IP_CONFIG_EMAIL', 'E-mail für Fehlermeldungen (optional)');
define('IP_CONFIG_LOGIN', 'Administrator Benutzername');
define('IP_CONFIG_PASS', 'Administrator Passwort');
define('IP_CONFIG_TIMEZONE', 'Webseiten Zeitzone');
define('IP_CONFIG_SELECT_TIMEZONE', 'Bitte wähle eine Zeitzone');

define('IP_FINISH_MESSAGE', '
<p>Bitte lösche die Verzeichnisse "install", "update".<br /><br /></p>
<p>
<a href="../">Zur Seite</a>
</p>
<p>
<a href="../admin.php">Zum Admin Panel</a><br /><br />
</p>
<p>
Wenn du die Installation nocheinmal machen möchtest, dann leere die "ip_config.php".
</p>
');


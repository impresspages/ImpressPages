<?php


define('IP_PHP_VERSION', 'PHP versie >= 5.3');
define('IP_MOD_REWRITE', 'Apache module "mod_rewrite"');
define('IP_HTACCESS', '.htaccess file');
define('IP_MOD_PDO', 'PHP module "PDO"');
define('IP_GD_LIB', 'GD Graphics Library');
define('IP_CURL', 'PHP module "Curl" (optional)');
define('IP_SESSION', 'PHP sessions');
define('IP_MAGIC_QUOTES', 'Magic quotes off');
define('IP_INDEX_HTML', 'index.html removed');

define('IP_OK', 'Ja');
define('IP_ERROR', 'Nee');
define('IP_WRITABLE', 'overschrijfbaar');
define('IP_CHECK_AGAIN', 'Opnieuw controleren');
define('IP_BACK', 'Vorige');
define('IP_NEXT', 'Volgende');
define('IP_ACCEPT', 'Accepteren');
define('IP_INSTALLATION', 'ImpressPages CMS installatie wizard');
define('IP_VERSION', 'Versie '.TARGET_VERSION);
define('IP_SUBDIRECTORIES', '(inclusief submappen en bestanden)');
define('IP_OPTIONAL', '(optie)');



define('IP_STEP_LANGUAGE', 'Language selection');
define('IP_STEP_CHECK', 'Systeem controle');
define('IP_STEP_LICENSE', 'Licentie');
define('IP_STEP_DB', 'Database');
define('IP_STEP_CONFIGURATION', 'Configuratie');
define('IP_STEP_COMPLETED', 'Klaar');
define('IP_STEP_LANGUAGE_LONG', 'Choose interface language');
define('IP_STEP_CHECK_LONG', 'Systeem controleren');
define('IP_STEP_LICENSE_LONG', 'ImpressPages Juridische mededelingen');
define('IP_STEP_DB_LONG', 'Database installatie');
define('IP_STEP_CONFIGURATION_LONG', 'Systeem configuratie');
define('IP_STEP_COMPLETED_LONG', 'ImpressPages CMS succesvol geinstalleerd.');



define('IP_DB_SERVER', 'Database Host (bv. localhost)');
define('IP_DB_USER', 'Database gebruikersnaam');
define('IP_DB_PASS', 'Database wachtwoord');
define('IP_DB_DB', 'Database naam');
define('IP_DB_PREFIX', 'Tabel prefix (gebruik underscore om de prefix te scheiden).');
define('IP_DB_DATA_WARNING', 'Let Op!!! Alle oude tabellen met dezelfde prefix worden verwijderd!');
define('IP_DB_ERROR_ALL_FIELDS', 'Vul alle velden in a.u.b.');
define('IP_DB_ERROR_CONNECT', 'Kan geen contact met de Database maken');
define('IP_DB_ERROR_DB', 'De gespecificeerde database bestaat niet');
define('IP_DB_ERROR_QUERY', 'Onbekende SQL foutmelding');
define('IP_DB_ERROR_LONG_PREFIX', 'Prefix mag niet langer zijn dan 7 tekens');
define('IP_DB_ERROR_INCORRECT_PREFIX', 'Prefix can\'t contain any special characters and should sart with letter');
define('IP_DB_ERROR_EMAIL', 'Ongeldig e-mailadres');
define('IP_CONFIG_ERROR_CONFIG', 'Kan het volgende bestand niet overschrijven "/ip_config.php"');
define('IP_CONFIG_ERROR_ROBOTS', 'Kan het volgende bestand niet overschrijven "/robots.txt"');
define('IP_CONFIG_ERROR_EMAIL', 'Vul hier het correcte e-mailadres van de Administrator in');
define('IP_CONFIG_ERROR_SITE_NAME', 'Vul de naam van de website in.');
define('IP_CONFIG_ERROR_SITE_EMAIL', 'Vul het correcte e-mailadres van de website in.');
define('IP_CONFIG_ERROR_LOGIN', 'Vul de administrator gebruikersnaam en wachtwoord in.');
define('IP_CONFIG_ERROR_TIME_ZONE', 'Kies de tijdzone van de website.');


define('IP_CONFIG_SITE_NAME', 'Naam website');
define('IP_CONFIG_SITE_EMAIL', 'E-mailadres website');
define('IP_CONFIG_EMAIL', 'E-mailadres voor foutmeldingen (optie)');
define('IP_CONFIG_LOGIN', 'Administrator gebruikersnaam');
define('IP_CONFIG_PASS', 'Administrator wachtwoord');
define('IP_CONFIG_TIMEZONE', 'Tijdzone website');
define('IP_CONFIG_SELECT_TIMEZONE', 'Kies de tijdzone van de website');

define('IP_FINISH_MESSAGE', '
<p>
<a href="../">Voorpagina</a>
</p>
<p>
<a href="../admin.php">Administrator pagina</a><br /><br />
</p>
<p>
Als je de installatie opnieuw wilt uitvoeren, dan moet u het "ip_config.php" bestand wissen.
</p>
');


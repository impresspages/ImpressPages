<?php


define('IP_PHP_VERSION', 'PHP versija >= 5.3');
define('IP_MOD_REWRITE', 'Apache modulis "mod_rewrite"');
define('IP_GD_LIB', 'GD grafinė biblioteka');
define('IP_MAGIC_QUOTES', '"Magic quotes" rekomenduojama išjungti.');

define('IP_OK', 'Taip');
define('IP_ERROR', 'Ne');
define('IP_WRITABLE', 'įrašoma');
define('IP_CHECK_AGAIN', 'Tikrinti dar kartą');
define('IP_BACK', 'Atgal');
define('IP_NEXT', 'Toliau');
define('IP_ACCEPT', 'Sutinku');
define('IP_INSTALLATION', 'ImpressPages TVS diegimo vedlys');
define('IP_VERSION', 'Versija 1.0.13');
define('IP_SUBDIRECTORIES', '(įskaitant pakatalogius ir failus)');
define('IP_OPTIONAL', '(pasirinktinai)');



define('IP_STEP_LANGUAGE', 'Kalbos pasirinkimas');
define('IP_STEP_CHECK', 'Sistemos patikra');
define('IP_STEP_LICENSE', 'Licensija');
define('IP_STEP_DB', 'Duomenų bazė');
define('IP_STEP_CONFIGURATION', 'Konfigūracija');
define('IP_STEP_COMPLETED', 'Pabaiga');
define('IP_STEP_LANGUAGE_LONG', 'Diegimo vedlio kalbos pasirinkimas');
define('IP_STEP_CHECK_LONG', 'Sistemos patikra');
define('IP_STEP_LICENSE_LONG', 'ImpressPages licensija');
define('IP_STEP_DB_LONG', 'Duomenų bazės diegimas');
define('IP_STEP_CONFIGURATION_LONG', 'Sistemos konfigūracija');
define('IP_STEP_COMPLETED_LONG', 'ImpressPages TVS sėkmingai įdiegta.');



define('IP_DB_SERVER', 'Duomenų bazės serveris (pvz. localhost)');
define('IP_DB_USER', 'Vartotojo vardas');
define('IP_DB_PASS', 'Vartotojo slaptažodis');
define('IP_DB_DB', 'Duomenų bazė');
define('IP_DB_PREFIX', 'Lentelių prefiksas (naudokite apatinį brūkšnį gale).');
define('IP_DB_DATA_WARNING', 'Dėmesio!!! Visos senos lentelės su tuo pačiu prefiksu bus ištrintos!');
define('IP_DB_ERROR_ALL_FIELDS', 'Prašome užpildyti visus laukus');
define('IP_DB_ERROR_CONNECT', 'Nepavyko prisijungti prie duomenų bazės');
define('IP_DB_ERROR_DB', 'Nurodyta duomenų bazė neegzistuoja');
define('IP_DB_ERROR_QUERY', 'Nežinoma SQL klaida');
define('IP_DB_ERROR_LONG_PREFIX', 'Prefiksas negali būti ilgesnis, kaip 7 simboliai');
define('IP_DB_ERROR_INCORRECT_PREFIX', 'Prefikse negali būti specialilų ženklų. Prefiksas turi prasidėti raide');
define('IP_DB_ERROR_EMAIL', 'Neteisingas el. pašto adresas'); 
define('IP_CONFIG_ERROR_CONFIG', 'Nepavyko įrašyti konfigūracijos failo "/ip_config.php"');
define('IP_CONFIG_ERROR_ROBOTS', 'Nepavyko suformuoti failo "/robots.txt"');
define('IP_CONFIG_ERROR_EMAIL', 'Prašome įvesti teisingą administratoriaus el. pašto adresą');
define('IP_CONFIG_ERROR_SITE_NAME', 'Prašome įvesti interneto sveatinės pavadinimą.');
define('IP_CONFIG_ERROR_SITE_EMAIL', 'Prašome įvesti teisingą svetainės el. pašto adresą.');
define('IP_CONFIG_ERROR_LOGIN', 'Prašome įvesti administratoriaus prisijungimo vardą ir slaptažodį.');
define('IP_CONFIG_ERROR_TIME_ZONE', 'Pasirinkite svetainės laiko juostą.');


define('IP_CONFIG_SITE_NAME', 'Svetainės pavadinimas');
define('IP_CONFIG_SITE_EMAIL', 'Svetainės el. pašto adresas');
define('IP_CONFIG_EMAIL', 'El. paštas klaidų pranešimams (pasirinktinai)');
define('IP_CONFIG_LOGIN', 'Administratoriaus prisijungimo vardas');
define('IP_CONFIG_PASS', 'Administratoriaus slaptžodis');
define('IP_CONFIG_TIMEZONE', 'Svetainės laiko juosta');
define('IP_CONFIG_SELECT_TIMEZONE', 'Prašome pasirinkti svetainės laiko juostą');

define('IP_FINISH_MESSAGE', '
<p>Prašome ištrinti katalogus "install", "update".<br /><br /></p>
<p>
<a href="../">Svetainės pagrindinis puslapis</a>
</p>
<p>
<a href="../admin.php">Administravimo aplinka</a><br /><br />
</p>
<p>
Jei norite pakartoti diegimo procesą, prašome ištrinti konfigūracijos failo "ip_config.php" turinį.
</p>
');

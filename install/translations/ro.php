<?php


define('IP_PHP_VERSION', 'Versiune PHP >= 5.3');
define('IP_MOD_REWRITE', 'Modul Apache "mod_rewrite"');
define('IP_HTACCESS', '.htaccess file');
define('IP_MOD_PDO', 'PHP module "PDO"');
define('IP_GD_LIB', 'GD Graphics Library');
define('IP_MAGIC_QUOTES', 'Citat magic oprit');
define('IP_INDEX_HTML', 'index.html removed');

define('IP_OK', 'Da');
define('IP_ERROR', 'Nu');
define('IP_WRITABLE', 'modificabil');
define('IP_CHECK_AGAIN', 'Verifica din nou');
define('IP_BACK', 'Inapoi');
define('IP_NEXT', 'Inainte');
define('IP_ACCEPT', 'Acceptare');
define('IP_INSTALLATION', 'ImpressPages CMS mod de instalare');
define('IP_VERSION', 'Versiune '.TARGET_VERSION);
define('IP_SUBDIRECTORIES', '(include dosare si subdosare)');
define('IP_OPTIONAL', '(optional)');



define('IP_STEP_LANGUAGE', 'Selectie limba');
define('IP_STEP_CHECK', 'Verificare sistem');
define('IP_STEP_LICENSE', 'Licienta');
define('IP_STEP_DB', 'Baza de date');
define('IP_STEP_CONFIGURATION', 'Configurare');
define('IP_STEP_COMPLETED', 'Finalizare');
define('IP_STEP_LANGUAGE_LONG', 'Alege limba interfatei');
define('IP_STEP_CHECK_LONG', 'Verificare sistem');
define('IP_STEP_LICENSE_LONG', 'ImpressPages Note Legale');
define('IP_STEP_DB_LONG', 'Instalare baza de date');
define('IP_STEP_CONFIGURATION_LONG', 'Configuratie sistem');
define('IP_STEP_COMPLETED_LONG', 'ImpressPages CMS sa instalat cu succes.');





define('IP_DB_SERVER', 'Host (ex. localhost)');
define('IP_DB_USER', 'Nume utilizator');
define('IP_DB_PASS', 'Parola utilizator');
define('IP_DB_DB', 'Baza de date');
define('IP_DB_PREFIX', 'Prefix tabel (foloseste underscore pentru separarea prefixului).');
define('IP_DB_DATA_WARNING', 'Atentie!!! toate tabelele vechi cu acelasi prefix vor fi sterse!');
define('IP_DB_ERROR_ALL_FIELDS', 'Te rugam sa completezi toate campurile');
define('IP_DB_ERROR_CONNECT', 'Nu se face conexiunea la baza de date');
define('IP_DB_ERROR_DB', 'Baza de date specificata nu exista');
define('IP_DB_ERROR_QUERY', 'Eroare SQL necunoscuta');
define('IP_DB_ERROR_LONG_PREFIX', 'Prefixul nu poate fi mai lung decat 7 caractere');
define('IP_DB_ERROR_INCORRECT_PREFIX', 'Prefixul nu poate contine caractere speciale si va trebui sa inceapa cu o litera');
define('IP_DB_ERROR_EMAIL', 'Adresa e-mail incorecta');
define('IP_CONFIG_ERROR_CONFIG', 'Nu pot scrie configuratia "/ip_config.php"');
define('IP_CONFIG_ERROR_ROBOTS', 'Nu pot scrie "/robots.txt"');
define('IP_CONFIG_ERROR_EMAIL', 'Te rugam sa introduci adresa corecta de email a administratorului.');
define('IP_CONFIG_ERROR_SITE_NAME', 'Te rugam sa introduci numele site-ului.');
define('IP_CONFIG_ERROR_SITE_EMAIL', 'Te rugam sa introduci o adresa de e-mail corecta.');
define('IP_CONFIG_ERROR_LOGIN', 'Te rugam sa introduci numele si parola de administrare.');
define('IP_CONFIG_ERROR_TIME_ZONE', 'Te rugam sa alegi timpul si zona pentru site.');


define('IP_CONFIG_SITE_NAME', 'Numele Site-ului');
define('IP_CONFIG_SITE_EMAIL', 'Adresa e-mail a site-ului');
define('IP_CONFIG_EMAIL', 'E-mail pentru raspunsurile de erori (optional)');
define('IP_CONFIG_LOGIN', 'Audentificare Administrator');
define('IP_CONFIG_PASS', 'Parola Administrator');
define('IP_CONFIG_TIMEZONE', 'Timpul si zona site-ului');
define('IP_CONFIG_SELECT_TIMEZONE', 'Te rugam sa alegi timpul si zona pentru site');

define('IP_FINISH_MESSAGE', '
<p>
<a href="../">Prima pagina</a>
</p>
<p>
<a href="../admin.php">Pagina de administrare</a><br /><br />
</p>
<p>
Daca vrei sa repeti instalarea, te rugam sa stergi continutul fisierului "ip_config.php".
</p>
');

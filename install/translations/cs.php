<?php


define('IP_PHP_VERSION', 'Verze PHP >= 5.3');
define('IP_MOD_REWRITE', 'Apache modul "mod_rewrite"');
define('IP_HTACCESS', '.htaccess file');
define('IP_MOD_PDO', 'PHP module "PDO"');
define('IP_GD_LIB', 'Grafická knihovna GD');
define('IP_MAGIC_QUOTES', 'Kouzelné citace (mimo doporučené)');

define('IP_OK', 'Ano');
define('IP_ERROR', 'Ne');
define('IP_WRITABLE', 'zapisovatelný');
define('IP_CHECK_AGAIN', 'Znovu zkontrolujte');
define('IP_BACK', 'zpět');
define('IP_NEXT', 'další');
define('IP_ACCEPT', 'Přijmout');
define('IP_INSTALLATION', 'Průvodce instalací CMS ImpressPages');
define('IP_VERSION', 'Version '.TARGET_VERSION);
define('IP_SUBDIRECTORIES', '(včetně podsložek a souborů)');
define('IP_OPTIONAL', '(volitelné)');



define('IP_STEP_LANGUAGE', 'Výběr jazyka');
define('IP_STEP_CHECK', 'Kontrola systému');
define('IP_STEP_LICENSE', 'Licence');
define('IP_STEP_DB', 'Databáze');
define('IP_STEP_CONFIGURATION', 'Konfigurace');
define('IP_STEP_COMPLETED', 'Hotovo');
define('IP_STEP_LANGUAGE_LONG', 'Vyberte si jazykové rozhraní');
define('IP_STEP_CHECK_LONG', 'Kontrola systému');
define('IP_STEP_LICENSE_LONG', 'Právní podmínky ImpressPages');
define('IP_STEP_DB_LONG', 'Instalace databáze');
define('IP_STEP_CONFIGURATION_LONG', 'Konfigurace systému');
define('IP_STEP_COMPLETED_LONG', 'CMS ImpressPages je úspěšně nainstalován.');





define('IP_DB_SERVER', 'Hostitel databáze (např. localhost)');
define('IP_DB_USER', 'Jméno uživatele');
define('IP_DB_PASS', 'Heslo uživatele');
define('IP_DB_DB', 'Databáze');
define('IP_DB_PREFIX', 'Prefix tabulek (použijte podtržítko pro oddělení prefixu).');
define('IP_DB_DATA_WARNING', 'Varování!!! Všechny tabulky se stejným prefixem budou smazány!');
define('IP_DB_ERROR_ALL_FIELDS', 'Vyplňte prosím všechna pole');
define('IP_DB_ERROR_CONNECT', 'Nelze se připojit k databázi');
define('IP_DB_ERROR_DB', 'Zadaná databáze neexistuje');
define('IP_DB_ERROR_QUERY', 'Neznámá chyba SQL');
define('IP_DB_ERROR_LONG_PREFIX', 'Prefix nesmí být delší než 7 znaků');
define('IP_DB_ERROR_INCORRECT_PREFIX', 'Prefix nesmí obsahovat žádné speciální znaky a měl by začínat písmenem');
define('IP_DB_ERROR_EMAIL', 'Nesprávná adresa e-mailu'); 
define('IP_CONFIG_ERROR_CONFIG', 'Nelze zapisovat do "/ip_config.php"');
define('IP_CONFIG_ERROR_ROBOTS', 'Nelze zapisovat do "/robots.txt"');
define('IP_CONFIG_ERROR_EMAIL', 'Zadejte prosím správnou adresu e-mailu administrátora');
define('IP_CONFIG_ERROR_SITE_NAME', 'Zadejte prosím název webové stránky.');
define('IP_CONFIG_ERROR_SITE_EMAIL', 'Prosím, zadejte správný e-mail internetové stránky.');
define('IP_CONFIG_ERROR_LOGIN', 'Zadejte přihlašovací jméno a heslo administrátora.');
define('IP_CONFIG_ERROR_TIME_ZONE', 'Zvolte prosím časové pásmo.');


define('IP_CONFIG_SITE_NAME', 'Název webových stránek');
define('IP_CONFIG_SITE_EMAIL', 'E-mailová addresa webových stránek');
define('IP_CONFIG_EMAIL', 'E-mail pro zasílání chybových stavů (volitelně)');
define('IP_CONFIG_LOGIN', 'Přihlašovací jméno administrátora');
define('IP_CONFIG_PASS', 'Heslo administrátora');
define('IP_CONFIG_TIMEZONE', 'Časové pásmo stránek');
define('IP_CONFIG_SELECT_TIMEZONE', 'Zvolte prosím časové pásmo');

define('IP_FINISH_MESSAGE', '
<p>
<a href="../">Titulní stránka</a>
</p>
<p>
<a href="../admin.php">Administrace</a><br /><br />
</p>
<p>
Pokud chcete opakovat instalaci, zrušte prosím konfigurační soubor "ip_config.php".
</p>
');



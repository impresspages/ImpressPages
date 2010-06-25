<?php


define('IP_PHP_VERSION', 'Wersja PHP >= 5.3');
define('IP_MOD_REWRITE', 'Modul Apache "mod_rewrite"');

define('IP_OK', 'Tak');
define('IP_ERROR', 'Nie');
define('IP_WRITABLE', 'zapisywalne');
define('IP_CHECK_AGAIN', 'Sprawdź ponownie');
define('IP_BACK', 'Wróć');
define('IP_NEXT', 'Dalej');
define('IP_ACCEPT', 'Akceptuj');
define('IP_INSTALLATION', 'Instalator ImpressPages CMS');
define('IP_VERSION', 'Wersja 1.0.5');
define('IP_SUBDIRECTORIES', '(włączając podfoldery i pliki)');
define('IP_OPTIONAL', '(opcjonalnie)');



define('IP_STEP_LANGUAGE', 'Wybór języka');
define('IP_STEP_CHECK', 'Sprawdzenie systemu');
define('IP_STEP_LICENSE', 'Licencja');
define('IP_STEP_DB', 'Baza danych');
define('IP_STEP_CONFIGURATION', 'Konfiguracja');
define('IP_STEP_COMPLETED', 'Koniec');
define('IP_STEP_LANGUAGE_LONG', 'Wybierz język instalatora');
define('IP_STEP_CHECK_LONG', 'Sprawdzanie systemu');
define('IP_STEP_LICENSE_LONG', 'Warunki użytkowania ImpressPages');
define('IP_STEP_DB_LONG', 'Instalacja bazy danych');
define('IP_STEP_CONFIGURATION_LONG', 'Konfiguracja systemu');
define('IP_STEP_COMPLETED_LONG', 'ImpressPages CMS zainstalowano pomyślnie.');





define('IP_DB_SERVER', 'Serwer bazy danych (np. localhost)');
define('IP_DB_USER', 'Nazwa użytkownika');
define('IP_DB_PASS', 'Hasło');
define('IP_DB_DB', 'Baza danych');
define('IP_DB_PREFIX', 'Przedrostki tabeli (użyj podkreślenia do rozbudowy).');
define('IP_DB_DATA_WARNING', 'Uwaga!!! Wszystkie tabele z tym samym przedrostkiem zostaną usunięte!');
define('IP_DB_ERROR_ALL_FIELDS', 'Proszę wypełnić wszystkie pola');
define('IP_DB_ERROR_CONNECT', 'Brak połączenia z bazą danych!');
define('IP_DB_ERROR_DB', 'Podana baza danych nie istnieje');
define('IP_DB_ERROR_QUERY', 'Nierozpoznany błąd MySQL');
define('IP_DB_ERROR_LONG_PREFIX', 'Przedrostek nie może być dłuższy niż 7 znaków');
define('IP_DB_ERROR_INCORRECT_PREFIX', 'Przedrostek nie może zawierać żadnych znaków specjalnych i powinien rozpoczynać się literą');
define('IP_DB_ERROR_EMAIL', 'Błędny adres e-mail'); 
define('IP_CONFIG_ERROR_CONFIG', 'Nie można zapisać konfiguracji do pliku "/ip_config.php"');
define('IP_CONFIG_ERROR_ROBOTS', 'Nie można zapisać pliku "/robots.txt"');
define('IP_CONFIG_ERROR_EMAIL', 'Proszę wprowadź poprawny adres e-mail administratora');
define('IP_CONFIG_ERROR_SITE_NAME', 'Proszę wprowadź poprawny adres strony.');
define('IP_CONFIG_ERROR_SITE_EMAIL', 'Proszę wprowadź poprawny adres e-mail.');
define('IP_CONFIG_ERROR_LOGIN', 'Proszę wprowadź login i hasło administratora.');
define('IP_CONFIG_ERROR_TIME_ZONE', 'Proszę wybierz strefę czasową.');


define('IP_CONFIG_SITE_NAME', 'Nazwa strony');
define('IP_CONFIG_SITE_EMAIL', 'Adres e-mail właściciela');
define('IP_CONFIG_EMAIL', 'Adres e-mail do raportowania błędów (opcja, wypełnij tylko jeśli chcesz otrzymywać powiadomienia)');
define('IP_CONFIG_LOGIN', 'Login');
define('IP_CONFIG_PASS', 'Hasło');
define('IP_CONFIG_TIMEZONE', 'Strefa czasowa');
define('IP_CONFIG_SELECT_TIMEZONE', 'Proszę wybierz strefę czasową z poniższej listy');

define('IP_FINISH_MESSAGE', '
<p>Instalacja zakończona. Skasuj katalogi "install", "update" oraz zmień uprawnienia zapisu (chmod 644) dla następujących plików:</p>
<p>
/ip_config.php<br/>
/robots.txt
</p>
<p>
<a href="../">Strona główna</a>
</p>
<p>
<a href="../admin.php">Administracja</a>
</p>
<p>
Jeśli chcesz powtórzyć proces instalacji, usuń dane konfiguracyjne z pliku "ip_config.php" lub nadpisz go plikiem z pakietu instalacyjnego.
</p>
');


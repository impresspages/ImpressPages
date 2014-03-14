<?php

/**
 * @package ImpressPages
 *
 *
 */

require_once __DIR__ . '/config.php';

date_default_timezone_set('Europe/Vilnius'); //PHP 5 requires timezone to be set.

if (!session_name()) {
    session_start('ipUpdateTest');
}

require_once(TEST_BASE_DIR.'/Autoloader.php');
$autoloader = new \PhpUnit\Autoloader();
$autoloader->register(TEST_BASE_DIR);

require_once TEST_CODEBASE_DIR . 'Ip/Config.php';


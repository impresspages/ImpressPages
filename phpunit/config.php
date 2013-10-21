<?php

$isTravis = getenv('TRAVIS') ? true : false;


define ('TEST_BASE_DIR', __DIR__.'/');
define ('TEST_CODEBASE_DIR', '../');

define ('TEST_FIXTURE_DIR', 'Fixture/');
define ('TEST_TMP_DIR', 'tmp/');


define ('TEST_TMP_URL', $isTravis ? 'http://localhost/phpunit/tmp/' : 'http://localhost/ip3.x/phpunit/tmp/');
define ('TEST_UNWRITABLE_DIR', '/var/tmp/testDir'); //root owned empty dir which can't be writable by Apache process and can't be chmoded. Used by making symbolic links and emulating unwritable dirs in such way.

define ('TEST_DB_HOST', 'localhost');
define ('TEST_DB_USER', $isTravis ? 'travis'    : 'test');
define ('TEST_DB_PASS', $isTravis ? ''          : 'test');
define ('TEST_DB_NAME', 'ip_test');

define ('TEST_CAPTURE_SCREENSHOT_ON_FAILURE', true);
define ('TEST_SCREENSHOT_PATH', __DIR__ . '/screenshots/');
define ('TEST_SCREENSHOT_URL', $isTravis ? 'http://localhost/phpunit/screenshots/' : 'http://localhost/ip3.x/phpunit/screenshots/');

define('RECENT_VERSION', '3.6');
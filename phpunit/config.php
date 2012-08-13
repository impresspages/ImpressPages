<?php 


define ('TEST_BASE_DIR', __DIR__.'/');
define ('CODEBASE_DIR', __DIR__.'/../');

define ('TEST_FIXTURE_DIR', TEST_BASE_DIR.'Fixture/');
define ('TEST_TMP_DIR', TEST_BASE_DIR.'Tmp/');
define ('TEST_TMP_URL', 'http://localhost/phpunit/Tmp/');
define ('TEST_UNWRITABLE_DIR', '/var/tmp/testDir'); //root owned empty dir which can't be writable by Apache process and can't be chmoded. Used by making symbolic links and emulating unwritable dirs in such way.

define ('TEST_DB_HOST', 'localhost');
define ('TEST_DB_USER', 'test');
define ('TEST_DB_PASS', 'test');


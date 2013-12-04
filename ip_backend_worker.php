<?php

if (!defined('BACKEND')) {
    define('BACKEND', true); // make sure other files are accessed through this file.
}
if (!defined('WORKER')) {
    define('WORKER', true); //worker don't show errors. Even if it is set to show them in config.php
}

include __DIR__ . '/index.php';
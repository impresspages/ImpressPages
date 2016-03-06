<?php

require_once dirname(__DIR__) . '/Application.php';

try {
    $application = new \Ip\Application($configFilename);
    $application->init();
    $application->run();
} catch (\Exception $e) {
    if (isset($log)) {
        $log->log('System', 'Exception caught', $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    }
    throw $e;
}


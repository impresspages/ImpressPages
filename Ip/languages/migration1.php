<?php

$parameter = array();

include __DIR__ . '/publicTranslations.php';

$contents = "<?" . "php \n";
$contents.= "return " . var_export($parameter, true) . ";";

file_put_contents(__DIR__ . '/ipPublic-source.php', $contents);
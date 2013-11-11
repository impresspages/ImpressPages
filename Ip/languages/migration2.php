<?php

// TODOX remove this file

$translations = include __DIR__ . '/ipAdmin-source.php';

$contents = "<?" . "php \n";
$contents.= "return " . var_export($parameter, true) . ";";

file_put_contents(__DIR__ . '/ipPublic-source.php', $contents);
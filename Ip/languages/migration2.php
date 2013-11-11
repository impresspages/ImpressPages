<?php

// TODO:
// - užregistruoti ipAdmin domeną
// - replace parameter mod calls to __()
// - replace keys in ipAdmin with values
// - replace __() keys with values

$translations = include __DIR__ . '/ipAdmin-source.php';

$contents = "<?" . "php \n";
$contents.= "return " . var_export($parameter, true) . ";";

file_put_contents(__DIR__ . '/ipPublic-source.php', $contents);
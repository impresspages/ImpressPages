<?php

// TODO:
// - užregistruoti ipAdmin domeną
// - replace parameter mod calls to __()
// - replace keys in ipAdmin with values
// - replace __() keys with values

$translations = include __DIR__ . '/ipPublic-source.php';

$en = array();

foreach ($translations as $key => $value) {
    $en[$value] = $value;
}

$contents = "<?" . "php \n";
$contents.= "return " . var_export($en, true) . ";";

file_put_contents(__DIR__ . '/ipPublic-en.php', $contents);
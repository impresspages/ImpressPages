<?php

// TODOX remove this file

$translations = include __DIR__ . '/ipPublic-source.php';

$en = array();

foreach ($translations as $key => $value) {
    $en[$value] = $value;
}

$contents = "<?" . "php \n";
$contents.= "return " . var_export($en, true) . ";";

file_put_contents(__DIR__ . '/ipPublic-en.php', $contents);
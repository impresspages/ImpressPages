<?php

// TODOX (before release) remove this file

$translations = include __DIR__ . '/migrate-en.php';

// Override replaces:
$translations['IP_FINISH_MESSAGE'] = 'FINISH_MESSAGE';
$translations['IP_DB_DB'] = 'DATABASE_NAME';
$translations['IP_STEP_CHECK_LONG'] = 'SYSTEM_CHECK_LONG';
$translations["'Yes'"] = "'Yes'";
$translations["'No'"] = "'No'";
$translations["'Warning'"] = "'Warning'";

// Remove already translated strings:
foreach ($translations as $id => $translation)
{
    if ($id == $translation) {
        unset($translations[$id]);
    }
}

// Add slashes:
foreach ($translations as $id => $translation)
{
    $translations[$id] = "__('" . addslashes($translation) . "', 'plugin-Install')";
}

$files = glob(__DIR__ . '/view/*.php');
//$files = array('Model.php');

foreach ($files as $file) {
    $contents = file_get_contents($file);
    $contents = strtr($contents, $translations);
    file_put_contents($file, $contents);
}
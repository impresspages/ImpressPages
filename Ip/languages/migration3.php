<?php

// TODOX remove this file

$translations = include __DIR__ . '/ipAdmin-source.php';

$path = '/var/www/ip4.x/Ip/Module';

$directoryIterator = new RecursiveDirectoryIterator($path);
$recursiveIterator = new RecursiveIteratorIterator($directoryIterator);
$list = new RegexIterator($recursiveIterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

$files = array();
foreach ($list as $file) {
    $files[] = $file[0];
}

echo "Files: " . count($files) . "\n\n";

$found = 0;

foreach ($files as $filename) {
    $contents = file_get_contents($filename);

    $replace = preg_replace_callback(
        '/\\$this->escPar\\(\'([^\']+)\'\\)/',
        function ($matches) {
            global $translations, $found;

            if (array_key_exists($matches[1], $translations)) {
                $found++;

                return "__('" . addslashes($translations[$matches[1]]) . "', 'ipAdmin')";
            }

            return $matches[0];
        },
        $contents
    );

    if ($replace != $contents) {
        file_put_contents($filename, $replace);
    }
}

echo "Found: {$found}\n";
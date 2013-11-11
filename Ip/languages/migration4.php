<?php

$translations = include __DIR__ . '/ipPublic-source.php';

$path = '/var/www/ip4.x';

$directoryIterator = new RecursiveDirectoryIterator($path);
$recursiveIterator = new RecursiveIteratorIterator($directoryIterator);
$list = new RegexIterator($recursiveIterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

$files = array();
foreach ($list as $file) {
    if (strpos($file[0], '/var/www/ip4.x/phpunit/vendor') === false) {
        $files[] = $file[0];
    }
}

echo "Files: " . count($files) . "\n\n";

$found = 0;

foreach ($files as $filename) {
    $contents = file_get_contents($filename);

    $replace = preg_replace_callback(
        '/\\$parametersMod->getValue\\(\'([^\']+)\'/',
        function ($matches) {
            global $translations, $found, $filename;

            if (array_key_exists($matches[1], $translations)) {
                $found++;

                echo $filename . " : " . $matches[1] . "\n";

                return "__('" . addslashes($translations[$matches[1]]) . "', 'ipAdmin')";
            }

            return $matches[0];
        },
        $contents
    );

    if ($replace != $contents) {
        // file_put_contents($filename, $replace);
    }
}

echo "Found: {$found}\n";
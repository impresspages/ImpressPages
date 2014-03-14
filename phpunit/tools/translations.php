<?php

/**
 * requires PHP 5.4, gettext, nodejs and npm install -g po2json
 *
 * Should be run from project root directory.
 */

$rootDir = dirname(dirname(__DIR__));

$dirs = array(
    'Ip',
    'Plugin',
    'Theme',
    'install'
);

chdir($rootDir);

if (file_exists("$rootDir/phpunit/tools/tmp_gettext_files.txt")) {
    unlink("$rootDir/phpunit/tools/tmp_gettext_files.txt");
}

foreach ($dirs as $dir) {
    `find ./$dir ! -type d -iname "*.php" >> {$rootDir}/phpunit/tools/tmp_gettext_files.txt`;
}

`xgettext -f {$rootDir}/phpunit/tools/tmp_gettext_files.txt -L PHP --from-code=utf-8 --keyword=__:1,2c --keyword=_e:1,2c -o {$rootDir}/phpunit/tools/all.po --omit-header`;
`rm {$rootDir}/phpunit/tools/tmp_gettext_files.txt`;

chdir($rootDir . '/phpunit/tools');

`po2json all.po all.json`;
`rm all.po`;

$all = json_decode(file_get_contents(__DIR__ . '/all.json'), true);

$t = array();

$unicodeChar = '\u0004';
$delimiter = json_decode('"' . $unicodeChar . '"');

$domains = array();

foreach ($all as $key => $values) {

    $parts = explode($delimiter, $key, 2);
    if (count($parts) != 2) {
        echo "error encountered on key: $key";
        exit();
    }

    list($domain, $id) = $parts;

    $domains[$domain][$id] = $id;
}

foreach (array('ipPublic', 'ipAdmin') as $domain) {
    $contents = file_get_contents($rootDir . '/Ip/Internal/Translations/translations/' . $domain . '-en.json');
    $messages = json_decode($contents, true);

    $domains[$domain] = array_merge($domains[$domain], $messages);
}

`rm all.json`;

$destinations = array(
    'ipAdmin' => 'Ip/Internal/Translations/translations/',
    'ipPublic' => 'Ip/Internal/Translations/translations/',
    'Install' => 'install/Plugin/Install/translations/',
    'Air' => 'Theme/Air/translations/',
);

foreach ($domains as $domain => $messageList) {
    $destinationDir = isset($destinations[$domain]) ? $rootDir . '/' .$destinations[$domain] : __DIR__ . '/';
    file_put_contents($destinationDir . $domain . '-en.json', json_encode($messageList, JSON_PRETTY_PRINT)); // JSON_PRETTY_PRINT is only for PHP 5.4 and above
}

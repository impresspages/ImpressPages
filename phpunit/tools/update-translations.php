<?php

$username = $argv[1];
$password = $argv[2];

$installTranslationsDir = dirname(dirname(__DIR__)) . '/install/Plugin/Install/translations';

$files = glob("$installTranslationsDir/*.json");

$installTranslations = array();

foreach ($files as $filename) {
    if (preg_match('%-([a-z]{2})[.]json%', $filename, $matches)) {
        if ($matches[1] != 'en') {
            $installTranslations[] = $matches[1];
        }
    }
}

$context = stream_context_create(array(
        'http' => array(
            'header'  => "Authorization: Basic " . base64_encode("$username:$password")
        )
    ));

$aliases = array(
   'cn' => 'zh_CN',
);

foreach ($installTranslations as $language) {

    if (isset($aliases[$language])) {
        $language = $aliases[$language];
    }

    $url = "http://www.transifex.com/api/2/project/impresspages/resource/Install/translation/{$language}/";

    $content = file_get_contents($url, false, $context);

    $json = json_decode($content, true);
    $data = json_decode($json['content'], true);

    $json = json_encode($data, JSON_PRETTY_PRINT);

    file_put_contents("$installTranslationsDir/Install-$language.json", $json);
}




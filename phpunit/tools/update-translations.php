<?php

$username = $argv[1];
$password = $argv[2];

function updateDirTranslations($dir, $transifexSourceKey) {
    global $username;
    global $password;
    $translationsDir = dirname(dirname(__DIR__)) . '/' . $dir;

    $files = glob("$translationsDir/*.json");

    $allTranslations = array();

    foreach ($files as $filename) {
        if (preg_match('%-([a-z]{2})[.]json%', $filename, $matches)) {
            if ($matches[1] != 'en') {
                $allTranslations[] = $matches[1];
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

    foreach ($allTranslations as $language) {

        $transifexLanguage = isset($aliases[$language]) ? $aliases[$language] : $language;

        $url = "http://www.transifex.com/api/2/project/impresspages/resource/".$transifexSourceKey."/translation/{$transifexLanguage}/";

        $content = file_get_contents($url, false, $context);

        $json = json_decode($content, true);
        $data = json_decode($json['content'], true);

        $json = json_encode($data, JSON_PRETTY_PRINT);

        file_put_contents("$translationsDir/".$transifexSourceKey."-$language.json", $json);
    }

}



updateDirTranslations('install/Plugin/Install/translations', 'Install');


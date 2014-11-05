<?php

$username = $argv[1];
$password = $argv[2];

function updateDirTranslations($dir, $transifexSourceKey, $aliases = array()) {
    global $username;
    global $password;
    $translationsDir = dirname(dirname(__DIR__)) . '/' . $dir;

    $files = glob("$translationsDir/".$transifexSourceKey."-??.json");
    $files = array_merge($files, glob("$translationsDir/".$transifexSourceKey."-??_??.json"));

    $allTranslations = array();

    foreach ($files as $filename) {
        if (preg_match('%-([a-z]{2})[.]json%', $filename, $matches)) {
            if ($matches[1] != 'en') {
                $allTranslations[] = $matches[1];
            }
        }
        if (preg_match('%-([a-z]{2})_([A-Z]{2})[.]json%', $filename, $matches)) {
            $allTranslations[] = $matches[1].'_'.$matches[2];
        }
    }

    $context = stream_context_create(array(
            'http' => array(
                'header'  => "Authorization: Basic " . base64_encode("$username:$password")
            )
        ));



    foreach ($allTranslations as $language) {

        $transifexLanguage = isset($aliases[$language]) ? $aliases[$language] : $language;

        $url = "http://www.transifex.com/api/2/project/impresspages/resource/".$transifexSourceKey."/translation/{$transifexLanguage}/";

        $content = file_get_contents($url, false, $context);

        $json = json_decode($content, true);
        $data = json_decode($json['content'], true);

        $data = array_map('utf8_encode', $data);

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);


        file_put_contents("$translationsDir/".$transifexSourceKey."-$language.json", $json);
    }

}


$installAliases = array(
    'cn' => 'zh_CN',
);

updateDirTranslations('install/Plugin/Install/translations', 'Install', $installAliases);
updateDirTranslations('Ip/Internal/Translations/translations', 'Ip-admin');


$ipAliases = array(
    'pt' => 'pt_BR',
    'fa' => 'fa_IR',
    'zh' => 'zh_CN',
);

updateDirTranslations('Ip/Internal/Translations/translations', 'Ip', $ipAliases);


<?php


$publicDir = 'public';

if (!empty($argv[1])) {
    $publicDir = $argv[1];
}



if (getcwd() . '/vendor/impresspages/impresspages/bin' != __DIR__) {
    throw new \Exception('This script must be executed from the project root (where composer.json is placed');
}

`rm -rf $publicDir/Ip/*`;

`mkdir -p $publicDir/Ip`;
`mkdir -p $publicDir/Ip/Internal`;

$assetPaths = glob(dirname(__DIR__) . '/Ip/Internal/*/assets/');
foreach ($assetPaths as $assetPath) {
    $module = basename(dirname($assetPath));
    `mkdir -p $publicDir/Ip/Internal/$module/`;
    `cp -rf $assetPath $publicDir/Ip/Internal/$module`;
}

$assetPaths = glob(dirname(__DIR__) . '/Ip/Internal/Content/Widget/*/assets/');
foreach ($assetPaths as $assetPath) {
    $widget = basename(dirname($assetPath));
    `mkdir -p $publicDir/Ip/Internal/Content/Widget/$widget/`;
    `cp -rf $assetPath $publicDir/Ip/Internal/Content/Widget/$widget`;
}




if (!is_dir($publicDir . '/Plugin')) {
    `cp -rf vendor/impresspages/impresspages/start-pack/Plugin public/`;
}

if (!is_dir($publicDir . '/Theme')) {
    `cp -rf vendor/impresspages/impresspages/start-pack/Theme public/`;
}

if (!is_dir($publicDir . '/file')) {
    `cp -rf vendor/impresspages/impresspages/start-pack/file public/`;
}

//user could have removed admin.php or favicon.ico. So we do our work only if all three are missing.
if (
    !is_file($publicDir . '/admin.php') &&
    !is_file($publicDir . '/favicon.ico') &&
    !is_file($publicDir . '/index.php') &&
    !is_file($publicDir . '/.htaccess')
) {
    `cp -rf vendor/impresspages/impresspages/start-pack/admin.php $publicDir/`;
    `cp -rf vendor/impresspages/impresspages/start-pack/favicon.ico $publicDir/`;
    `cp -rf vendor/impresspages/impresspages/start-pack/index.php $publicDir/`;
    `cp -rf vendor/impresspages/impresspages/start-pack/.htaccess $publicDir/`;
}


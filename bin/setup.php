<?php






if (getcwd() . '/vendor/impresspages/coreexperimental/bin' != __DIR__) {
    throw new \Exception('This script must be executed from the project root (where composer.json is placed');
}

`rm -rf public/Ip/*`;

`mkdir -p public/Ip`;
`mkdir -p public/Ip/Internal`;

$assetPaths = glob(dirname(__DIR__) . '/Ip/Internal/*/assets/');
foreach ($assetPaths as $assetPath) {
    $module = basename(dirname($assetPath));
    `mkdir -p public/Ip/Internal/$module/`;
    `cp -rf $assetPath public/Ip/Internal/$module`;
}

$assetPaths = glob(dirname(__DIR__) . '/Ip/Internal/Content/Widget/*/assets/');
foreach ($assetPaths as $assetPath) {
    $widget = basename(dirname($assetPath));
    `mkdir -p public/Ip/Internal/Content/Widget/$widget/`;
    `cp -rf $assetPath public/Ip/Internal/Content/Widget/$widget`;
}




if (!is_dir('public/Plugin')) {
    `cp -rf vendor/impresspages/coreexperimental/start-pack/Plugin public/`;
}

if (!is_dir('public/Theme')) {
    `cp -rf vendor/impresspages/coreexperimental/start-pack/Theme public/`;
}

if (!is_dir('public/file')) {
    `cp -rf vendor/impresspages/coreexperimental/start-pack/file public/`;
}

//user could have removed admin.php or favicon.ico. So we do our work only if all three are missing.
if (
    !is_file('public/admin.php') &&
    !is_file('public/favicon.ico') &&
    !is_file('public/index.php') &&
    !is_file('public/.htaccess')
) {
    `cp -rf vendor/impresspages/coreexperimental/start-pack/admin.php public/`;
    `cp -rf vendor/impresspages/coreexperimental/start-pack/favicon.ico public/`;
    `cp -rf vendor/impresspages/coreexperimental/start-pack/index.php public/`;
    `cp -rf vendor/impresspages/coreexperimental/start-pack/.htaccess public/`;
}


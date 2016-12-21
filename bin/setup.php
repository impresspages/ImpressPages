<?php


$publicDir = 'public';

if (!empty($argv[1])) {
    $publicDir = $argv[1];
}

if (getcwd() . '/vendor/impresspages/impresspages/bin' != __DIR__) {
    throw new \Exception('This script must be executed from the project root (where composer.json is placed');
}

createMainDirs($publicDir);

copyAssets($publicDir);

createRootFiles($publicDir);

registerComposerInstalledPlugins();

function registerComposerInstalledPlugins()
{
    $vendorDir = dirname(dirname(dirname(__DIR__)));
    $baseDir = dirname($vendorDir);
    $composerPluginsRegisterFile = $baseDir . '/composerPlugins.php';

    $autoLoaderPaths = require($vendorDir . '/composer/autoload_psr4.php');
    $composerPlugins = [];
    foreach ($autoLoaderPaths as $key => $autoLoaderPath) {
        $keyParts = explode('\\', $key);
        if ($keyParts[0] != 'Plugin') {
            continue;
        }

        if (empty($keyParts[1])) {
            continue;
        }

        $composerPlugins[$keyParts[1]] = substr($autoLoaderPath['0'], mb_strlen($baseDir) + 1);
    }

    $content = '<?php return ';
    $content .= var_export($composerPlugins, true);
    $content .= ';';

    file_put_contents($composerPluginsRegisterFile, $content);
}

/**
 * @param $publicDir
 */
function createRootFiles($publicDir)
{
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
}

/**
 * @param $publicDir
 */
function copyAssets($publicDir)
{
    $coreAssetPaths = glob(dirname(__DIR__) . '/Ip/Internal/*/assets/');
    foreach ($coreAssetPaths as $coreAssetPath) {
        $module = basename(dirname($coreAssetPath));
        copyPluginAssets(dirname($coreAssetPath), $publicDir . '/Ip/Internal/' . $module);
    }

    $pluginsAssetPaths = glob(dirname(dirname(dirname(__DIR__))) . '/*/*/assets/');
    foreach ($pluginsAssetPaths as $pluginAssetsPath) {
        $pluginDir = dirname($pluginAssetsPath);
        $pluginName = parsePluginName($pluginDir);
        if (!$pluginName) {
            continue;
        }
        copyPluginAssets($pluginDir, $publicDir . '/Plugin/' . $pluginName);
    }
}

function createMainDirs($publicDir)
{
    `rm -rf $publicDir/Ip/*`;

    `mkdir -p $publicDir/Ip`;
    `mkdir -p $publicDir/Ip/Internal`;

    if (!is_dir($publicDir . '/Plugin')) {
        `cp -rf vendor/impresspages/impresspages/start-pack/Plugin public/`;
    }

    if (!is_dir($publicDir . '/Theme')) {
        `cp -rf vendor/impresspages/impresspages/start-pack/Theme public/`;
    }

    if (!is_dir($publicDir . '/file')) {
        `cp -rf vendor/impresspages/impresspages/start-pack/file public/`;
    }
}

function copyPluginAssets($pluginDir, $destinationDir)
{
    `rm -rf $destinationDir`;
    `mkdir -p $destinationDir`;
    `cp -rf $pluginDir/assets $destinationDir`;

    $assetPaths = glob($pluginDir . '/Widget/*/assets/');
    foreach ($assetPaths as $assetPath) {
        $widget = basename(dirname($assetPath));
        `mkdir -p $destinationDir/Widget/$widget/`;
        `cp -rf $assetPath $destinationDir/Widget/$widget`;
    }

}

function parsePluginName($pluginDir)
{
    $pluginJson = file_get_contents($pluginDir . '/Setup/plugin.json');
    $data = json_decode($pluginJson, true);
    if (!empty($data['name'])) {
        return $data['name'];
    }
    return false;
}
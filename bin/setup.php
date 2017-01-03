<?php

require (dirname(dirname(dirname(__DIR__ . ''))) . '/autoload.php');
$publicDir = 'public';

if (!empty($argv[1])) {
    $publicDir = $argv[1];
}

if (str_replace('\\', '/', getcwd()) . '/vendor/impresspages/impresspages/bin' != str_replace('\\', '/', __DIR__)) {
    throw new \Exception('This script must be executed from the project root (where composer.json is placed');
}

createMainDirs($publicDir);

copyAssets($publicDir);

createRootFiles($publicDir);

registerComposerInstalledPlugins();

function registerComposerInstalledPlugins()
{
    $vendorDir = dirname(dirname(dirname(__DIR__)));
    $baseDir = getcwd();
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

    $content = '<?php return ' . "\n";
    $content .= '// This is a auto generated file during composer install/update. It helps ImpressPages core to load plugins from vendor directory.';
    $content .= '// It helps ImpressPages core to load plugins from vendor directory.';
    $content .= '// It is common to add this file to the git repository.';
    $content .= '// But you shouldn\'t change this file manually as it will be regenerated on next composer install/update.';
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
        copy('vendor/impresspages/impresspages/start-pack/admin.php', $publicDir . '/admin.php');
        copy('vendor/impresspages/impresspages/start-pack/favicon.ico', $publicDir . '/favicon.ico');
        copy('vendor/impresspages/impresspages/start-pack/index.php', $publicDir . '/index.php');
        copy('vendor/impresspages/impresspages/start-pack/.htaccess', $publicDir . '/.htaccess');
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
	AdvancedFs::createPath($publicDir . '/Ip');
    AdvancedFs::cleanPath($publicDir . '/Ip/');
    AdvancedFs::createPath($publicDir . '/Ip/Internal/');

    $pluginDir = $publicDir . '/Plugin';
    if (!is_dir($pluginDir)) {
        AdvancedFs::createPath($pluginDir);
        AdvancedFs::copyPathContent('vendor/impresspages/impresspages/start-pack/Plugin', $pluginDir);
    }

    $pluginDir = $publicDir . '/Theme';
    if (!is_dir($pluginDir)) {
        AdvancedFs::createPath($pluginDir);
        AdvancedFs::copyPathContent('vendor/impresspages/impresspages/start-pack/Theme', $pluginDir);
    }

    $pluginDir = $publicDir . '/file';
    if (!is_dir($pluginDir)) {
        AdvancedFs::createPath($pluginDir);
        AdvancedFs::copyPathContent('vendor/impresspages/impresspages/start-pack/file', $pluginDir);
    }
}

function copyPluginAssets($pluginDir, $destinationDir)
{
    AdvancedFs::createPath($destinationDir);
    AdvancedFs::cleanPath($destinationDir);
    AdvancedFs::createPath($destinationDir . '/assets');
    AdvancedFs::copyPathContent($pluginDir . '/assets', $destinationDir . '/assets');

    $assetPaths = glob($pluginDir . '/Widget/*/assets/');
    foreach ($assetPaths as $assetPath) {
        $widget = basename(dirname($assetPath));
        $dest = $destinationDir . '/Widget/' . $widget . '/assets';
        AdvancedFs::createPath($dest);
        AdvancedFs::copyPathContent($assetPath, $dest);
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

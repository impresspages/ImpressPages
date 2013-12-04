<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Modules\developer\modules;

require_once(__DIR__ . '/configuration_file.php');
require_once(__DIR__ . '/installation.php');
require_once(__DIR__ . '/manager.php');
require_once (BASE_DIR.LIBRARY_DIR.'php/standard_module/std_mod.php');



class Service {

    /**
     * @param $pluginDir
     * @return bool|ConfigurationFile
     */
    public static function parsePluginConfig($pluginDir)
    {
        $configFile = $pluginDir . '/install/plugin.ini';
        if (!file_exists($configFile)) {
            return false;
        }
        $configuration = new ConfigurationFile($configFile);
        return $configuration;
    }

    public static function installPlugin($pluginGroup, $pluginName)
    {
//        global $cms;
//        if (!$cms) {
//            require (BASE_DIR.BACKEND_DIR.'cms.php');
//            $cms = new \Backend\Cms();
//        }
        $installation = new ModulesInstallation();
        $installation->recursiveInstall($pluginGroup, $pluginName);
    }

}
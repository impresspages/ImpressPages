<?php
namespace Ip\Internal\Plugins;


class Service
{
    public static function getActivePluginNames()
    {
        return Model::getActivePluginNames();
    }

    public static function getAllPluginNames()
    {
        return Model::getAllPluginNames();
    }

    public static function getActivePlugins()
    {
        return Model::getActivePlugins();
    }

    public static function parsePluginConfigFile($pluginDir)
    {
        return Model::parseConfigFile($pluginDir);
    }

    public static function activatePlugin($pluginName)
    {
        Model::activatePlugin($pluginName);
    }

    public static function deactivatePlugin($pluginName)
    {
        Model::deactivatePlugin($pluginName);
    }

    public static function removePlugin($pluginName)
    {
        Model::removePlugin($pluginName);
    }


}

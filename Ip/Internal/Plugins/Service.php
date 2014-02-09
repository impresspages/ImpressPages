<?php
namespace Ip\Internal\Plugins;


class Service
{
    public static function getActivePluginNames()
    {
        return Model::getActivePluginNames();
    }

    public static function getActivePlugins()
    {
        return Model::getActivePlugins();
    }

    public static function parsePluginConfigFile($configFile)
    {
        return Model::parseConfigFile($configFile);
    }

}

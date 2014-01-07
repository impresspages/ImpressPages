<?php

namespace Plugin\AssetRsync;


class System
{
    public function init()
    {
        ipDispatcher()->addEventListener('Ip.cacheClear', array($this, 'syncAssets'));
        ipDispatcher()->addEventListener('Ip.pluginActivated', array($this, 'onPluginActivate'));
        ipDispatcher()->addEventListener('Ip.pluginDeactivated', array($this, 'onPluginDeactivate'));
    }

    public function syncAssets()
    {
        if (ipGetOption('AssetRsync.syncOnCacheClear') && ipGetOption('AssetRsync.assetDestinationDirectory')) {
            Model::syncAssets();
        }
    }

    public function onPluginActivate($data)
    {
        if (ipGetOption('AssetRsync.assetDestinationDirectory')) {
            Model::syncPlugin('Plugin', $data['name']);
        }
    }

    public function onPluginDeactivate()
    {

    }
}

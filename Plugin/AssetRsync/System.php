<?php

namespace Plugin\AssetRsync;


class System
{
    public function init()
    {
        ipDispatcher()->addEventListener('site.clearCache', array($this, 'syncAssets'));
        ipDispatcher()->addEventListener('Plugin.activate', array($this, 'onPluginActivate'));
        ipDispatcher()->addEventListener('Plugin.deactivate', array($this, 'onPluginDeactivate'));
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

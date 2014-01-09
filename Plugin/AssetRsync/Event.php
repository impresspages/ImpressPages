<?php


namespace Plugin\AssetRsync;


class Event
{
    public static function ipCacheClear()
    {
        if (ipGetOption('AssetRsync.syncOnCacheClear') && ipGetOption('AssetRsync.assetDestinationDirectory')) {
            Model::syncAssets();
        }
    }

    public static function ipPluginActivated($data)
    {
        if (ipGetOption('AssetRsync.assetDestinationDirectory')) {
            Model::syncPlugin('Plugin', $data['name']);
        }
    }

    public static function ipPluginDeactivated()
    {

    }


} 
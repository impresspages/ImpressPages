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

} 
<?php


namespace Ip\Internal\Core;


class Service
{
    public static function invalidateAssetCache()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion') + 1);
    }
}

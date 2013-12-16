<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Module\Pages;





class Service
{

    /**
     * @param string $zoneName
     * @param int $pageId
     * @param array $data
     */
    public static function updatePage($zoneName, $pageId, $data)
    {
        Db::updatePage($zoneName, $pageId, $data);
    }
}
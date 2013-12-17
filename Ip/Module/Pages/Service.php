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


    public static function addPage($parentId, $title, $data = array()) {
        if (!isset($data['navigationTitle'])) {
            $data['navigationTitle'] = $title;
        }
        if (!isset($data['pageTitle'])) {
            $data['pageTitle'] = $title;
        }

        if (!isset($data['url'])) {
            $data['url'] = Db::makeUrl($title);
        }


        if (!isset($data['createdOn'])) {
            $data['createdOn'] = date("Y-m-d");
        }
        if (!isset($data['lastModified'])) {
            $data['lastModified'] = date("Y-m-d");
        }
        if (!isset($data['visible'])) {
            $data['visible'] = !ipGetOption('Pages.hideNewPages');
        }

        $newPageId = Db::addPage($parentId, $data);

        return $newPageId;
    }


    /**
     * @param string $zoneName
     * @param int $languageId
     * @return int
     */
    public static function getRootId($zoneName, $languageId)
    {
        $zone = ipContent()->getZone($zoneName);
        $zoneId = $zone->getId();
        $rootId = Db::rootContentElement($zoneId, $languageId);
        return $rootId;
    }

}
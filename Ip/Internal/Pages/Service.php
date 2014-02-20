<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Internal\Pages;





class Service
{
    public static function getPage($pageId)
    {
        return Model::getPage($pageId);
    }

    public static function getMenu($languageCode, $alias)
    {
        return Model::getMenu($languageCode, $alias);
    }

    public static function createMenu($languageCode, $alias, $title)
    {
        return Model::createMenu($languageCode, $alias, $title);
    }

    public static function addMenu($title, $name, $url, $layout, $metaTitle, $metaKeywords, $metaDescription, $position)
    {
        $zoneName = Model::addMenu($title, $name, $url, $layout, $metaTitle, $metaKeywords, $metaDescription, $position);
        return $zoneName;
    }

    public static function updateMenu($menuId, $alias, $title, $layout)
    {
        Model::updateMenu($menuId, $alias, $title, $layout);
    }

    public static function deleteZone($zoneName)
    {
        Model::deleteZone($zoneName);
    }

    /**
     * @param string $zoneName
     * @param int $pageId
     * @param array $data
     */
    public static function updatePage($pageId, $data)
    {
        Model::updatePageProperties($pageId, $data);
    }

    public static function addPage($parentId, $title, $data = array())
    {
        if (!isset($data['pageTitle'])) {
            $data['pageTitle'] = $title;
        }

        if (!isset($data['url'])) {
            $data['url'] = Db::makeUrl($title);
        }

        if (!isset($data['createdAt'])) {
            $data['createdAt'] = date("Y-m-d H:i:s");
        }
        if (!isset($data['updatedAt'])) {
            $data['updatedAt'] = date("Y-m-d H:i:s");
        }
        if (!isset($data['isVisible'])) {
            $data['isVisible'] = !ipGetOption('Pages.hideNewPages');
        }

        if (!isset($data['languageCode'])) {
            $data['languageCode'] = ipDb()->selectValue('page', 'languageCode', array('id' => $parentId));
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
        $rootId = Db::rootId($zoneId, $languageId);
        return $rootId;
    }

    public static function copyPage($pageId, $destinationParentId, $destinationPosition)
    {
        $pageInfo = Db::pageInfo($pageId);
        $destinationPageInfo = Db::pageInfo($destinationParentId);
        $zoneName = Db::getZoneName($pageInfo['zone_id']);
        $destinationZone = ipContent()->getZone(Db::getZoneName($destinationPageInfo['zone_id']));
        return Model::copyPage($zoneName, $pageId, $destinationZone->getName(), $destinationParentId, $destinationPosition);
    }


    public static function movePage($pageId, $destinationParentId, $destinationPosition)
    {
        Model::movePage($pageId, $destinationParentId, $destinationPosition);
    }

    public static function deletePage($pageId)
    {
        Model::deletePage($pageId);
    }



}

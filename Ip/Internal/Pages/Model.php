<?php

/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Internal\Pages;


class Model
{

    public static function removeZonePages ($zoneId)
    {
        $pages = ipDb()->selectAll('zone_to_page', '*', array('zone_id' => $zoneId));
        foreach ($pages as $page) {
            Service::deletePage($page['element_id']);
        }
    }

    public static function sortZone($menuName, $newIndex)
    {
        $menu = Db::getZones(ipContent()->getCurrentLanguage()->getId());

        $newPriority = null;

        if ($newIndex <= 0) {
            $newPriority = $zones[0]['pageOrder'] - 20;
        } elseif ($newIndex > count($zones) - 1) {
            $lastZone = end($zones);
            $newPriority = $lastZone['pageOrder'] + 20;
        } else {
            $newPriority = ($zones[$newIndex - 1]['pageOrder'] + $zones[$newIndex]['pageOrder']) / 2;
        }

        ipDb()->update('zone', array('pageOrder' => $newPriority), array('name' => $menuName));
    }

    public static function deletePage($pageId)
    {
        $children = Db::pageChildren($pageId);
        if ($children) {
            foreach ($children as $child) {
                self::deletePage($child['id']);
            }
        }

        ipDb()->delete('page', array('id' => $pageId));
        ipPageStorage($pageId)->removeAll();

        ipEvent('ipPageDeleted', array('pageId' => $pageId));
    }

    /**
     *
     * Copy page
     * @param int $pageId
     * @param int$newParentId
     * @param int $position page position in the subtree //TODO implement
     */
    public static function copyPage($pageId, $destinationPageId, $position = null)
    {

        $children = Db::pageChildren($destinationPageId);

        if (!empty($children)) {
            $rowNumber = $children[count($children) - 1]['pageOrder'] + 1;
        } else {
            $rowNumber = 0;
        }


        return self::_copyPageRecursion($pageId, $destinationPageId, $rowNumber);

    }

    /**
     *
     * Copy page internal recursion
     * @param unknown_type $nodeId
     * @param unknown_type $destinationPageId
     * @param unknown_type $newIndex
     * @param unknown_type $newPages
     */
    private static function _copyPageRecursion(
        $nodeId,
        $destinationPageId,
        $rowNumber,
        $newPages = null
    ) {
        //$newPages are the pages that have been copied already and should be skipped to duplicate again. This situation can occur when copying the page to it self
        if ($newPages == null) {
            $newPages = array();
        }
        $newNodeId = Db::copyPage($nodeId, $destinationPageId, $rowNumber);
        $newPages[$newNodeId] = 1;
        self::_copyWidgets($nodeId, $newNodeId);


        $children = Db::pageChildren($nodeId);
        if ($children) {
            foreach ($children as $key => $lock) {
                if (!isset($newPages[$lock['id']])) {
                    self::_copyPageRecursion($lock['id'], $newNodeId, $key, $newPages);
                }
            }
        }
        return $newNodeId;

    }

    private static function _copyWidgets($sourceId, $targetId)
    {
        $oldRevision = \Ip\Internal\Revision::getPublishedRevision($sourceId);
        \Ip\Internal\Revision::duplicateRevision($oldRevision['revisionId'], $targetId, 1);
    }



    protected static function deleteZoneParameters($languageId)
    {
        //remove zone to content binding
        ipDb()->delete('zone_to_page', array('language_id' => $languageId));
    }

    protected static function removeZoneToContent($languageId)
    {
        //remove zone translations
        ipDb()->delete('zone_to_language', array('language_id' => $languageId));
    }

    protected static function newZoneUrl($languageId, $requestedUrl)
    {
        $table = ipTable('zone_to_language');
        $sql = "
        SELECT
            `url`
        FROM
            $table
        WHERE
            `language_id` = :languageId
        ";

        $params = array (
            'languageId' => $languageId
        );
        $takenUrls = ipDb()->fetchColumn($sql, $params);

        if (in_array($requestedUrl, $takenUrls)) {
            $i = 1;
            while(in_array($requestedUrl.$i, $takenUrls)) {
                $i++;
            }
            return $requestedUrl.$i;
        } else {
            return $requestedUrl;
        }
    }



    public static function getMenu($languageCode, $alias)
    {
        return ipDb()->selectRow('page', '*', array('languageCode' => $languageCode, 'alias' => $alias));
    }

    public static function getPage($pageId)
    {
        return ipDb()->selectRow('page', '*', array('id' => $pageId));
    }

    protected static function getNextPageOrder($where)
    {
        $value = ipDb()->selectValue('page', 'MAX(`pageOrder`)', $where); //can't use +1 in mysql. It fails if there are no records
        $value++;
        return $value;
    }



    public static function changePageUrlPath($pageId, $newUrlPath)
    {
        $pageBeforeChange = ipPage($pageId);

        if ($newUrlPath == $pageBeforeChange->getUrlPath()) {
            return false;
        }

        $allocatedPath = UrlAllocator::allocatePath($pageBeforeChange->getLanguageCode(), $newUrlPath);
        ipDb()->update('page', array('urlPath' => $allocatedPath), array('id' => $pageId));

        $pageAfterChange = ipPage($pageId);

        ipEvent('ipUrlChanged', array(
                'oldUrl' => $pageBeforeChange->getLink(),
                'newUrl' => $pageAfterChange->getLink(),
            ));
    }




    public static function deleteZone($zoneName)
    {
        $zone = ipDb()->selectRow('zone', '*', array('name' => $zoneName));
        if ($zone) {
            ipEvent('ipBeforeZoneDeleted', $zone);
            ipDb()->delete('zone', array('name' => $zoneName));
            ipDb()->delete('zone_to_language', array('zone_id' => $zone['id']));
        }
    }

    /**
     * @param $pageId
     * @param $properties
     * @return bool
     */
    public static function updatePageProperties($pageId, $properties)
    {
        $update = array();

        if (isset($properties['title'])) {
            $update['title'] = $properties['title'];
        }

        if (isset($properties['metaTitle'])) {
            $update['metaTitle'] = $properties['metaTitle'];
        }

        if (isset($properties['keywords'])) {
            $update['keywords'] = $properties['keywords'];
        }

        if (isset($properties['description'])) {
            $update['description'] = $properties['description'];
        }

        if (isset($properties['createdAt']) && strtotime($properties['createdAt']) !== false) {
            $update['createdAt'] = $properties['createdAt'];
        }

        if (isset($properties['updatedAt']) && strtotime($properties['updatedAt']) !== false) {
            $update['updatedAt'] = $properties['updatedAt'];
        }

        if (isset($properties['type'])) {
            $update['type'] = $properties['type'];
        }

        if (isset($properties['redirectURL'])) {
            $update['redirectUrl'] = $properties['redirectURL'];
        }

        if (isset($properties['isVisible'])) {
            $update['isVisible'] = $properties['isVisible'];
        }

        if (count($update) == 0) {
            return true; //nothing to update.
        }

        ipDb()->update('page', $update, array('id' => $pageId));

        if (!empty($properties['layout'])) {
            ipPageStorage($pageId)->set('layout', $properties['layout']);
        }

        return true;
    }

    public static function movePage($pageId, $destinationParentId, $destinationPosition)
    {
        if (Db::isChild($destinationParentId, $pageId) || (int)$pageId === (int)$destinationParentId) {
            throw new \Ip\Exception(__("Can't move page inside itself.", 'ipAdmin', false));
        }

        $newParentChildren = Db::pageChildren($destinationParentId);
        $newPageOrder = 0; //initial value

        if (count($newParentChildren) > 0) {
            $newPageOrder = $newParentChildren[0]['pageOrder'] - 1;  //set as first page
            if ($destinationPosition > 0) {
                if (isset($newParentChildren[$destinationPosition - 1]) && isset($newParentChildren[$destinationPosition])) { //new position is in the middle of other pages
                    $newPageOrder = ($newParentChildren[$destinationPosition - 1]['pageOrder'] + $newParentChildren[$destinationPosition]['pageOrder']) / 2; //average
                } else { //new position is at the end
                    $newPageOrder = $newParentChildren[count($newParentChildren) - 1]['pageOrder'] + 1;
                }
            }
        }

        $update = array (
            'parentId' => $destinationParentId,
            'pageOrder' => $newPageOrder
        );

        ipDb()->update('page', $update, array('id' => $pageId));
    }

}

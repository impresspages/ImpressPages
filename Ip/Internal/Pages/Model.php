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
            $newPriority = $zones[0]['row_number'] - 20;
        } elseif ($newIndex > count($zones) - 1) {
            $lastZone = end($zones);
            $newPriority = $lastZone['row_number'] + 20;
        } else {
            $newPriority = ($zones[$newIndex - 1]['row_number'] + $zones[$newIndex]['row_number']) / 2;
        }

        ipDb()->update('zone', array('row_number' => $newPriority), array('name' => $menuName));
    }

    public static function cleanupLanguage($id)
    {
        $zones = ipContent()->getZones();
        foreach($zones as $zone) {
            if ($zone->getAssociatedModule() == 'Content') {
                $rootId = Db::rootId($zone->getId(), $id);
                Model::deletePage($rootId);
            }
        }
    }

    protected static function uniqueZoneName($name)
    {
        $suffix = '';

        while(ipContent()->getZone($name.$suffix)) {
            $suffix++;
        }
        return $name.$suffix;
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
     * @param unknown_type $nodeId
     * @param unknown_type $newParentId
     * @param int $position page position in the subtree
     */
    public static function copyPage($zoneName, $nodeId, $destinationZoneName, $destinationPageId, $position)
    {

        $children = Db::pageChildren($destinationPageId);

        if (!empty($children)) {
            $rowNumber = $children[count($children) - 1]['row_number'] + 1;
        } else {
            $rowNumber = 0;
        }


        return self::_copyPageRecursion($zoneName, $nodeId, $destinationZoneName, $destinationPageId, $rowNumber);

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
        $zoneName,
        $nodeId,
        $destinationZoneName,
        $destinationPageId,
        $rowNumber,
        $newPages = null
    ) {
        //$newPages are the pages that have been copied already and should be skiped to duplicate again. This situacion can occur when copying the page to it self
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
                    self::_copyPageRecursion($zoneName, $lock['id'], $destinationZoneName, $newNodeId, $key, $newPages);
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

    protected static function createParametersZone($zoneId, $url, $title, $keywords, $description)
    {
        //create zone translations
        $languages = ipContent()->getLanguages();
        foreach ($languages as $language) {
            $params = array(
                'language_id' => $language->getId(),
                'zone_id' => $zoneId,
                'url' =>self::newZoneUrl($language->getId(), $url),
                'title' => $title,
                'keywords' => $keywords,
                'description' => $description
            );
            ipDb()->insert('zone_to_language', $params);
        }
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

    public static function createMenu($languageCode, $alias, $title)
    {
        $data = array();
        $data['languageCode'] = $languageCode;
        $data['alias'] = $alias;
        $data['navigationTitle'] = $title;

        if (!array_key_exists('parentId', $data)) {
            $data['parentId'] = 0;
        }

        if (!array_key_exists('pageOrder', $data)) {
            $data['pageOrder'] = static::getNextPageOrder(array('languageCode' => $languageCode, 'parentId' => $data['parentId']));
        }

        if (!array_key_exists('isVisible', $data)) {
            $data['isVisible'] = (int)!ipGetOption('Pages.hideNewPages');
        }

        $menuId = ipDb()->insert('page', $data);

        return $menuId ? $alias : null;
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

    public static function addMenu($title, $name, $url, $layout, $metaTitle, $metaKeywords, $metaDescription, $position)
    {
        $zones = Db::getZones(ipContent()->getCurrentLanguage()->getId());
        $rowNumber = 0; //initial value

        if(count($zones) > 0) {
            $rowNumber = $zones[0]['row_number'] - 1;  //set as first page
            if ($position > 0) {
                if (isset($zones[$position - 1]) && isset($zones[$position])) { //new position is in the middle of other pages
                    $rowNumber = ($zones[$position - 1]['row_number'] + $zones[$position]['row_number']) / 2; //average
                } else { //new position is at the end
                    $rowNumber = $zones[count($zones) - 1]['row_number'] + 1;
                }
            }
        }


        $zoneName = self::uniqueZoneName($name);

        $data = array(
            'translation' => $title,
            'name' => $zoneName,
            'row_number' => $rowNumber,
            'associated_module' => 'Content',
            'template' => $layout
        );
        $zoneId = ipDb()->insert('zone', $data);

        self::createParametersZone($zoneId, $url, $metaTitle, $metaKeywords, $metaDescription);

        return $zoneName;
    }


    public static function updateMenu($menuId, $alias, $title, $layout)
    {
        $update = array(
            'alias' => $alias,
            'navigationTitle' => $title,
        );

        ipDb()->update('page', $update, array('id' => $menuId));
        ipPageStorage($menuId)->set('layout', $layout);
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

        if (isset($properties['navigationTitle'])) {
            $update['navigationTitle'] = $properties['navigationTitle'];
        }

        if (isset($properties['pageTitle'])) {
            $update['pageTitle'] = $properties['pageTitle'];
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

    public static function updatePageSlug($pageId, $slug)
    {
        $page = ipDb()->selectRow('page', array('parentId', 'pageTitle', 'navigationTitle', 'slug', 'url'), array('id' => $pageId));

        $parentUrl = ipDb()->selectValue('page', 'url', array('id' => $page['parentId']));

        $slug = str_replace("/", "-", $slug);

        $newUrl = $parentUrl . '/' . $slug;

        if ($newUrl == $page['url']) {
            return false;
        }

        if (!Db::availableUrl($newUrl, $pageId)) {
            $i = 1;
            while (!Db::availableUrl("$newUrl-$i", $pageId)) {
                $i++;
            }

            $newUrl = "$newUrl-$i";
            $slug .= '-' . $i;
        }

        ipDb()->update('page', array('url' => $newUrl, 'slug' => $slug), array('id' => $pageId));

        if ($newUrl != $page['url']) {
            // TODOX full url
            ipEvent('ipUrlChanged', array('oldUrl' => $page['url'], 'newUrl' => $newUrl));
        }

        return true;
    }

    public static function regeneratePageSlug($pageId)
    {
        $page = ipDb()->selectRow('page', array('pageTitle', 'navigationTitle'), array('id' => $pageId));

        if (!empty($page['pageTitle'])) {
            $slug = $page['pageTitle'];
        } elseif (!empty($page['navigationTitle'])) {
            $slug = $page['navigationTitle'];
        } else {
            throw new \Ip\Exception('Page has no title.');
        }

        return static::updatePageSlug($pageId, $slug);
    }

    public static function movePage($pageId, $destinationParentId, $destinationPosition)
    {
        if (Db::isChild($destinationParentId, $pageId) || (int)$pageId === (int)$destinationParentId) {
            throw new \Ip\Exception(__("Can't move page inside itself.", 'ipAdmin', false));
        }

        // for ipUrlChanged event
        $oldPage = new \Ip\Page($pageId);
        $oldUrl = $oldPage->getLink();
        // for ipUrlChanged event

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

        static::updatePageSlug($pageId, $oldPage->getSlug());

        $newPage = new \Ip\Page($pageId);

        ipEvent('ipUrlChanged', array('oldUrl' => $oldUrl, 'newUrl' => $newPage->getLink()));
    }

}

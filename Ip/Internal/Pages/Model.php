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
        $pages = ipDb()->select('*', 'zone_to_page', array('zone_id' => $zoneId));
        foreach ($pages as $page) {
            Service::deletePage($page['element_id']);
        }
    }

    public static function sortZone($zoneName, $newIndex)
    {
        $zones = Db::getZones(ipContent()->getCurrentLanguage()->getId());

        $newPriority = null;

        if ($newIndex <= 0) {
            $newPriority = $zones[0]['row_number'] - 20;
        } elseif ($newIndex > count($zones) - 1) {
            $lastZone = end($zones);
            $newPriority = $lastZone['row_number'] + 20;
        } else {
            $newPriority = ($zones[$newIndex - 1]['row_number'] + $zones[$newIndex]['row_number']) / 2;
        }

        ipDb()->update('zone', array('row_number' => $newPriority), array('name' => $zoneName));
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
        self::deleteZoneParameters($id);
        self::removeZoneToContent($id);
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
        $pageInfo = Db::pageInfo($pageId);
        $zoneName = Db::getZoneName($pageInfo['zone_id']);
        $zone = ipContent()->getZone($zoneName);
        if (!$zone) {
            throw new \Exception("Unknown zone " . $zoneName);
        }
        self::_deletePageRecursion($zone, $pageId);
        return true;
    }


    private static function _deletePageRecursion(\Ip\Zone $zone, $id)
    {
        $children = Db::pageChildren($id);
        if ($children) {
            foreach ($children as $key => $lock) {
                self::_deletePageRecursion($zone, $lock['id']);
            }
        }

        Db::deletePage($id);

        ipDispatcher()->notify('site.pageDeleted', array('zoneName' => $zone->getName(), 'pageId' => $id));
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
        self::_copyWidgets($zoneName, $nodeId, $destinationZoneName, $newNodeId);


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

    private static function _copyWidgets($zoneName, $sourceId, $destinationZoneName, $targetId)
    {
        $oldRevision = \Ip\Revision::getPublishedRevision($zoneName, $sourceId);
        \Ip\Revision::duplicateRevision($oldRevision['revisionId'], $destinationZoneName, $targetId, 1);
    }


    public static function createParametersLanguage($languageId)
    {
        //create zone translations
        $zones = ipContent()->getZones();
        foreach ($zones as $zone) {
            $params = array(
                'language_id' => $languageId,
                'zone_id' => $zone->getId(),
                'title' => $zone->getTitle(),
                'url' =>self::newZoneUrl($languageId, $zone->getUrl())
            );
            ipDb()->insert('zone_to_language', $params);
        }
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

    public static function addZone($title, $name, $url, $layout, $metaTitle, $metaKeywords, $metaDescription, $position)
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

        ipContent()->invalidateZones();

        return $zoneName;
    }


    public static function updateZone($zoneName, $languageId, $title, $url, $name, $layout, $metaTitle, $metaKeywords, $metaDescription)
    {
        $zone = ipContent()->getZone($zoneName);
        if (!$zone) {
            throw new \Ip\CoreException('Unknown zone ' . $zoneName);
        }
        $language = ipContent()->getLanguage($languageId);
        if (!$language) {
            throw new \Ip\CoreException('Unknown language ' . $languageId);
        }

        $oldUrl = $zone->getUrl();

        //update zone table record
        $params = array(
            'name' => $name,
            'template' => $layout,
            'translation' => $title
        );

        ipDb()->update('zone', $params, array('name' => $zoneName));

        //update zone parameters table
        $newUrl = self::newZoneUrl($languageId, $url);
        $params = array(
            'url' => $newUrl,
            'title' => $metaTitle,
            'keywords' => $metaKeywords,
            'description' => $metaDescription
        );

        ipDb()->update('zone_to_language', $params, array('zone_id' => $zone->getId(), 'language_id' => $languageId));

        $oldUrl = ipFileUrl('') . $language->getUrl() . '/' . $oldUrl . '/';
        $newUrl = ipFileUrl('') . $language->getUrl() . '/' . $newUrl . '/';

        if ($oldUrl != $newUrl) {
            ipDispatcher()->notify('site.urlChanged', array('oldUrl' => $oldUrl, 'newUrl' => $newUrl));
        }
    }

    public static function deleteZone($zoneName)
    {
        $zone = ipDb()->select('*', 'zone', array('name' => $zoneName));
        if (isset($zone[0])) {
            $zone = $zone[0];
            ipDispatcher()->notify('Ip.deleteZone', $zone);
            ipDb()->delete('zone', array('name' => $zoneName));
            ipDb()->delete('zone_to_language', array('zone_id' => $zone['id']));
        }
    }

}
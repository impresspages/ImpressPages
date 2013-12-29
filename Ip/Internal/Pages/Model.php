<?php

/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Internal\Pages;


class Model
{


    public static function cleanupLanguage($id)
    {
        $zones = ipContent()->getZones();
        foreach($zones as $zone) {
            if ($zone->getAssociatedModule() == 'Content') {
                $rootId = Db::rootId($zone->getId(), $id);
                Model::deletePage($zone->getName(), $rootId);
            }
        }
        self::deleteZoneParameters($id);
        self::removeZoneToContent($id);
    }

    public static function uniqueZoneName($name)
    {
        $suffix = '';

        while(ipContent()->getZone($name.$suffix)) {
            $suffix++;
        }
        return $name.$suffix;
    }

    public static function deletePage($zoneName, $pageId)
    {

        $zone = ipContent()->getZone($zoneName);
        if (!$zone) {
            throw new \Exception("Unknown zone " + $zoneName);
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


        self::_copyPageRecursion($zoneName, $nodeId, $destinationZoneName, $destinationPageId, $rowNumber);

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
            ipDb()->insert('zone_parameter', $params);
        }
    }

    public static function createParametersZone($zoneId, $url, $title, $keywords, $description)
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
            ipDb()->insert('zone_parameter', $params);
        }
    }

    protected static function deleteZoneParameters($languageId)
    {
        //remove zone to content binding
        ipDb()->delete('zone_to_content', array('language_id' => $languageId));
    }

    protected static function removeZoneToContent($languageId)
    {
        //remove zone translations
        ipDb()->delete('zone_parameter', array('language_id' => $languageId));
    }

    protected static function newZoneUrl($languageId, $requestedUrl)
    {
        $table = ipTable('zone_parameter');
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

}
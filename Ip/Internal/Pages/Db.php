<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Internal\Pages;





class Db {






    public static function pageInfo($pageId){
        //check when root page id given
        $sql = "
        SELECT
            mte.*
        FROM
            ".ipTable('zone_to_page', 'mte')."
        WHERE
            mte.element_id = :pageId
        ";

        $params = array(
            'pageId' => $pageId
        );
        $answer = ipDb()->fetchRow($sql, $params);
        if ($answer) {
            return $answer;
        }

        //non root page id given
        $voidZone = new \Ip\Internal\Content\Zone(array());
        $breadcrumb = $voidZone->getBreadcrumb($pageId);
        $pageId = $breadcrumb[0]->getId();

        $sql = "
        SELECT
            mte.*
        FROM
            ".ipTable('zone_to_page', 'mte').",
            ".ipTable('page', 'page')."
        WHERE
            page.id = :pageId
            AND
            page.parentId = mte.element_id
        ";

        $params = array(
            'pageId' => $pageId
        );
        return ipDb()->fetchRow($sql, $params);
    }


    public static function getZoneName($zoneId){
        $sql = "
        SELECT
            `name`
        FROM
            ".ipTable('zone')."
        WHERE
            id = :id";

        $params = array(
            'id' => (int)$zoneId
        );

        return ipDb()->fetchValue($sql, $params);
    }


    /**
     * @param $zoneId
     * @param $languageId
     * @return mixed
     * @throws \Ip\Exception
     */
    public static function rootId($zoneId, $languageId)
    {
        $sql = '
            SELECT
                mte.element_id
            FROM ' . ipTable('zone_to_page', 'mte') . ', ' . ipTable('language', 'l') . '
            WHERE l.id = :languageId AND  mte.language_id = l.id AND zone_id = :zoneId';

        $where = array(
            'languageId' => $languageId,
            'zoneId' => $zoneId
        );

        $pageId = ipDb()->fetchValue($sql, $where);
        if (!$pageId) {
            $pageId = self::createRootZoneElement($zoneId, $languageId);
        }

        if (!$pageId) {
            throw new \Ip\Exception("Failed to create root zone element. Zone: ". $zoneId . ', ' . $languageId);
        }

        return $pageId;
    }

    /**
     * @param $zoneId
     * @param $languageId
     * @throws \Ip\Exception
     */
    protected static function createRootZoneElement($zoneId, $languageId)
    {
        $pageId = ipDb()->insert('page', array('isVisible' => 1));

        return $pageId;
    }


    public static function deleteRootZoneElements($languageId)
    {
        return ipDb()->delete('zone_to_page', array('language_id' => $languageId));
    }

    public static function isChild($pageId, $parentId)
    {
        $page = self::getPage($pageId);
        if (!$page) {
            return FALSE;
        }
        if ($page['parentId'] == $parentId) {
            return TRUE;
        }

        if ($page['parentId']) {
            return self::isChild($page['parentId'], $parentId);
        }

        return FALSE;
    }


    /**
     * Get page children
     * @param int $elementId
     * @return array
     */
    public static function pageChildren($parentId)
    {
        return ipDb()->selectAll('page', '*', array('parentId' => $parentId), 'ORDER BY `pageOrder`');
    }

    /**
     *
     * Get page
     * @param int $id
     * @return array
     */
    private static function getPage($id)
    {
        return ipDb()->selectRow('page', '*', array('id' => $id));
    }


    /**
     * @param int $language_id
     * @return array all website zones with meta tags for specified language
     */
    public static function getZones($languageId)
    {
        $sql = 'SELECT m.*, p.url, p.description, p.keywords, p.title
                FROM ' . ipTable('zone', 'm') . ', ' . ipTable('zone_to_language', 'p') . '
                WHERE
                    p.zone_id = m.id
                    AND p.language_id = ?
                ORDER BY m.row_number';

        return ipDb()->fetchAll($sql, array($languageId));
    }

    /**
     * @param $pageId
     * @param $newLayout
     * @return bool whether layout was changed or not
     */
    private static function changePageLayout($pageId, $newLayout)
    {
        ipPageStorage($pageId)->set('layout', $newLayout);
    }

    /**
     *
     * Insert new page
     * @param int $parentId
     * @param array $params
     */
    public static function addPage($parentId, $params)
    {
        $row = array(
            'parentId' => $parentId,
            'pageOrder' => self::getNextPageOrder($parentId),
        );

        $fields = array(
            'title',
            'metaTitle',
            'languageCode',
            'keywords',
            'description',
            'urlPath',
            'createdAt',
            'updatedAt',
            'type',
            'isVisible'
        );

        foreach ($fields as $column) {
            if (array_key_exists($column, $params)) {
                $row[$column] = $params[$column];
            }
        }

        if (empty($row['createdAt'])) {
            $row['createdAt'] = date('Y-m-d H:i:s');
        }

        if (empty($row['updatedAt'])) {
            $row['updatedAt'] = date('Y-m-d H:i:s');
        }



        return ipDb()->insert('page', $row);
    }

    private static function getNextPageOrder($parentId) {
        $order = ipDb()->selectValue('page', 'MAX(`pageOrder`) + 1', array('parentId' => $parentId));
        return $order ? $order : 1;
    }

    public static function copyPage($nodeId, $newParentId, $newIndex)
    {
        $db = ipDb();
        $copy = $db->selectRow('page', '*', array('id' => $nodeId));
        if (!$copy) {
            trigger_error("Element does not exist");
        }

        unset($copy['id']);
        $copy['parentId'] = $newParentId;
        $copy['row_number'] = $newIndex;
        $copy['url'] = UrlAllocator::ensureUniqueUrl($copy['url']);

        return ipDb()->insert('page', $copy);
    }


}

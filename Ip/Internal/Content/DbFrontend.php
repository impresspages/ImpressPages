<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Internal\Content;


class DbFrontend
{


    public static function getPageByUrl($url, $parent)
    {
        return ipDb()->selectRow('page', '*', array('url' => $url, 'parent' => $parent));
    }

    public static function getFirstPage($parent)
    {
        return ipDb()->selectRow('page', '*', array('visible' => 1, 'parent' => $parent), 'ORDER BY `row_number`');
    }

    public static function getRootPageId($zoneName, $language)
    {
        $sql = "select mte.element_id from
        " . ipTable('zone', 'm') . ",
        " . ipTable('zone_to_page', 'mte') . "
        where mte.zone_id = m.id
            and mte.language_id = :language_id
            and m.name = :zoneName
        ";
        return ipDb()->fetchValue($sql, array(
                'language_id' => $language,
                'zoneName' => $zoneName,
            ));
    }

    public static function languageByRootPage($pageId)
    { //returns root element of menu
        return ipDb()->selectValue('zone_to_page', 'language_id', array('element_id' => $pageId));
    }


    public static function getPages(
        $zoneName,
        $parent,
        $language,
        $currentPage,
        $selectedPage,
        $order = 'asc',
        $startFrom = 0,
        $limit = null,
        $includeHidden = false
    ) {
        if ($parent == null) {
            $parent = DbFrontend::getRootPageId($zoneName, $language);
        }

        $sql = 'SELECT * FROM ' . ipTable('page') . ' WHERE `parent` = :parentId';

        if (!$includeHidden) {
            $sql .= ' AND `visible` = 1';
        }

        $sql .= ' ORDER BY `row_number` ' . $order;

        if ($limit !== null) {
            $sql .= ' LIMIT ' . (int)$startFrom . ', ' . (int)$limit;
        }

        return ipDb()->fetchAll($sql, array('parentId' => $parent));
    }

    /**
     * @param $id
     * @return array
     */
    public static function getPage($id)
    {
        return ipDb()->fetchRow('select  *  from ' . ipTable('page') . ' WHERE `id` = ?', array($id));
    }

}



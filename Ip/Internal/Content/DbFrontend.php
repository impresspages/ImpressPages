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
        $rs = ipDb()->select('*', 'page', array('url' => $url, 'parent' => $parent), 'LIMIT 1');
        return $rs ? $rs[0] : null;
    }

    public static function getFirstPage($parent)
    {
        $rs = ipDb()->select('*', 'page', array('visible' => 1, 'parent' => $parent), ' order by row_number limit 1');
        return $rs ? $rs[0] : null;
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
        $rs = ipDb()->select('language_id', 'zone_to_page', array('element_id' => $pageId));
        if (!$rs) {
            return null;
        }

        return $rs[0]['language_id'];
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



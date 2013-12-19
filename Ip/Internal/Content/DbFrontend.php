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
        $sql = "select * from `" . DB_PREF . "content_element` where  url = '" . ip_deprecated_mysql_real_escape_string(
                $url
            ) . "' and parent = '" . ip_deprecated_mysql_real_escape_string($parent) . "' limit 1";
        $rs = ip_deprecated_mysql_query($sql);
        if ($rs) {
            $answer = ip_deprecated_mysql_fetch_assoc($rs);
        } else {
            $answer = false;
        }
        return $answer;
    }


    public static function getFirstPage($parent)
    {
        $sql = "select  *  from `" . DB_PREF . "content_element` where visible and parent = '" . ip_deprecated_mysql_real_escape_string(
                $parent
            ) . "' order by row_number limit 1";
        $rs = ip_deprecated_mysql_query($sql);
        if ($rs) {
            $answer = ip_deprecated_mysql_fetch_assoc($rs);
        }
        return $answer;
    }


    public static function getRootPageId($zoneName, $language)
    {
        $sql = "select mte.element_id from
    `" . DB_PREF . "zone` m,
    `" . DB_PREF . "zone_to_content` mte
    where mte.zone_id = m.id and mte.language_id = '" . ip_deprecated_mysql_real_escape_string($language) . "'
    and m.name = '" . ip_deprecated_mysql_real_escape_string($zoneName) . "'
    ";
        $rs = ip_deprecated_mysql_query($sql);
        if ($rs) {
            $lock = ip_deprecated_mysql_fetch_assoc($rs);
            return $lock['element_id'];
        } else
            trigger_error($sql . " " . ip_deprecated_mysql_error());
    }

    public static function languageByRootPage($pageId)
    { //returns root element of menu
        $sql = "select mte.language_id from `" . DB_PREF . "zone_to_content` mte where  mte.element_id = '" . (int)$pageId . "'";
        $rs = ip_deprecated_mysql_query($sql);
        if ($rs) {
            if ($lock = ip_deprecated_mysql_fetch_assoc($rs)) {
                return $lock['language_id'];
            }
        } else
            trigger_error("Can't find zone element " . $sql . " " . ip_deprecated_mysql_error());
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
        $answer = array();

        if ($parent == null) {
            $parent = DbFrontend::getRootPageId($zoneName, $language);
        }

        $sql = "select * from `" . DB_PREF . "content_element` where `parent` = " . (int)$parent . "";

        if (!$includeHidden) {
            $sql .= " and `visible` ";
        }

        $sql .= " order by `row_number` " . $order . " ";

        if ($limit !== null) {
            $sql .= " limit " . (int)$startFrom . ", " . (int)$limit;
        }


        $rs = ip_deprecated_mysql_query($sql);
        if ($rs) {
            while ($lock = ip_deprecated_mysql_fetch_assoc($rs)) {
                $answer[] = $lock;
            }
        } else {
            trigger_error($sql . " " . ip_deprecated_mysql_error());
        }

        return $answer;
    }


    /**
     * @param $id
     * @return array
     */
    public static function getPage($id)
    {
        $sql = "select  *  from `" . DB_PREF . "content_element` where id = '" . $id . "' ";
        $rs = ip_deprecated_mysql_query($sql);
        if ($rs) {
            if ($lock = ip_deprecated_mysql_fetch_assoc($rs)) {
                return $lock;
            } else {
                return false;
            }
        } else {
            trigger_error("Can't find menu element " . $sql . " " . ip_deprecated_mysql_error());
        }
        return false;
    }


}



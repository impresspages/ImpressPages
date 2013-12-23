<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Internal;

/**
 * Class for common tasks with database
 * @package ImpressPages
 */
class ContentDb {

    /**
     * @param int $language_id
     * @return array all website zones with meta tags for specified language
     */
    public static function getZones($languageId)
    {
        $sql = "select m.*, p.url, p.description, p.keywords, p.title from `" . DB_PREF . "zone` m,`" . DB_PREF . "zone_parameter` p where p.zone_id = m.id and p.language_id = " . (int) $languageId . " order by m.row_number";
        $rs = ip_deprecated_mysql_query($sql);
        if ($rs) {
            $zones = array();
            while ($lock = ip_deprecated_mysql_fetch_assoc($rs)) {
                $zones[$lock['name']] = $lock;
            }
        } else {
            trigger_error("Can't get all zones " . $sql . " ");
            return false;
        }
        return $zones;
    }


    /**
     *
     * @param string $url
     * @return array language attributes
     */
    public static function getLanguage($url) {
        $sql = "select * from `".DB_PREF."language` where `d_short` = '".ip_deprecated_mysql_real_escape_string($url)."' ";
        $rs = ip_deprecated_mysql_query($sql);
        if ($rs) {
            if($lock = ip_deprecated_mysql_fetch_assoc($rs))
            return $lock;
        } else {
            trigger_error($sql." ".ip_deprecated_mysql_error());
        }
    }


    /**
     *
     * @param int $id
     * @return array language attributes
     */
    public static function getLanguageById($languageId) {
        $sql = "select * from `".DB_PREF."language` where `id` = '".$languageId."' ";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs) {
            if($lock = ip_deprecated_mysql_fetch_assoc($rs))
            return $lock;
        } else {
            trigger_error($sql." ".ip_deprecated_mysql_error());
        }
    }

    /**
     * Finds first language of website
     * @return array
     */
    public static function getFirstLanguage() {
        $sql = "select * from `".DB_PREF."language` where visible order by row_number ";
        $rs = ip_deprecated_mysql_query($sql);
        if ($rs) {
            if($lock = ip_deprecated_mysql_fetch_assoc($rs))
            return $lock;
        } else {
            trigger_error($sql." ".ip_deprecated_mysql_error());
        }

    }


    /**
     *
     * @return array all visible website languages
     */
    public static function getLanguages($includeHidden = false) {
        $answer = array();
        if ($includeHidden) {
            $sql = "select * from `".DB_PREF."language` where 1 order by row_number";
        } else {
            $sql = "select * from `".DB_PREF."language` where visible order by row_number";
        }
        $rs = ip_deprecated_mysql_query($sql);
        if ($rs) {
            while($lock = ip_deprecated_mysql_fetch_assoc($rs))
            $answer[$lock['id']] = $lock;
        } else {
            trigger_error($sql." ".ip_deprecated_mysql_error());
            return false;
        }
        return $answer;
    }


    public static function getModules(){
        $sql = "
        SELECT
            *, g.name as g_name, m.name as m_name  
        FROM
            `".DB_PREF."module_group` g,
            `".DB_PREF."module` m
        WHERE
            m.group_id = g.id
        ";

        $rs = ip_deprecated_mysql_query($sql);
        if (!$rs) {
            trigger_error($sql." ".ip_deprecated_mysql_error());
            return false;
        }

        $answer = array();
        while ($lock = ip_deprecated_mysql_fetch_assoc($rs)) {
            $answer[] = $lock;
        }
        return $answer;
    }

    /**
     * @param string $groupName
     * @param string $moduleName
     * @param string $pageId
     * @return string|false
     */
    public static function getPageLayout($moduleName, $pageId)
    {
        $sql = 'SELECT
                   `layout`
                FROM
                   `' . DB_PREF . 'page_layout`
                WHERE
                   module_name = :moduleName
                   AND `page_id`   = :pageId';

        $dbh = ipDb()->getConnection();
        $q = $dbh->prepare($sql);
        $params = array(
            'moduleName' => $moduleName,
            'pageId' => $pageId,
        );
        $q->execute($params);

        return $q->fetchColumn(0);
    }

}

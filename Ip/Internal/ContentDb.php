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


    /**
     * @param string $moduleName
     * @param string $pageId
     * @return string|null
     */
    public static function getPageLayout($moduleName, $pageId)
    {
        $table = ipTable('page_layout');
        $sql = "SELECT
                   `layout`
                FROM
                   $table
                WHERE
                   module_name = :moduleName
                   AND `page_id`   = :pageId";

        return ipDb()->fetchValue($sql, array(
                'moduleName' => $moduleName,
                'pageId' => $pageId,
            ));
    }

}

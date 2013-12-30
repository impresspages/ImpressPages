<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Internal\Languages;

class Db {

    public static function getLanguages() {
        $answer = array();
        $sql = "select * from " . ipTable('language') . " where 1 order by row_number";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs) {
            while($lock = ip_deprecated_mysql_fetch_assoc($rs))
            $answer[] = $lock;
        }else {
            trigger_error($sql." ".ip_deprecated_mysql_error());
        }
        return $answer;
    }

    public static function getLanguageById($id) {
        $sql = "select * from " . ipTable('language') . " where `id` = '".(int)$id."' ";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs) {
            if($lock = ip_deprecated_mysql_fetch_assoc($rs)){
                return $lock;
            }
        }else {
            trigger_error($sql." ".ip_deprecated_mysql_error());
        }
        return false;
    }

    public static function getLanguageByUrl($url) {
        $sql = "select * from " . ipTable('language') . " where `url` = '".ip_deprecated_mysql_real_escape_string($url)."' ";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs) {
            if($lock = ip_deprecated_mysql_fetch_assoc($rs)){
                return $lock;
            }
        }else {
            trigger_error($sql." ".ip_deprecated_mysql_error());
        }
        return false;
    }

    public static function getZones() {
        $answer = array();
        $sql = "select * from " . ipTable('zone') . " where 1 order by row_number";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs) {
            while($lock = ip_deprecated_mysql_fetch_assoc($rs))
            $answer[] = $lock;
        }else {
            trigger_error($sql." ".ip_deprecated_mysql_error());
        }
        return $answer;
    }

    public static function deleteRootZoneElement($language) {
        $zones = Db::getZones();
        foreach($zones as $key => $zone) {

            $sql = "delete `".DB_PREF."content_element`.*, `".DB_PREF."zone_to_content`.* from `".DB_PREF."content_element`, `".DB_PREF."zone_to_content` where
      `".DB_PREF."zone_to_content`.zone_id = ".$zone['id']." and `".DB_PREF."zone_to_content`.element_id = `".DB_PREF."content_element`.id and `".DB_PREF."zone_to_content`.language_id = '".ip_deprecated_mysql_real_escape_string($language)."'";
            $rs = ip_deprecated_mysql_query($sql);
            if(!$rs) {
                trigger_error($sql." ".ip_deprecated_mysql_error());
            }

            $sql2 = "delete from " . ipTable('zone_parameter') . " where language_id = '".ip_deprecated_mysql_real_escape_string($language)."'";
            $rs2 = ip_deprecated_mysql_query($sql2);
            if(!$rs2)
            trigger_error($sql2." ".ip_deprecated_mysql_error());

        }


    }



    public static function newUrl($preferredUrl) {
        $suffix = '';
        $url = ipDb()->select('id', 'language', array('url' => $preferredUrl . $suffix));
        if (empty($url)) {
            return $preferredUrl;
        }

        while(!empty($url)) {
            $suffix++;
            $url = ipDb()->select('id', 'language', array('url' => $preferredUrl . $suffix));
        }

        return $preferredUrl . $suffix;
    }




}


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
        $sql = "select * from `".DB_PREF."language` where 1 order by row_number";
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
        $sql = "select * from `".DB_PREF."language` where `id` = '".(int)$id."' ";
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
        $sql = "select * from `".DB_PREF."language` where `url` = '".ip_deprecated_mysql_real_escape_string($url)."' ";
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
        $sql = "select * from `".DB_PREF."zone` where 1 order by row_number";
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

            $sql2 = "delete from `".DB_PREF."zone_parameter` where language_id = '".ip_deprecated_mysql_real_escape_string($language)."'";
            $rs2 = ip_deprecated_mysql_query($sql2);
            if(!$rs2)
            trigger_error($sql2." ".ip_deprecated_mysql_error());

        }


    }



    public static function newUrl($language, $url = 'zone') {
        $sql = "select url from `".DB_PREF."zone_parameter` where `language_id` = '".ip_deprecated_mysql_real_escape_string($language)."' ";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs) {
            $urls = array();
            while($lock = ip_deprecated_mysql_fetch_assoc($rs))
            $urls[$lock['url']] = 1;

            if (isset($urls[$url])) {
                $i = 1;
                while(isset($urls[$url.$i])) {
                    $i++;
                }
                return $url.$i;
            } else {
                return $url;
            }
        }else {
            trigger_error("Can't get all urls ".$sql." ");
        }
    }


    public static function createEmptyTranslations($language, $table) {
        $sql = "select * from `".DB_PREF."language` where `id` <> '".$language."' order by row_number";
        $rs = ip_deprecated_mysql_query($sql);
        if(!$rs || ip_deprecated_mysql_num_rows($rs) == 0)
        trigger_error($sql);
        else {
            $oldLang = ip_deprecated_mysql_fetch_assoc($rs);
            if($oldLang) {
                $sql = "select * from `".DB_PREF."".$table."` where `language_id` = '".$oldLang['id']."'";
                $rs = ip_deprecated_mysql_query($sql);
                if($rs) {
                    $translations = array();
                    while($lock = ip_deprecated_mysql_fetch_assoc($rs))
                    $translations[] = $lock;
                    foreach($translations as $key => $lock) {
                        $sql2 = "insert into `".DB_PREF."".$table."` set language_id = '".$language."', parameter_id = '".$lock['parameter_id']."', translation = '".ip_deprecated_mysql_real_escape_string($lock['translation'])."'";
                        $rs2 = ip_deprecated_mysql_query($sql2);
                        if(!$rs2)
                        trigger_error($sql2);
                    }
                }else
                trigger_error($sql);
            }
        }
    }

    public static function deleteTranslations($language, $table) {
        $sql = "delete from `".DB_PREF."".$table."` where language_id = '".$language."'";
        $rs = ip_deprecated_mysql_query($sql);
        if(!$rs) {
            trigger_error($sql." ".ip_deprecated_mysql_error());
        }
    }

}


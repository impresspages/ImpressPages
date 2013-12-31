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
        $result = ipDb()->select('*', 'language', array('id' => $id), 'LIMIT 1');
        if (isset($result[0])) {
            return $result[0];
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


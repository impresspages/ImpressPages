<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Modules\standard\seo;

if (!defined('BACKEND')) exit;

class Db{




    public static function getParameter($zoneId, $languageId){
        $sql = "select * from `".DB_PREF."zone_parameter` where `zone_id` = ".(int)$zoneId." and `language_id` = ".(int)$languageId." ";
        $rs = mysql_query($sql);
        if($rs){
            $answer = false;
            if($lock = mysql_fetch_assoc($rs)){
                $answer = $lock;
            }
            return $answer;
        } else {
            trigger_error($sql." ".mysql_error());
        }
        return false;
    }



    public static function newUrl($language, $url, $alowedId = null){
        $sql = "select id, url from `".DB_PREF."zone_parameter` where language_id = '".mysql_real_escape_string($language)."' ";
        $rs = mysql_query($sql);
        //require_once(BACKEND_DIR."cms.php");
        if($rs){
            $urls = array();
            while($lock = mysql_fetch_assoc($rs)){
                if($alowedId !== null && $lock['id'] != $alowedId){
                    $urls[$lock['url']] = 1;
                }
            }

            if(isset($urls[$url]) || \Ip\Backend\Cms::usedUrl($url)){
                $i = 1;
                while(isset($urls[$url.$i]) || \Ip\Backend\CmS::usedUrl($url.$i)){
                    $i++;
                }
                return $url.$i;
            }else
            return $url;
        }else
        trigger_error("Can't get all urls ".$sql." ");
    }




    public static function getLanguages(){
        $answer = array();
        $sql = "select id, d_long, d_short, url from `".DB_PREF."language` where 1 order by row_number  ";
        $rs = mysql_query($sql);
        if($rs){
            while($lock = mysql_fetch_assoc($rs))
            $answer[] = $lock;
        }else trigger_error($sql." ".mysql_error());
        return $answer;
    }

}


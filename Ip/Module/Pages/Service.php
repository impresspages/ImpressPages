<?php
/**
 * @package   ImpressPages
 *
 *
 */

namespace Ip\Module\Pages;

use Ip\Form\Exception;

class Service
{
    public static function addZone($title, $zoneName, $associatedModule, $defaultLayout, $associatedGroup = '', $description = '', $url = '') {

        $content = new \Ip\Content();

        if ($content->getZone($zoneName)){
            throw new \Exception("Zone '".$zoneName."' already exists.");
        }

//TODO  throw new Exception("Zone name ".$zoneName." already exists");


        $languages = Db::getLanguages();

        $row = array(
            'name' => $zoneName,
            'template' => $defaultLayout,
            'translation' => $title,
            'associated_module' => $associatedModule,
            'associated_group' => $associatedGroup
        );

        $zoneId = ipDb()->insert(ipDb()->tablePrefix() . 'zone', $row);

        /**
         * //rankomis sukurti įrašą DB
         */

        foreach($languages as $key => $language){

            $language_id = $language['id'];
            $language_title = $language['d_short'];

            $row = array(
                'visible' => 1
            );

            $element_id = ipDb()->insert(ipDb()->tablePrefix() . 'content_element', $row);

            $row = array(
                'language_id' => $language_id,
                'zone_id' => $zoneId,
                'element_id' => $element_id
            );

            ipDb()->insert(ipDb()->tablePrefix() . 'zone_to_content', $row);

            $row = array(
                'title' => $title,
                'language_id' => $language_id,
                'zone_id' => $zoneId,
                'url' => \Ip\Module\Languages\Db::newUrl($language_id, $url)
            );

            ipDb()->insert(ipDb()->tablePrefix() . 'zone_parameter', $row);


        }

//
//    $row = array(
//    'level' => \Psr\Log\LogLevel::ERROR,
//    'message' => 'Code uses ipLog()->log() without giving $level info.',
//    'context' => json_encode(array('args' => func_get_args())),
//    );
//
//    ipDb()->insert(ipDb()->tablePrefix() . 'log', $row);
    return true;

//
//
//
//        $languages = Db::getLanguages();
//        $zone = Db::getZone($zoneId);
//
//        foreach($languages as $key => $language){
//            $sql = "insert into `".DB_PREF."content_element` set `visible` = 1";
//            $rs = mysql_query($sql);
//            if($rs){
//                $sql2 = "insert into `".DB_PREF."zone_to_content` set
//            `language_id` = '".mysql_real_escape_string($language['id'])."',
//            `zone_id` = '".mysql_real_escape_string($zoneId)."',
//            `element_id` = '".mysql_insert_id()."'";
//                $rs2 = mysql_query($sql2);
//                if(!$rs2)
//                    trigger_error($sql2." ".mysql_error());
//
//                $sql2 = "insert into `".DB_PREF."zone_parameter` set
//            `title` = '".mysql_real_escape_string($translation)."',
//            `language_id` = '".mysql_real_escape_string($language['id'])."',
//            `zone_id` = '".$zoneId."',
//            `url` = '".mysql_real_escape_string(Db::newUrl($language['id'], $zone['translation']))."'";
//                $rs2 = mysql_query($sql2);
//                if(!$rs2)
//                    trigger_error($sql2." ".mysql_error());
//            }else{
//                trigger_error($sql." ".mysql_error());
//            }
//        }

//        Db::afterInsert($id);


    }

    public static function createRootZoneElement($language) {
        $firstLanguage = \Ip\Internal\ContentDb::getFirstLanguage();
        $zones = \Ip\Internal\ContentDb::getZones($firstLanguage['id']);
        foreach($zones as $key => $zone) {
            $sql2 = "insert into `".DB_PREF."zone_parameter` set
        language_id = '".ip_deprecated_mysql_real_escape_string($language)."',
        zone_id = '".$zone['id']."',
        title = '".ip_deprecated_mysql_real_escape_string(Db::newUrl($language, $zone['title']))."',
        url = '".ip_deprecated_mysql_real_escape_string(Db::newUrl($language, $zone['url']))."'";
            $rs2 = ip_deprecated_mysql_query($sql2);
            if(!$rs2)
                trigger_error($sql2." ".ip_deprecated_mysql_error());
        }
    }





}
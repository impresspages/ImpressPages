<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Modules\standard\content_management;

if (!defined('CMS')) exit;

class DbFrontend{





    public static function getElementByUrl($url, $parent){
        $sql = "select * from `".DB_PREF."content_element` where  url = '".mysql_real_escape_string($url)."' and parent = '".mysql_real_escape_string($parent)."' limit 1";
        $rs = mysql_query($sql);
        if($rs){
            $answer = mysql_fetch_assoc($rs);
        }else
        $answer = false;
        return $answer;
    }


    public static function getFirstElement($parent){
        $sql = "select  *  from `".DB_PREF."content_element` where visible and parent = '".mysql_real_escape_string($parent)."' order by row_number limit 1";
        $rs = mysql_query($sql);
        if($rs){
            $answer = mysql_fetch_assoc($rs);
        }
        return $answer;
    }


    public static function getRootElementId($zoneName, $language){
        $sql = "select mte.element_id from
    `".DB_PREF."zone` m, 
    `".DB_PREF."zone_to_content` mte 
    where mte.zone_id = m.id and mte.language_id = '".mysql_real_escape_string($language)."'
    and m.name = '".mysql_real_escape_string($zoneName)."'
    ";
        $rs = mysql_query($sql);
        if($rs){
            $lock = mysql_fetch_assoc($rs);
            return $lock['element_id'];
        }else
        trigger_error($sql." ".mysql_error());
    }

    public static function languageByRootElement($element_id){ //returns root element of menu
        $sql = "select mte.language_id from `".DB_PREF."zone_to_content` mte where  mte.element_id = '".(int)$element_id."'";
        $rs = mysql_query($sql);
        if($rs){
            if($lock = mysql_fetch_assoc($rs)){
                return $lock['language_id'];
            }
        }else
        trigger_error("Can't find zone element ".$sql." ".mysql_error());
    }


    public static function getElements($zoneName, $parent, $language, $currentElement, $selectedElement, $order = 'asc', $startFrom = 0, $limit = null, $includeHidden = false){
        $answer = array();

        if($parent == null)
        {
            $parent = DbFrontend::getRootElementId($zoneName, $language);
        }

        $sql = "select * from `".DB_PREF."content_element` where `parent` = ".(int)$parent."";

        if(!$includeHidden){
            $sql .= " and `visible` ";
        }

        $sql .= " order by `row_number` ".$order." ";

        if($limit !== null){
            $sql .= " limit ".(int)$startFrom.", ".(int)$limit;
        }


        $rs = mysql_query($sql);
        if($rs){
            while($lock = mysql_fetch_assoc($rs))
            $answer[] = $lock;
        }else
        trigger_error($sql." ".mysql_error());
         
        return $answer;
    }



    public static function getElement($id){ //return element
        $sql = "select  *  from `".DB_PREF."content_element` where id = '".$id."' ";
        $rs = mysql_query($sql);
        if($rs){
            if($lock = mysql_fetch_assoc($rs)){
                return $lock;
            }else
            return false;
        }else
        trigger_error("Can't find menu element ".$sql." ".mysql_error());
        return false;
    }


}



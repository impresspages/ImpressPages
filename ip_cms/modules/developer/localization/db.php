<?php
/**
 * @package   ImpressPages
 *
 *
 */

namespace Modules\developer\localization;

class Db{


    public static function getModuleGroup($name){
        $sql = "select * from `".DB_PREF."module_group` where `name` = '".ip_deprecated_mysql_real_escape_string($name)."'";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs){
            if($lock = ip_deprecated_mysql_fetch_assoc($rs)){
                return $lock;
            } else {
                return false;
            }
        }else{
            trigger_error($sql." ".ip_deprecated_mysql_error());
            return false;
        }
    }

    public static function getModule($groupName, $moduleName){
        $sql = "select m.* from `".DB_PREF."module` m, `".DB_PREF."module_group` g
    where m.group_id = g.id and m.name = '".ip_deprecated_mysql_real_escape_string($moduleName)."' and g.name = '".ip_deprecated_mysql_real_escape_string($groupName)."' ";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs){
            if($lock = ip_deprecated_mysql_fetch_assoc($rs)){
                return $lock;
            } else {
                return false;
            }
        }else{
            trigger_error($sql." ".ip_deprecated_mysql_error());
            return false;
        }
    }

    public static function getParameterGroup($moduleId, $groupName){
        $sql = "select * from `".DB_PREF."parameter_group` where `module_id` = '".(int)$moduleId."' and `name` = '".ip_deprecated_mysql_real_escape_string($groupName)."'";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs){
            if($lock = ip_deprecated_mysql_fetch_assoc($rs)){
                return $lock;
            } else {
                return false;
            }
        }else{
            trigger_error($sql." ".ip_deprecated_mysql_error());
            return false;
        }
    }


    public static function getParameter($moduleGroupName, $moduleName, $parameterGroupName, $parameterName){
        $sql = "select * from `".DB_PREF."module_group` mg, `".DB_PREF."module` m, `".DB_PREF."parameter_group` pg, `".DB_PREF."parameter` p
    where p.group_id = pg.id and pg.module_id = m.id and m.group_id = mg.id
    and mg.name = '".ip_deprecated_mysql_real_escape_string($moduleGroupName)."' and m.name = '".ip_deprecated_mysql_real_escape_string($moduleName)."' and pg.name = '".ip_deprecated_mysql_real_escape_string($parameterGroupName)."' and p.name = '".ip_deprecated_mysql_real_escape_string($parameterName)."'";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs){
            if($lock = ip_deprecated_mysql_fetch_assoc($rs)){
                return $lock;
            }else{
                return false;
            }

        }else{
            trigger_error($sql." ".ip_deprecated_mysql_error());
            return false;
        }

    }




    public static function setModuleGroupTitle($moduleGroupName,$value){
        $sql = "update `".DB_PREF."module_group`
    set `translation` = '".ip_deprecated_mysql_real_escape_string($value)."'
    where  name = '".ip_deprecated_mysql_real_escape_string($moduleGroupName)."' ";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs){
            return true;
        }else{
            trigger_error($sql." ".ip_deprecated_mysql_error());
            return false;
        }
    }

    public static function createParameterGroup($moduleId, $name, $translation, $admin){
        $sql = "insert into `".DB_PREF."parameter_group` set `name`='".ip_deprecated_mysql_real_escape_string($name)."', `translation`='".ip_deprecated_mysql_real_escape_string($translation)."', `admin`='".(int)$admin."', `module_id`=".(int)$moduleId." ";
        $rs = ip_deprecated_mysql_query($sql);
        if(!$rs){
            trigger_error($sql." ".ip_deprecated_mysql_error());
            return false;
        }else
        return ip_deprecated_mysql_insert_id();
    }

    public static function setModuleTitle($moduleGroupName, $moduleName, $value){
        $sql = "update `".DB_PREF."module_group` mg, `".DB_PREF."module` m
    set m.translation = '".ip_deprecated_mysql_real_escape_string($value)."'
    where m.group_id = mg.id and mg.name = '".ip_deprecated_mysql_real_escape_string($moduleGroupName)."' and m.name = '".ip_deprecated_mysql_real_escape_string($moduleName)."'";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs){
            return true;
        }else{
            trigger_error($sql." ".ip_deprecated_mysql_error());
            return false;
        }
    }

    public static function setParameterGroupTitle($parameterGroupId, $title){
        $sql = "update `".DB_PREF."parameter_group`
    set `translation` = '".ip_deprecated_mysql_real_escape_string($title)."'
    where  `id` = ".(int)$parameterGroupId." ";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs){
            return true;
        }else{
            trigger_error($sql." ".ip_deprecated_mysql_error());
            return false;
        }
    }



    public static function setParameterTitle($moduleGroupName, $moduleName, $parameterGroupName, $parameterName, $value){
        $sql = "update `".DB_PREF."module_group` mg, `".DB_PREF."module` m, `".DB_PREF."parameter_group` pg, `".DB_PREF."parameter` p
    set p.translation = '".ip_deprecated_mysql_real_escape_string($value)."'
    where p.group_id = pg.id and pg.module_id = m.id and m.group_id = mg.id
    and mg.name = '".ip_deprecated_mysql_real_escape_string($moduleGroupName)."' and m.name = '".ip_deprecated_mysql_real_escape_string($moduleName)."' and pg.name = '".ip_deprecated_mysql_real_escape_string($parameterGroupName)."' and p.name = '".ip_deprecated_mysql_real_escape_string($parameterName)."'";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs){
            return true;
        }else{
            trigger_error($sql." ".ip_deprecated_mysql_error());
            return false;
        }
    }

    public static function insertLanguage($languageCode, $languageShort, $languageLong, $languageUrl, $visible, $rowNumber){
        $sql = "insert into `".DB_PREF."language`
    set row_number = ".(int)$rowNumber.", d_short = '".ip_deprecated_mysql_real_escape_string($languageShort)."',  d_long = '".ip_deprecated_mysql_real_escape_string($languageLong)."',  url = '".ip_deprecated_mysql_real_escape_string($languageUrl)."',  code = '".ip_deprecated_mysql_real_escape_string($languageCode)."', visible = ".(int)$visible."";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs){
            return ip_deprecated_mysql_insert_id();
        }else{
            trigger_error($sql." ".ip_deprecated_mysql_error());
            return false;
        }

    }



    public static function getLanguage($id){
        $answer = array();
        $sql = "select * from `".DB_PREF."language` where id = ".(int)$id."";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs){
            if($lock = ip_deprecated_mysql_fetch_assoc($rs)){
                return $lock;
            }else{
                return false;
            }
        }else{
            trigger_error($sql." ".ip_deprecated_mysql_error());
            return false;
        }
        return $answer;
    }

    public static function insertParameter($groupId, $parameter){
        $sql = "insert into `".DB_PREF."parameter`
      set name = '".ip_deprecated_mysql_real_escape_string($parameter['name'])."',
      admin = '".ip_deprecated_mysql_real_escape_string($parameter['admin'])."',
      group_id = ".(int)$groupId.",
      translation = '".ip_deprecated_mysql_real_escape_string($parameter['translation'])."',
      type = '".ip_deprecated_mysql_real_escape_string($parameter['type'])."'";

        $rs = ip_deprecated_mysql_query($sql);
        if($rs){
            $last_insert_id = ip_deprecated_mysql_insert_id();
            switch($parameter['type']){
                case "string_wysiwyg":
                    $sql = "insert into `".DB_PREF."par_string` set `value` = '".ip_deprecated_mysql_real_escape_string($parameter['value'])."', `parameter_id` = ".$last_insert_id."";
                    $rs = ip_deprecated_mysql_query($sql);
                    if(!$rs)
                    trigger_error("Can't insert parameter ".$sql." ".ip_deprecated_mysql_error());
                    break;
                case "string":
                    $sql = "insert into `".DB_PREF."par_string` set `value` = '".ip_deprecated_mysql_real_escape_string($parameter['value'])."', `parameter_id` = ".$last_insert_id."";
                    $rs = ip_deprecated_mysql_query($sql);
                    if(!$rs)
                    trigger_error("Can't insert parameter ".$sql." ".ip_deprecated_mysql_error());
                    break;
                case "integer":
                    $sql = "insert into `".DB_PREF."par_integer` set `value` = ".ip_deprecated_mysql_real_escape_string($parameter['value']).", `parameter_id` = ".$last_insert_id."";
                    $rs = ip_deprecated_mysql_query($sql);
                    if(!$rs)
                    trigger_error("Can't insert parameter ".$sql." ".ip_deprecated_mysql_error());
                    break;
                case "bool":
                    $sql = "insert into `".DB_PREF."par_bool` set `value` = ".ip_deprecated_mysql_real_escape_string($parameter['value']).", `parameter_id` = ".$last_insert_id."";
                    $rs = ip_deprecated_mysql_query($sql);
                    if(!$rs)
                    trigger_error("Can't insert parameter ".$sql." ".ip_deprecated_mysql_error());
                    break;
                case "textarea":
                    $sql = "insert into `".DB_PREF."par_string` set `value` = '".ip_deprecated_mysql_real_escape_string($parameter['value'])."', `parameter_id` = ".$last_insert_id."";
                    $rs = ip_deprecated_mysql_query($sql);
                    if(!$rs)
                    trigger_error("Can't insert parameter ".$sql." ".ip_deprecated_mysql_error());
                    break;

                case "lang":
                    $languages = \Ip\Frontend\Db::getLanguages();
                    foreach($languages as $key => $language){
                        $sql3 = "insert into `".DB_PREF."par_lang` set `translation` = '".ip_deprecated_mysql_real_escape_string($parameter['value'])."', `language_id` = '".$language['id']."', `parameter_id` = ".$last_insert_id." ";
                        $rs3 = ip_deprecated_mysql_query($sql3);
                        if(!$rs3)
                        trigger_error("Can't update parameter ".$sql3." ".ip_deprecated_mysql_error());
                    }
                    break;
                case "lang_textarea":
                    $languages = \Ip\Frontend\Db::getLanguages();
                    foreach($languages as $key => $language){
                        $sql3 = "insert into `".DB_PREF."par_lang` set `translation` = '".ip_deprecated_mysql_real_escape_string($parameter['value'])."', `language_id` = '".$language['id']."', `parameter_id` = ".$last_insert_id." ";
                        $rs3 = ip_deprecated_mysql_query($sql3);
                        if(!$rs3)
                        trigger_error("Can't update parameter ".$sql3." ".ip_deprecated_mysql_error());
                    }
                    break;
                case "lang_wysiwyg":
                    $languages = \Ip\Frontend\Db::getLanguages();
                    foreach($languages as $key => $language){
                        $sql3 = "insert into `".DB_PREF."par_lang` set `translation` = '".ip_deprecated_mysql_real_escape_string($parameter['value'])."', `language_id` = '".$language['id']."', `parameter_id` = ".$last_insert_id." ";
                        $rs3 = ip_deprecated_mysql_query($sql3);
                        if(!$rs3)
                        trigger_error("Can't update parameter ".$sql3." ".ip_deprecated_mysql_error());
                    }
                    break;
            }
        }else{
            trigger_error($sql." ".ip_deprecated_mysql_error());
        }
    }


}



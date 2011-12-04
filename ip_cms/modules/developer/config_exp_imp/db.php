<?php

/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */


namespace Modules\developer\config_exp_imp;

if (!defined('BACKEND')) exit;



class Db {


    public static function createParameterGroup($moduleId, $name, $translation, $admin) {
        $sql = "insert into `".DB_PREF."parameter_group` set `name`='".mysql_real_escape_string($name)."', `translation`='".mysql_real_escape_string($translation)."', `admin`='".(int)$admin."', `module_id`=".(int)$moduleId." ";
        $rs = mysql_query($sql);
        if(!$rs) {
            trigger_error($sql." ".mysql_error());
            return false;
        }else
        return mysql_insert_id();
    }


    public static function getGroup($name) {
        $sql = "select * from `".DB_PREF."module_group` where `name` = '".mysql_real_escape_string($name)."' ";
        $rs = mysql_query($sql);
        if(!$rs) {
            trigger_error($sql." ".mysql_error());
            return false;
        }else {
            if($lock = mysql_fetch_assoc($rs))
            $answer = $lock;
            else
            $answer = false;
            return $answer;
        }
    }

    public static function updateModuleGroupTranslation($moduleGroupId, $newTranslation) {
        $sql = "update `".DB_PREF."module_group` set `translation` = '".mysql_real_escape_string($newTranslation)."' where `id` = ".(int)$moduleGroupId." ";
        $rs = mysql_query($sql);
        if(!$rs) {
            trigger_error($sql." ".mysql_error());
            return false;
        } else {
            return true;
        }
    }


    public static function updateModuleTranslation($moduleId, $newTranslation) {
        $sql = "update `".DB_PREF."module` set `translation` = '".mysql_real_escape_string($newTranslation)."' where `id` = ".(int)$moduleId." ";
        $rs = mysql_query($sql);
        if(!$rs) {
            trigger_error($sql." ".mysql_error());
            return false;
        } else {
            return true;
        }
    }

    public static function getLanguage($languageId) {
        $sql = "select * from `".DB_PREF."language` where `id` = ".(int)$languageId." ";
        $rs = mysql_query($sql);
        if(!$rs) {
            trigger_error($sql." ".mysql_error());
            return false;
        }else {
            if($lock = mysql_fetch_assoc($rs))
            $answer = $lock;
            else
            $answer = false;
            return $answer;
        }
    }



    public static function getParameterGroup($moduleId, $groupName) {
        $sql = "select * from `".DB_PREF."parameter_group` where `module_id` = ".(int)$moduleId." and `name`='".mysql_real_escape_string($groupName)."' ";
        $rs = mysql_query($sql);
        if(!$rs) {
            trigger_error($sql." ".mysql_error());
            return false;
        }else {
            if($lock = mysql_fetch_assoc($rs))
            $answer = $lock;
            else
            $answer = false;
            return $answer;
        }
    }

    public static function insertLanguage($languageData) {
        $all_languages = Db::getLanguages();

        $urls = array();
        foreach($all_languages as $key => $language)
        $urls[$language['url']] = 1;

        while(isset($urls[$languageData['url']]))
        $languageData['url'] .= '-';


        $max_row_number = 0;
        foreach($all_languages as $key => $language)
        $max_row_number = $language['row_number'] + 1;

        $sql = "insert into `".DB_PREF."language` set
    d_short = '".mysql_real_escape_string($languageData['d_short'])."',
    d_long = '".mysql_real_escape_string($languageData['d_long'])."',
    visible = 0,
    row_number = ".$max_row_number.",
    url = '".mysql_real_escape_string($languageData['url'])."',
    code = '".mysql_real_escape_string($languageData['code'])."'
    
    ";
        $rs = mysql_query($sql);
        if(!$rs) {
            trigger_error($sql." ".mysql_error());
            return false;
        }else {
            return mysql_insert_id();
        }
    }


    public static function getLanguageId($languageCode) {
        $sql = "select id from `".DB_PREF."language` where `code` = '".mysql_real_escape_string($languageCode)."' ";
        $rs = mysql_query($sql);
        if(!$rs) {
            trigger_error($sql." ".mysql_error());
            return false;
        }else {
            if($lock = mysql_fetch_assoc($rs))
            $answer = $lock['id'];
            else
            $answer = false;
            return $answer;
        }
    }


    public static function getParameterGroups($moduleId) {
        $sql = "select * from `".DB_PREF."parameter_group` where `module_id` = ".(int)$moduleId." order by name ";
        $rs = mysql_query($sql);
        if(!$rs) {
            trigger_error($sql." ".mysql_error());
            return false;
        }else {
            $answer = array();
            while($lock = mysql_fetch_assoc($rs))
            $answer[] = $lock;
            return $answer;
        }
    }



    public static function getLanguages() {
        $answer = array();
        $sql = "select * from `".DB_PREF."language` where 1 order by row_number";
        $rs = mysql_query($sql);
        if($rs) {
            $answer = array();
            while($lock = mysql_fetch_assoc($rs)) {
                $answer[] = $lock;
            }
            return $answer;
        }else {
            trigger_error($sql." ".mysql_error());
            return false;
        }
    }

    /**
     * @access private
     */
    public static function  getParLang($groupId, $types, $languageId) {
        $answer = array();
        $sql = "select p.*, t.translation as 'value' from `".DB_PREF."parameter` p, `".DB_PREF."par_lang` t where
      p.group_id = ".(int)$groupId." and t.parameter_id = p.id and t.language_id =  '".$languageId."'";
        $rs = mysql_query($sql);
        if($rs) {
            $answer = array();
            while($lock = mysql_fetch_assoc($rs)) {
                $answer[] = $lock;
            }
            return $answer;
        }else {
            trigger_error($sql." ".mysql_error());
            return false;
        }
    }

    /**
     * @access private
     */
    public static function  getParString($groupId, $types) {
        $sql = "select p.*, s.value as 'value' from `".DB_PREF."parameter` p, `".DB_PREF."par_string` s where
      p.group_id = ".(int)$groupId."  and p.id = s.parameter_id";
        $rs = mysql_query($sql);
        if($rs) {
            $answer = array();
            while($lock = mysql_fetch_assoc($rs)) {
                $answer[] = $lock;
            }
            return $answer;
        }else {
            trigger_error($sql." ".mysql_error());
            return false;
        }


    }


    /**
     * @access private
     */
    public static function  getParInteger($groupId, $types) {
        $answer = array();
        $sql = "select p.*, s.value as 'value' from `".DB_PREF."parameter` p, `".DB_PREF."par_integer` s where
      p.group_id = ".(int)$groupId."  and p.id = s.parameter_id";
        $rs = mysql_query($sql);
        if($rs) {
            while($lock = mysql_fetch_assoc($rs)) {
                $answer[] = $lock;
            }
        }else trigger_error($sql." ".mysql_error());
        return $answer;

    }

    /**
     * @access private
     */
    public static function  getParBool($groupId, $types) {
        $answer = array();
        $sql = "select p.*, s.value as 'value' from `".DB_PREF."parameter` p, `".DB_PREF."par_bool` s where
      p.group_id = ".(int)$groupId."  and p.id = s.parameter_id";
        $rs = mysql_query($sql);
        if($rs) {
            while($lock = mysql_fetch_assoc($rs)) {
                $answer[] = $lock;
            }
        }else trigger_error($sql." ".mysql_error());
        return $answer;


    }


    public static function deleteParameter($id, $type) {
        if($type == 'string_wysiwyg') {
            $sql = "delete from `".DB_PREF."par_string` where `parameter_id` = '".$id."' ";
            $rs = mysql_query($sql);
            if(!$rs)
            trigger_error("Can't delete parameter ".$sql." ".mysql_error());
        }
        if($type == 'string') {
            $sql = "delete from `".DB_PREF."par_string` where `parameter_id` = '".$id."' ";
            $rs = mysql_query($sql);
            if(!$rs)
            trigger_error("Can't delete parameter ".$sql." ".mysql_error());
        }


        if($type == 'textarea') {
            $sql = "delete from `".DB_PREF."par_string` where `parameter_id` = '".(int)$id."' ";
            $rs = mysql_query($sql);
            if(!$rs)
            trigger_error("Can't delete parameter ".$sql." ".mysql_error());
        }
        if($type == 'bool') {
            $sql = "delete from `".DB_PREF."par_bool` where `parameter_id` = '".(int)$id."' ";
            $rs = mysql_query($sql);
            if(!$rs)
            trigger_error("Can't delete parameter ".$sql." ".mysql_error());
        }

        if($type == 'integer') {
            $sql = "delete from `".DB_PREF."par_integer` where `parameter_id` = '".(int)$id."' ";
            $rs = mysql_query($sql);
            if(!$rs)
            trigger_error("Can't delete parameter ".$sql." ".mysql_error());
        }
        if($type == 'lang') {
            $languages = Db::getLanguages();

            $sql2 = "delete from `".DB_PREF."par_lang` where `parameter_id` = '".(int)$id."' ";
            $rs2 = mysql_query($sql2);
            if(!$rs2)
            trigger_error("Can't delete parameter ".$sql2." ".mysql_error());

        }

        if($type == 'lang_textarea') {
            $languages = Db::getLanguages();

            $sql2 = "delete from `".DB_PREF."par_lang` where `parameter_id` = '".(int)$id."' ";
            $rs2 = mysql_query($sql2);
            if(!$rs2)
            trigger_error("Can't delete parameter ".$sql2." ".mysql_error());

        }

        if($type == 'lang_wysiwyg') {
            $languages = Db::getLanguages();

            $sql2 = "delete from `".DB_PREF."par_lang` where `parameter_id` = '".(int)$id."' ";
            $rs2 = mysql_query($sql2);
            if(!$rs2)
            trigger_error("Can't delete parameter ".$sql2." ".mysql_error());

        }

        $sql = "delete from `".DB_PREF."parameter` where `id` = '".(int)$id."' ";
        $rs = mysql_query($sql);
        if(!$rs) {
            trigger_error($sql." ".mysql_error());
            return false;
        }else
        return true;

    }

    public static function renameParameter($id, $translation) {
        $sql = "update `".DB_PREF."parameter` set `translation` = '".mysql_real_escape_string($translation)."' where `id` = ".(int)$id." ";
        $rs = mysql_query($sql);
        if(!$rs) {
            trigger_error($sql." ".mysql_error());
            return false;
        } else {
            return true;
        }
    }


    public static function renameParameterGroup($id, $translation) {
        $sql = "update `".DB_PREF."parameter_group` set `translation` = '".mysql_real_escape_string($translation)."' where `id` = ".(int)$id." ";
        $rs = mysql_query($sql);
        if(!$rs) {
            trigger_error($sql." ".mysql_error());
            return false;
        } else {
            return true;
        }
    }


    public static function insertParameter($groupId, $parameter) {
        $sql = "insert into `".DB_PREF."parameter`
      set `name` = '".mysql_real_escape_string($parameter['name'])."',
      `admin` = '".mysql_real_escape_string($parameter['admin'])."',
      `group_id` = ".(int)$groupId.",
      `translation` = '".mysql_real_escape_string($parameter['translation'])."',
      `comment` = '".mysql_real_escape_string($parameter['comment'])."',
      `type` = '".mysql_real_escape_string($parameter['type'])."'";

        $rs = mysql_query($sql);
        if($rs) {
            $last_insert_id = mysql_insert_id();
            switch($parameter['type']) {
                case "string_wysiwyg":
                    $sql = "insert into `".DB_PREF."par_string` set `value` = '".mysql_real_escape_string($parameter['value'])."', `parameter_id` = ".$last_insert_id."";
                    $rs = mysql_query($sql);
                    if(!$rs)
                    trigger_error("Can't insert parameter ".$sql." ".mysql_error());
                    break;
                case "string":
                    $sql = "insert into `".DB_PREF."par_string` set `value` = '".mysql_real_escape_string($parameter['value'])."', `parameter_id` = ".$last_insert_id."";
                    $rs = mysql_query($sql);
                    if(!$rs)
                    trigger_error("Can't insert parameter ".$sql." ".mysql_error());
                    break;
                case "integer":
                    $sql = "insert into `".DB_PREF."par_integer` set `value` = ".mysql_real_escape_string($parameter['value']).", `parameter_id` = ".$last_insert_id."";
                    $rs = mysql_query($sql);
                    if(!$rs)
                    trigger_error("Can't insert parameter ".$sql." ".mysql_error());
                    break;
                case "bool":
                    $sql = "insert into `".DB_PREF."par_bool` set `value` = ".mysql_real_escape_string($parameter['value']).", `parameter_id` = ".$last_insert_id."";
                    $rs = mysql_query($sql);
                    if(!$rs)
                    trigger_error("Can't insert parameter ".$sql." ".mysql_error());
                    break;
                case "textarea":
                    $sql = "insert into `".DB_PREF."par_string` set `value` = '".mysql_real_escape_string($parameter['value'])."', `parameter_id` = ".$last_insert_id."";
                    $rs = mysql_query($sql);
                    if(!$rs)
                    trigger_error("Can't insert parameter ".$sql." ".mysql_error());
                    break;

                case "lang":
                    $languages = Db::getLanguages();
                    foreach($languages as $key => $language) {
                        $sql3 = "insert into `".DB_PREF."par_lang` set `translation` = '".mysql_real_escape_string($parameter['value'])."', `language_id` = '".$language['id']."', `parameter_id` = ".$last_insert_id." ";
                        $rs3 = mysql_query($sql3);
                        if(!$rs3)
                        trigger_error("Can't update parameter ".$sql3." ".mysql_error());
                    }
                    break;
                case "lang_textarea":
                    $languages = Db::getLanguages();
                    foreach($languages as $key => $language) {
                        $sql3 = "insert into `".DB_PREF."par_lang` set `translation` = '".mysql_real_escape_string($parameter['value'])."', `language_id` = '".$language['id']."', `parameter_id` = ".$last_insert_id." ";
                        $rs3 = mysql_query($sql3);
                        if(!$rs3)
                        trigger_error("Can't update parameter ".$sql3." ".mysql_error());
                    }
                    break;
                case "lang_wysiwyg":
                    $languages = Db::getLanguages();
                    foreach($languages as $key => $language) {
                        $sql3 = "insert into `".DB_PREF."par_lang` set `translation` = '".mysql_real_escape_string($parameter['value'])."', `language_id` = '".$language['id']."', `parameter_id` = ".$last_insert_id." ";
                        $rs3 = mysql_query($sql3);
                        if(!$rs3)
                        trigger_error("Can't update parameter ".$sql3." ".mysql_error());
                    }
                    break;
            }
        }else {
            trigger_error($sql." ".mysql_error());
        }
    }
}


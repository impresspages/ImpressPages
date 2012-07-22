<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Frontend;

if (!defined('CMS')) exit;

/**
 * Class for common tasks with database
 * @package ImpressPages
 */
class Db {

    /**
     * @param int $language_id
     * @return array all website zones with meta tags for specified language
     */
    public static function getZones($languageId) {
        $sql = "select m.*, p.url, p.description, p.keywords, p.title from `".DB_PREF."zone` m,`".DB_PREF."zone_parameter` p where p.zone_id = m.id and p.language_id = '".mysql_real_escape_string($languageId)."' order by m.row_number";
        $rs = mysql_query($sql);
        if($rs) {
            $zones = array();
            while($lock = mysql_fetch_assoc($rs))
            $zones[$lock['name']] = $lock;
        } else {
            trigger_error("Can't get all zones ".$sql." ");
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
        $sql = "select * from `".DB_PREF."language` where `d_short` = '".mysql_real_escape_string($url)."' ";
        $rs = mysql_query($sql);
        if($rs) {
            if($lock = mysql_fetch_assoc($rs))
            return $lock;
        }else
        trigger_error($sql." ".mysql_error());
    }


    /**
     *
     * @param int $id
     * @return array language attributes
     */
    public static function getLanguageById($languageId) {
        $sql = "select * from `".DB_PREF."language` where `id` = '".$languageId."' ";
        $rs = mysql_query($sql);
        if($rs) {
            if($lock = mysql_fetch_assoc($rs))
            return $lock;
        }else
        trigger_error($sql." ".mysql_error());
    }

    /**
     * Finds first language of website
     * @return array
     */
    public static function getFirstLanguage() {
        $sql = "select * from `".DB_PREF."language` where visible order by row_number ";
        $rs = mysql_query($sql);
        if($rs) {
            if($lock = mysql_fetch_assoc($rs))
            return $lock;
        }else
        trigger_error($sql." ".mysql_error());

    }


    /**
     *
     * @return array all visible website languages
     */
    public static function getLanguages($includeHidden = false) {
        $answer = array();
        if($includeHidden) {
            $sql = "select * from `".DB_PREF."language` where 1 order by row_number";
        }else {
            $sql = "select * from `".DB_PREF."language` where visible order by row_number";
        }
        $rs = mysql_query($sql);
        if($rs) {
            while($lock = mysql_fetch_assoc($rs))
            $answer[$lock['id']] = $lock;
        }else {
            trigger_error($sql." ".mysql_error());
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

        $rs = mysql_query($sql);
        if (!$rs) {
            trigger_error($sql." ".mysql_error());
            return false;
        }

        $answer = array();
        while($lock = mysql_fetch_assoc($rs)) {
            $answer[] = $lock;
        }
        return $answer;
    }

}

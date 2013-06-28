<?php
/**
 * @package ImpressPages
 *
 *
 */


if (!defined('FRONTEND')&&!defined('BACKEND')) exit;



/**
 * db class to make system operations
 * Provide some general functions.
 * @package ImpressPages
 */
class DbSystem{    //system variables

    /**
     * @access private
     */
    public static function setSystemVariable($name, $value){
        if (self::getSystemVariable($name) !== FALSE) {
            $sql = "update `".DB_PREF."variables` set `value` = '".mysql_real_escape_string($value)."' where
        `name` = '".mysql_real_escape_string($name)."'";
            $rs = mysql_query($sql);
            if (!$rs) {
                trigger_error($sql." ".mysql_error());
                return false;
            }
        } else {
            self::insertSystemVariable($name, $value);
        }
    }

    /**
     * @access private
     */
    public static function getSystemVariable($name){
        $sql = "select value from `".DB_PREF."variables`  where `name` = '".mysql_real_escape_string($name)."'";
        $rs = mysql_query($sql);
        if ($rs) {
            if ($lock = mysql_fetch_assoc($rs)) {
                return $lock['value'];
            } else {
                throw new \Ip\CoreException("Unknown system variable ".$name, \Ip\CoreException::SYSTEM_VARIABLE);
            }
        } else {
            trigger_error($sql." ".mysql_error());
            return false;
        }
    }

    /**
     * @access private
     */
    public static function insertSystemVariable($name, $value){
        $sql = "insert into `".DB_PREF."variables` set `value` = '".mysql_real_escape_string($value)."', `name` = '".mysql_real_escape_string($name)."'";
        $rs = mysql_query($sql);
        if (!$rs) {
            trigger_error($sql." ".mysql_error());
            return false;
        }
    }

    //end system variables


    public static function replaceUrls($oldUrl, $newUrl){

        if ($oldUrl == '' || $newUrl == '') {
            trigger_error('Can\'t update URL');
        }

        $oldUrlParts = explode('?', $oldUrl);
        $oldUrl = $oldUrlParts[0];

        $newUrlParts = explode('?', $newUrl);
        $newUrl = $newUrlParts[0];

        $sql = "update `".DB_PREF."par_string` set value = REPLACE(`value`, '".mysql_real_escape_string($oldUrl)."', '".mysql_real_escape_string($newUrl)."') where 1";
        $rs = mysql_query($sql);
        
        if (!$rs) {
            trigger_error($sql." ".mysql_error());
            return false;
        }


        $sql = "update `".DB_PREF."par_lang` set translation = REPLACE(`translation`, '".mysql_real_escape_string($oldUrl)."', '".mysql_real_escape_string($newUrl)."') where 1";
        $rs = mysql_query($sql);
        if (!$rs) {
            trigger_error($sql." ".mysql_error());
            return false;
        }

        
        $fromJsonUrl = json_encode($oldUrl);
        $fromJsonUrl = substr($fromJsonUrl, 1, -1);
        $toJsonUrl = json_encode($newUrl);
        $toJsonUrl = substr($toJsonUrl, 1, -1);
        
        $sql = "
        UPDATE 
            `".DB_PREF."m_content_management_widget` 
        SET 
            `data` = REPLACE(`data`, '".mysql_real_escape_string($fromJsonUrl)."', '".mysql_real_escape_string($toJsonUrl)."') where 1";
        global $log;
        $rs = mysql_query($sql);
        if ($rs) {
            return true;
        } else {
            trigger_error($sql." ".mysql_error());
            return false;
        }
        
        
        return true;
    }




}
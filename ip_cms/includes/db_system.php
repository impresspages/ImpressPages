<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
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
      $sql = "update ".DB_PREF."variables set `value` = '".mysql_real_escape_string($value)."' where
      `name` = '".mysql_real_escape_string($name)."'";
      $rs = mysql_query($sql);
      if (!$rs) {
        trigger_error($sql." ".mysql_error());
        return false;
      }
    }    
	
    /**
     * @access private
     */	
    public static function getSystemVariable($name){
      $sql = "select value from ".DB_PREF."variables  where `name` = '".mysql_real_escape_string($name)."'";
      $rs = mysql_query($sql);
      if ($rs) {
        if ($lock = mysql_fetch_assoc($rs)) {
          return $lock['value'];
        } else
          return false;
      } else {
        trigger_error($sql." ".mysql_error());
        return false;
      }
    }    
    
    /**
     * @access private
     */
    public static function insertSystemVariable($name, $value){
      $sql = "insert into ".DB_PREF."variables set `value` = '".mysql_real_escape_string($value)."', `name` = '".mysql_real_escape_string($name)."'";
      $rs = mysql_query($sql);
      if (!$rs) {
        trigger_error($sql." ".mysql_error());
        return false;
      }
    }        
    
    //end system variables
  
  
    public static function replaceUrls($oldUrl, $newUrl){
      $sql = "update ".DB_PREF."par_string set value = REPLACE(`value`, '".mysql_real_escape_string($oldUrl)."', '".mysql_real_escape_string($newUrl)."') where 1";
      $rs = mysql_query($sql);
      if ($rs) {
      
        $sql2 = "update ".DB_PREF."par_lang set translation = REPLACE(`translation`, '".mysql_real_escape_string($oldUrl)."', '".mysql_real_escape_string($newUrl)."') where 1";
        $rs2 = mysql_query($sql2);
        if ($rs2) {
          return true;
        } else {
          trigger_error($sql2." ".mysql_error());
          return false;
        }
      
        return true;
      } else {
        trigger_error($sql." ".mysql_error());
        return false;
      }
    }     
    
}
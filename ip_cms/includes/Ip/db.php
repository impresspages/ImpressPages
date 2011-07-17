<?php 
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

namespace Ip;

 
if (!defined('CMS')) exit;  



/**
 * 
 * View class
 * 
 */ 
class Db{
    public static function getLastRevision($zoneName, $pageId) {
        $sql = "
            SELECT * FROM `".DB_PREF."revision`
            WHERE
                `zoneName` = '".mysql_real_escape_string($zoneName)."' AND
                `pageId` = '".(int)$pageId."'
            ORDER BY `created` DESC
            LIMIT 1
        ";    
        
        $rs = mysql_query($sql);
        if (!$rs){
            throw new \Exception('Can\'t find last revision '.$sql.' '.mysql_error());
        }
        
        if ($lock = mysql_fetch_assoc($rs)) {
            return $lock;
        } else {
            return false;
        }       
        
    }
    
    public static function getRevision($revisionId) {
        $sql = "
            SELECT * FROM `".DB_PREF."revision`
            WHERE `id` = ".(int)$revisionId."
        ";    
        
        $rs = mysql_query($sql);
        if (!$rs){
            throw new \Exception('Can\'t find revision '.$sql.' '.mysql_error());
        }
        
        if ($lock = mysql_fetch_assoc($rs)) {
            return $lock;
        } else {
            return false;
        }       
        
    }
        
    
    public static function createRevision ($zoneName, $pageId) {
        $sql = "
            INSERT INTO `".DB_PREF."revision`
            SET
                `zoneName` = '".mysql_real_escape_string($zoneName)."',
                `pageId` = '".(int)$pageId."',
                `published` = 0,
                `created` = ".time()."
        ";    
        
        $rs = mysql_query($sql);
        if (!$rs){
            throw new \Exception('Can\'t create new revision '.$sql.' '.mysql_error());
        }
        
        return mysql_insert_id();        
    }       

    public static function getPageRevisions($zoneName, $pageId) {
        $sql = "
            SELECT * FROM `".DB_PREF."revision`
            WHERE `pageId` = ".(int)$pageId." AND `zoneName` = '".mysql_real_escape_string($zoneName)."'
        ";    
        
        $rs = mysql_query($sql);
        if (!$rs){
            throw new \Exception('Can\'t find revision '.$sql.' '.mysql_error());
        }
        
        $answer = array();
        while ($lock = mysql_fetch_assoc($rs)) {
            $answer[] = $lock;
        }
        return $answer;
        
    }
            
    
}
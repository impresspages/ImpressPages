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
        //ordering by id is required because sometimes two revisions might be created at excatly the same time
        $sql = "
            SELECT * FROM `".DB_PREF."revision`
            WHERE
                `zoneName` = '".mysql_real_escape_string($zoneName)."' AND
                `pageId` = '".(int)$pageId."'
            ORDER BY `created` DESC, `id` DESC
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
    
    public static function getPublishedRevision($zoneName, $pageId) {
        //ordering by id is required because sometimes two revisions might be created at excatly the same time
        $sql = "
            SELECT * FROM `".DB_PREF."revision`
            WHERE
                `zoneName` = '".mysql_real_escape_string($zoneName)."' AND
                `pageId` = '".(int)$pageId."' AND
                `published`
            ORDER BY `created` DESC, `id` DESC
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
        
    
    public static function createRevision ($zoneName, $pageId, $published) {
        global $dispatcher;
        $sql = "
            INSERT INTO `".DB_PREF."revision`
            SET
                `zoneName` = '".mysql_real_escape_string($zoneName)."',
                `pageId` = '".(int)$pageId."',
                `published` = ".(int)$published.",
                `created` = ".time()."
        ";   

        $rs = mysql_query($sql);
        if (!$rs){
            throw new \Exception('Can\'t create new revision '.$sql.' '.mysql_error());
        }

        $revisionId = mysql_insert_id();
        
        $eventData = array(
            'revisionId' => $revisionId
        );
        $dispatcher->notify(new \Ip\Event(null, 'site.createdRevision', $eventData));    
        
        
        
        return $revisionId;        
    }       

    public static function duplicateRevision ($oldRevisionId) {
        global $dispatcher;
        
        $oldRevision = self::getRevision($oldRevisionId);
        $newRevisionId = self::createRevision($oldRevision['zoneName'], $oldRevision['pageId'], 0);
        
        $eventData = array(
            'newRevisionId' => $newRevisionId,
            'basedOn' => $oldRevisionId 
        );
        
        $dispatcher->notify(new \Ip\Event(null, 'site.duplicatedRevision', $eventData));    
            
        return $newRevisionId;        
    }       
    
    
    public static function getPageRevisions($zoneName, $pageId) {
        $sql = "
            SELECT * FROM `".DB_PREF."revision`
            WHERE `pageId` = ".(int)$pageId." AND `zoneName` = '".mysql_real_escape_string($zoneName)."'
            ORDER BY `created` DESC
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
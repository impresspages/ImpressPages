<?php
/**
 * @package ImpressPages

 *
 */

namespace Ip;

/**
 *
 * View class
 *
 */
class Revision{
    
    public static function getLastRevision($zoneName, $pageId) {
        //ordering by id is required because sometimes two revisions might be created at excatly the same time
        $sql = "
            SELECT * FROM `".DB_PREF."revision`
            WHERE
                `zoneName` = '".ip_deprecated_mysql_real_escape_string($zoneName)."' AND
                `pageId` = '".(int)$pageId."'
            ORDER BY `created` DESC, `revisionId` DESC
            LIMIT 1
        ";    

        $rs = ip_deprecated_mysql_query($sql);
        if (!$rs){
            throw new CoreException('Can\'t find last revision '.$sql.' '.ip_deprecated_mysql_error(), CoreException::DB);
        }

        if ($lock = ip_deprecated_mysql_fetch_assoc($rs)) {
            return $lock;
        } else {
            $revisionId = self::createRevision($zoneName, $pageId, 1);
            return self::getRevision($revisionId);
        }

    }

    public static function getPublishedRevision($zoneName, $pageId) {
        //ordering by id is required because sometimes two revisions might be created at excatly the same time
        $sql = "
            SELECT * FROM `".DB_PREF."revision`
            WHERE
                `zoneName` = '".ip_deprecated_mysql_real_escape_string($zoneName)."' AND
                `pageId` = '".(int)$pageId."' AND
                `published`
            ORDER BY `created` DESC, `revisionId` DESC
            LIMIT 1
        ";    

        $rs = ip_deprecated_mysql_query($sql);
        if (!$rs){
            throw new CoreException('Can\'t find last revision '.$sql.' '.ip_deprecated_mysql_error(), CoreException::DB);
        }

        if ($lock = ip_deprecated_mysql_fetch_assoc($rs)) {
            return $lock;
        } else {
            $revisionId = self::createRevision($zoneName, $pageId, 1);
            return self::getRevision($revisionId);
        }

    }

    public static function getRevision($revisionId) {
        $sql = "
            SELECT * FROM `".DB_PREF."revision`
            WHERE `revisionId` = ".(int)$revisionId."
        ";    

        $rs = ip_deprecated_mysql_query($sql);
        if (!$rs){
            throw new CoreException('Can\'t find revision '.$sql.' '.ip_deprecated_mysql_error(), CoreException::DB);
        }

        if ($lock = ip_deprecated_mysql_fetch_assoc($rs)) {
            return $lock;
        } else {
            return false;
        }

    }


    public static function createRevision ($zoneName, $pageId, $published) {

        $revision = array(
            'zoneName' => $zoneName,
            'pageId' => $pageId,
            'published' => (int)$published,
            'created' => time(),
        );

        $revisionId = ipDb()->insert('revision', $revision);
        $revision['id'] = $revisionId;

        ipDispatcher()->notify('site.createdRevision', array('revision' => $revision));

        return $revisionId;
    }

    public static function publishRevision ($revisionId) {
        $revision = self::getRevision($revisionId);
        if (!$revision) {
            return false;
        }

        $wasUpdated = ipDb()->update('revision',
            array(
                'published' => 1,
                'revisionId' => $revisionId,
            ),
            array(
                'zoneName' => $revision['zoneName'],
                'pageId' => (int)$revision['pageId'],
            )
        );
         
        if (!$wasUpdated) {
            throw new CoreException("Can't publish page #{$revision['pageId']} revision #{$revisionId}", CoreException::DB);
        }
        
        ipDispatcher()->notify('site.publishRevision', array('revisionId' => $revisionId));
    }

    public static function duplicateRevision ($oldRevisionId, $zoneName = null, $pageId = null, $published = null) {

        $oldRevision = self::getRevision($oldRevisionId);
        
        if (!$oldRevision) {
            throw new \Ip\CoreException("Can't find old revision: ".$oldRevisionId, \Ip\CoreException::REVISION);
        }
        
        if ($zoneName !== null) {
            $oldRevision['zoneName'] = $zoneName;
        }
        if ($pageId !== null) {
            $oldRevision['pageId'] = $pageId;
        }
        
        $newRevisionId = self::createRevision($oldRevision['zoneName'], $oldRevision['pageId'], 0);

        if ($published !== null) {
            self::publishRevision($newRevisionId);
        }
        
        
        $eventData = array(
            'newRevisionId' => $newRevisionId,
            'basedOn' => $oldRevisionId 
        );
        ipDispatcher()->notify('site.duplicatedRevision', $eventData);

        return $newRevisionId;
    }


    public static function getPageRevisions($zoneName, $pageId) {
        $table = ipTable('revision');
        $sql = "
            SELECT * FROM $table
            WHERE `pageId` = :pageId AND `zoneName` = :zoneName
            ORDER BY `created` DESC, `revisionId` DESC
        ";

        $revisions = ipDb()->fetchAll($sql, array(
                'pageId' => $pageId,
                'zoneName' => $zoneName,
            ));

        if (!$revisions) {
            throw new CoreException("Can\'t get page #{$pageId} revisions.", CoreException::DB);
        }

        return $revisions;
    }

    /**
     * 
     * Delete all not published revisions that are older than X days. 
     * @param int $days
     */
    public static function removeOldRevisions($days)
    {
        $table = ipTable('revision');

        $sql = "
            SELECT `id` FROM $table
            WHERE `created` < ? AND `published` = 0
        ";

        $revisionList = ipDb()->fetchColumn($sql, array(time() - $days * 24 * 60 * 60));

        $dispatcher = ipDispatcher();

        foreach ($revisionList as $revisionId) {
            $eventData = array(
                'revisionId' => $revisionId,
            );
            $dispatcher->notify('site.removeRevision', $eventData);
            ipDb()->delete('revision', array('id' => $revisionId));
        }
    }

}
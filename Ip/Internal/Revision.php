<?php
/**
 * @package ImpressPages

 *
 */

namespace Ip\Internal;

/**
 *
 * View class
 *
 */
class Revision{
    
    public static function getLastRevision($zoneName, $pageId) {
        //ordering by id is required because sometimes two revisions might be created at excatly the same time
        $revisionTable = ipTable('revision');
        $sql = "
            SELECT * FROM $revisionTable
            WHERE
                `zoneName` = :zoneName AND
                `pageId` = :pageId
            ORDER BY `created` DESC, `revisionId` DESC
        ";

        $revision = ipDb()->fetchRow($sql, array('pageId' => $pageId, 'zoneName' => $zoneName));

        if (!$revision) {
            $revisionId = self::createRevision($zoneName, $pageId, 1);
            $revision = self::getRevision($revisionId);
        }

        return $revision;
    }

    public static function getPublishedRevision($zoneName, $pageId) {
        //ordering by id is required because sometimes two revisions might be created at excatly the same time
        $revisionTable = ipTable('revision');
        $sql = "
            SELECT * FROM $revisionTable
            WHERE
                `zoneName` = ? AND
                `pageId` = ? AND
                `published` = 1
            ORDER BY `created` DESC, `revisionId` DESC
        ";

        $revision = ipDb()->fetchRow($sql, array($zoneName, $pageId));

        if (!$revision) {
            $revisionId = self::createRevision($zoneName, $pageId, 1);
            $revision = self::getRevision($revisionId);
        }

        return $revision;
    }

    public static function getRevision($revisionId) {

        return ipDb()->fetchRow("SELECT * FROM " . ipTable('revision') . " WHERE revisionId = :revisionId ", array('revisionId' => $revisionId));
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

        ipEvent('ipPageRevisionCreated', array('revision' => $revision));

        return $revisionId;
    }

    public static function publishRevision ($revisionId) {
        $revision = self::getRevision($revisionId);
        if (!$revision) {
            return false;
        }

        ipDb()->update('revision',
            array(
                'published' => 0
            ),
            array(
                'zoneName' => $revision['zoneName'],
                'pageId' => (int)$revision['pageId'],
            )
        );
        $wasUpdated = ipDb()->update('revision',
            array(
                'published' => 1
            ),
            array(
                'revisionId' => $revisionId
            )
        );


        if (!$wasUpdated) {
            throw new \Ip\Exception\Db("Can't publish page #{$revision['pageId']} revision #{$revisionId}");
        }
        
        ipEvent('ipPageRevisionPublished', array('revisionId' => $revisionId));
    }

    public static function duplicateRevision ($oldRevisionId, $zoneName = null, $pageId = null, $published = null) {

        $oldRevision = self::getRevision($oldRevisionId);
        
        if (!$oldRevision) {
            throw new \Ip\Exception\Revision("Can't find old revision: ".$oldRevisionId);
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
        ipEvent('ipPageRevisionDuplicated', $eventData);

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

        $where = array(
           'pageId' => $pageId,
            'zoneName' => $zoneName,
        );
        $revisions = ipDb()->selectAll('*', 'revision', $where, 'ORDER BY `created` DESC, `revisionId` DESC');

        if (!$revisions) {
            return array();
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
            SELECT `revisionId` FROM $table
            WHERE `created` < ? AND `published` = 0
        ";

        $revisionList = ipDb()->fetchColumn($sql, array(time() - $days * 24 * 60 * 60));

        $dispatcher = \Ip\ServiceLocator::dispatcher();

        foreach ($revisionList as $revisionId) {
            $eventData = array(
                'revisionId' => $revisionId,
            );
            $dispatcher->event('ipPageRevisionRemoved', $eventData);
            ipDb()->delete('revision', array('id' => $revisionId));
        }
    }

}
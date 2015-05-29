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
class Revision
{

    public static function getLastRevision($pageId)
    {
        if (empty($pageId)) {
            return null;
        }

        //ordering by id is required because sometimes two revisions might be created at exactly the same time
        $revisionTable = ipTable('revision');
        $sql = "
            SELECT * FROM $revisionTable
            WHERE
                `pageId` = :pageId
            ORDER BY `createdAt` DESC, `revisionId` DESC
        ";

        $revision = ipDb()->fetchRow($sql, array('pageId' => $pageId));

        if (!$revision) {
            $revisionId = self::createRevision($pageId, 1);
            $revision = self::getRevision($revisionId);
        }

        return $revision;
    }

    public static function getPublishedRevision($pageId)
    {
        assert('$pageId > 0');
        //ordering by id is required because sometimes two revisions might be created at excatly the same time
        $revisionTable = ipTable('revision');
        $sql = "
            SELECT * FROM $revisionTable
            WHERE
                `pageId` = ? AND
                `isPublished` = 1
            ORDER BY `createdAt` DESC, `revisionId` DESC
        ";

        $revision = ipDb()->fetchRow($sql, array($pageId));

        if (!$revision) {
            $revisionId = self::createRevision($pageId, 1);
            $revision = self::getRevision($revisionId);
        }

        return $revision;
    }

    public static function getRevision($revisionId)
    {

        return ipDb()->selectRow('revision', '*', array('revisionId' => $revisionId));
    }


    public static function createRevision($pageId, $published)
    {

        assert('$pageId > 0');

        $revision = array(
            'pageId' => $pageId,
            'isPublished' => (int)$published,
            'createdAt' => date('Y-m-d H:i:s'),
        );

        $revisionId = ipDb()->insert('revision', $revision);
        $revision['id'] = $revisionId;

        ipEvent('ipPageRevisionCreated', array('revision' => $revision));

        return $revisionId;
    }

    public static function publishRevision($revisionId)
    {
        $revision = self::getRevision($revisionId);
        if (!$revision) {
            return false;
        }

        ipDb()->update(
            'revision',
            array(
                'isPublished' => 0
            ),
            array(
                'pageId' => (int)$revision['pageId'],
            )
        );
        $wasUpdated = ipDb()->update(
            'revision',
            array(
                'isPublished' => 1
            ),
            array(
                'revisionId' => $revisionId
            )
        );


        if (!$wasUpdated) {
            throw new \Ip\Exception\Db("Can't publish page #" . esc($revision['pageId']) . " revision #" . esc(
                $revisionId
            ) . "");
        }

        ipEvent('ipPageRevisionPublished', array('revisionId' => $revisionId));
        return null;
    }

    public static function duplicateRevision($oldRevisionId, $pageId = null, $published = null)
    {

        $oldRevision = self::getRevision($oldRevisionId);

        if (!$oldRevision) {
            throw new \Ip\Exception\Revision("Can't find old revision: " . esc($oldRevisionId));
        }

        if ($pageId !== null) {
            $oldRevision['pageId'] = $pageId;
        }

        $newRevisionId = self::createRevision($oldRevision['pageId'], 0);

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


    public static function getPageRevisions($pageId)
    {
        $where = array(
            'pageId' => $pageId,
        );

        return ipDb()->selectAll('revision', '*', $where, 'ORDER BY `createdAt` DESC, `revisionId` DESC');
    }

    /**
     *
     * Delete all not published revisions that are older than X days.
     * @param int $days
     */
    public static function removeOldRevisions($days)
    {
        //
        // 1) Dynamic Widgets (including revisions)
        // Dynamic widgets have an associated revision. 
        // That revision's creation time and publication 
        // state indicates if a widget should be removed  
        // or not from corresponding db table 'ip_widget'.
        //
        $table = ipTable('revision');

        $sql = "
            SELECT `revisionId` FROM $table
            WHERE (" . ipDb()->sqlMinAge('createdAt', $days * 24, 'HOUR') .") AND `isPublished` = 0
        ";

        $revisionList = ipDb()->fetchColumn($sql);

        foreach ($revisionList as $revisionId) {
            \Ip\Internal\Content\Service::removeRevision($revisionId);
        }

        //
        // 2) Static Widgets (from static blocks only!)
        // Static widgets are presisted with revisionId=0.
        // Therefore, we've to make the time check on widget's
        // 'createdAt' column combined with 'isDeleted=1' flag 
        // and 'revisionId=0' indicating widget's removal state. 
        //
        $table = ipTable('widget');

        $sql = $sql = "
            SELECT `id` FROM $table
            WHERE (" . ipDb()->sqlMinAge('createdAt', $days * 24, 'HOUR') .") 
            AND `revisionId` = 0 AND `isDeleted` = 1 AND `deletedAt` IS NOT NULL
        ";

        $staticWidgetList = ipDb()->fetchColumn($sql);

        foreach ($staticWidgetList as $staticWidgetId) {
            \Ip\Internal\Content\Service::removeWidget($staticWidgetId);
        }
    }

}

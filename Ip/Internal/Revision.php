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

    public static function getPublishedRevision($pageId) {
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

    public static function getRevision($revisionId) {

        return ipDb()->selectRow('revision', '*', array('revisionId' => $revisionId));
    }


    public static function createRevision ($pageId, $published) {

        assert('$pageId > 0');

        $revision = array(
            'pageId' => $pageId,
            'isPublished' => (int)$published,
            'createdAt' => time(),
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
                'isPublished' => 0
            ),
            array(
                'pageId' => (int)$revision['pageId'],
            )
        );
        $wasUpdated = ipDb()->update('revision',
            array(
                'isPublished' => 1
            ),
            array(
                'revisionId' => $revisionId
            )
        );


        if (!$wasUpdated) {
            throw new \Ip\Exception\Db("Can't publish page #" . esc($revision['pageId']) . " revision #" . esc($revisionId) . "");
        }

        ipEvent('ipPageRevisionPublished', array('revisionId' => $revisionId));
    }

    public static function duplicateRevision ($oldRevisionId, $pageId = null, $published = null) {

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
        $table = ipTable('revision');

        $sql = "
            SELECT `revisionId` FROM $table
            WHERE `createdAt` < ? AND `isPublished` = 0
        ";

        $revisionList = ipDb()->fetchColumn($sql, array(time() - $days * 24 * 60 * 60));

        foreach ($revisionList as $revisionId) {
            \Ip\Internal\Content\Service::removeRevision($revisionId);
        }
    }

}

<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal\Content;


class InstanceModel
{




    public static function getInstance($instanceId)
    {
        return ipDb()->selectRow('widgetInstance', '*', array('id' => $instanceId));
    }

    public static function updateInstance($instanceId, $data)
    {
        return ipDb()->update('widgetInstance', $data, array('id' => $instanceId));
    }



    public static function addInstance($widgetId, $revisionId, $blockName, $position, $visible)
    {

        $positionNumber = self::_calcWidgetPositionNumber($revisionId, null, $blockName, $position);

        $row = array(
            'widgetId' => $widgetId,
            'revisionId' => $revisionId,
            'blockName' => $blockName,
            'position' => $positionNumber,
            'isVisible' => (int)$visible,
            'createdAt' => time(),
            'isDeleted' => 0,
        );

        return ipDb()->insert('widgetInstance', $row);
    }

    /**
     *
     * Return float number that will position widget in requested position
     * @param int $instnaceId
     * @param string $blockName
     * @param int $newPosition Real position of widget starting with 0
     */
    private static function _calcWidgetPositionNumber($revisionId, $instanceId, $newBlockName, $newPosition)
    {
        $allWidgets = Model::getBlockWidgetRecords($newBlockName, $revisionId);

        $widgets = array();

        foreach ($allWidgets as $widgetKey => $instance) {
            if ($instanceId === null || $instance['id'] != $instanceId) {
                $widgets[] = $instance;
            }
        }

        if (count($widgets) == 0) {
            $positionNumber = 0;
        } else {
            if ($newPosition <= 0) {
                $positionNumber = $widgets[0]['position'] - 40;
            } else {
                if ($newPosition >= count($widgets)) {
                    $positionNumber = $widgets[count($widgets) - 1]['position'] + 40;
                } else {
                    $positionNumber = ($widgets[$newPosition - 1]['position'] + $widgets[$newPosition]['position']) / 2;
                }
            }
        }
        return $positionNumber;
    }

    /**
     *
     * Mark instance as deleted. Instance will be remove completely, when revision will be deleted.
     * @param int $instanceId
     */
    public static function deleteInstance($instanceId)
    {
        ipDb()->update('widgetInstance', array('isDeleted' => 1, 'deletedAt' => time()), array('instanceId' => $instanceId));
        return true;
    }


    /**
     *
     * Find position of widget in current block
     * @param int $instanceId
     * @return int position of widget or null if widget does not exist
     */
    public static function getInstancePosition($instanceId)
    {
        $record = Model::getWidgetFullRecord($instanceId);

        $table = ipTable('widgetInstance');
        $sql = "
            SELECT count(*) as position
            FROM $table
            WHERE
                `revisionId` = :revisionId AND
                `blockName` = :blockName AND
                `position` < :position AND
                `isDeleted` = 0
        ";

        return ipDb()->fetchValue($sql, array(
                'revisionId' => $record['revisionId'],
                'blockName' => $record['blockName'],
                'position' => $record['position'],
            ));
    }

}

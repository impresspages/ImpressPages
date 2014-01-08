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
        $instances = ipDb()->select('*', 'widget_instance', array('instanceId' => $instanceId));
        if (isset($instances[0])) {
            return $instances[0];
        } else {
            return false;
        }
    }

    public static function updateInstance($instanceId, $data)
    {
        return ipDb()->update('widget_instance', $data, array('instanceId' => $instanceId));
    }



    public static function addInstance($widgetId, $revisionId, $blockName, $position, $visible)
    {

        $positionNumber = self::_calcWidgetPositionNumber($revisionId, null, $blockName, $position);

        $row = array(
            'widgetId' => $widgetId,
            'revisionId' => $revisionId,
            'blockName' => $blockName,
            'position' => $positionNumber,
            'visible' => (int)$visible,
            'created' => time(),
        );

        return ipDb()->insert('widget_instance', $row);
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
            if ($instanceId === null || $instance['instanaceId'] != $instanceId) {
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
        ipDb()->update('widget_instance', array('deleted' => time()), array('instanceId' => $instanceId));
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

        $table = ipTable('widget_instance');
        $sql = "
            SELECT count(instanceId) as position
            FROM $table
            WHERE
                `revisionId` = :revisionId AND
                `blockName` = :blockName AND
                `position` < :position AND
                `deleted` IS NULL
        ";

        return ipDb()->fetchValue($sql, array(
                'revisionId' => $record['revisionId'],
                'blockName' => $record['blockName'],
                'position' => $record['position'],
            ));
    }

}
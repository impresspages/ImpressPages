<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal\Content;


class WidgetModel
{




    public static function getInstance($instanceId)
    {
        return ipDb()->selectRow('widget', '*', array('id' => $instanceId));
    }

    public static function updateInstance($instanceId, $data)
    {
        return ipDb()->update('widget', $data, array('id' => $instanceId));
    }



    public static function addInstance($widgetId, $revisionId, $languageId, $blockName, $position, $visible)
    {

        $positionNumber = self::_calcWidgetPositionNumber($revisionId, $languageId, null, $blockName, $position);

        $row = array(
            'widgetId' => $widgetId,
            'revisionId' => $revisionId,
            'languageId' => $languageId,
            'blockName' => $blockName,
            'position' => $positionNumber,
            'isVisible' => (int)$visible,
            'createdAt' => time(),
            'isDeleted' => 0,
        );

        return ipDb()->insert('widget', $row);
    }

    /**
     *
     * Return float number that will position widget in requested position
     * @param int $instnaceId
     * @param string $blockName
     * @param int $newPosition Real position of widget starting with 0
     */
    private static function _calcWidgetPositionNumber($revisionId, $languageId, $instanceId, $newBlockName, $newPosition)
    {
        $allWidgets = Model::getBlockWidgetRecords($newBlockName, $revisionId, $languageId);

        $widgets = array();

        foreach ($allWidgets as $instance) {
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
     * Mark widget as deleted. Widget will be removed completely, when revision will be removed.
     * @param $widgetId
     * @return bool
     */
    public static function delete($widgetId)
    {
        ipDb()->update('widget', array('isDeleted' => 1, 'deletedAt' => time()), array('id' => $widgetId));
        return true;
    }




}

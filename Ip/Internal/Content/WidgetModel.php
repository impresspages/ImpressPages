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





}

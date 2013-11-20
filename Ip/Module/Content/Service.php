<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Content;



class Service{
    public static function getAvailableWidgets()
    {
        return Model::getAvailableWidgetObjects();
    }


    public static function setManagementMode($newMode)
    {
        $_SESSION['Content']['managementMode'] = $newMode ? 1 : 0;
    }

    public static function isManagementMode()
    {
        return !empty($_SESSION['Content']['managementMode']);
    }
}
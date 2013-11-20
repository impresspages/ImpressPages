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
}
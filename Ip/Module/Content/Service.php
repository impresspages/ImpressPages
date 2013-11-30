<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Content;



class Service{

    /**
     * @return \Ip\WidgetController[]
     */
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

    public static function getPageLayout(\Ip\Page $page)
    {
        $zone = ipContent()->getZone($page->getZoneName());
        $layout = \Ip\Internal\ContentDb::getPageLayout(
            $zone->getAssociatedModuleGroup(),
            $zone->getAssociatedModule(),
            $page->getId()
        );



        if (!$layout) {
            $layout = $zone->getLayout();
        }
        return $layout;
    }
}
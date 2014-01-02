<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal\Content;


use Ip\Form\Exception;

class Service
{


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
        $backendLoggedIn = \Ip\Internal\Admin\Backend::loggedIn();
        return $backendLoggedIn && !empty($_SESSION['Content']['managementMode']);
    }

    public static function getPageLayout(\Ip\Page $page)
    {
        $zone = ipContent()->getZone($page->getZoneName());
        $layout = \Ip\Internal\ContentDb::getPageLayout(
            $zone->getAssociatedModule(),
            $page->getId()
        );


        if (!$layout) {
            $layout = $zone->getLayout();
        }
        return $layout;
    }




    public static function addWidget($widgetName, $data = null, $look = null)
    {
        $widgetObject = Model::getWidgetObject($widgetName);
        if (!$widgetObject) {
            throw new \Ip\CoreException("Widget '$widgetName' doesn't exist");
        }

        if ($data ===  null) {
            $data = $widgetObject->defaultData();
        }

        if ($look === null) {
            $looks = $widgetObject->getLooks();
            $look = $looks[0]['name'];
        }

        $widgetId = Model::createWidget($widgetName, $data, $look);
        return $widgetId;
    }

    public static function addWidgetInstance($widgetId, $revisionId, $block, $position, $columnId = null, $visible = true)
    {
        $instanceId = Model::addInstance($widgetId, $revisionId, $block, $position, $columnId, $visible);
        return $instanceId;
    }


    public static function removeWidgetInstance($instanceId)
    {
        Model::removeWidgetInstance($instanceId);
    }



}
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
        return $backendLoggedIn && !empty($_SESSION['Content']['managementMode']); //we can't check here if we are in a page. It will result in widget rendering in non management mode when widget is rendered using ajax
    }

    public static function getPageLayout(\Ip\Page $page)
    {
        return ipPageStorage($page->getId())->get('layout', 'main.php');
    }

    public static function createWidget($widgetName, $data, $skin, $revisionId, $languageId, $blockName, $position, $visible = true)
    {
        $widgetObject = Model::getWidgetObject($widgetName);
        if (!$widgetObject) {
            throw new \Ip\Exception("Widget '" . esc($widgetName) . "' doesn't exist");
        }

        if ($data ===  null) {
            $data = $widgetObject->defaultData();
        }

        if ($skin === null) {
            $skins = $widgetObject->getSkins();
            $skin = $skins[0]['name'];
        }

        $widgetId = Model::createWidget($widgetName, $data, $skin, $revisionId, $languageId, $blockName, $position, $visible);
        return $widgetId;
    }


    public static function deleteWidget($widgetId)
    {
        Model::deleteWidget($widgetId);
    }


    public static function moveWidget($widgetId, $position, $blockName, $revisionId, $languageId)
    {
        Model::moveWidget($widgetId, $position, $blockName, $revisionId, $languageId);
    }

    public static function removeRevision($revisionId)
    {
        Model::removeRevision($revisionId);
    }



}

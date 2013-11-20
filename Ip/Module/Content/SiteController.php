<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Content;



class SiteController extends \Ip\Controller{


    public function widgetPost()
    {
        $instanceId = ipRequest()->getPost('instanceId');

        if (!$instanceId) {
            return \Ip\Response\JsonRpc::error('Mising instanceId POST variable');
        }
        $instanceId = $_POST['instanceId'];

        $widgetRecord = Model::getWidgetFullRecord($instanceId);

        try {
            if (!$widgetRecord) {
                return \Ip\Response\JsonRpc::error("Can't find requested Widget: ".$instanceId, Exception::UNKNOWN_INSTANCE);
            }

            $widgetObject = Model::getWidgetObject($widgetRecord['name']);
            if (!$widgetObject) {
                return \Ip\Response\JsonRpc::error("Can't find requested Widget: ".$widgetRecord['name'], Exception::UNKNOWN_WIDGET);
            }

            // TODOX Trello #133 remove $post parameter
            return $widgetObject->post($instanceId, ipRequest()->getPost(), $widgetRecord['data']);
        } catch (Exception $e) {
            return \Ip\Response\JsonRpc::error($e->getMessage());
        }
    }

}

<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal\Content;


class SiteController extends \Ip\Controller
{


    public function widgetPost()
    {
        $instanceId = ipRequest()->getPost('instanceId');

        if (!$instanceId) {
            return \Ip\Response\JsonRpc::error('Missing instanceId POST variable');
        }
        $instanceId = $_POST['instanceId'];

        $widgetRecord = Model::getWidgetRecord($instanceId);

        try {
            if (!$widgetRecord) {
                return \Ip\Response\JsonRpc::error(
                    "Can't find requested Widget: " . $instanceId,
                    Exception::UNKNOWN_INSTANCE
                );
            }

            $widgetObject = Model::getWidgetObject($widgetRecord['name']);
            if (!$widgetObject) {
                return \Ip\Response\JsonRpc::error(
                    "Can't find requested Widget: " . $widgetRecord['name'],
                    Exception::UNKNOWN_WIDGET
                );
            }

            return $widgetObject->post($instanceId, $widgetRecord['data']);
        } catch (Exception $e) {
            return \Ip\Response\JsonRpc::error($e->getMessage());
        }
    }

}

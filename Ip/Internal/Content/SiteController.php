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
        $widgetId = ipRequest()->getPost('widgetId');

        if (!$widgetId) {
            return \Ip\Response\JsonRpc::error('Missing widgetId POST variable');
        }
        $widgetId = $_POST['widgetId'];

        $widgetRecord = Model::getWidgetRecord($widgetId);

        try {
            if (!$widgetRecord) {
                return \Ip\Response\JsonRpc::error(
                    "Can't find requested Widget: " . $widgetId,
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

            return $widgetObject->post($widgetId, $widgetRecord['data']);
        } catch (Exception $e) {
            return \Ip\Response\JsonRpc::error($e->getMessage());
        }
    }

}

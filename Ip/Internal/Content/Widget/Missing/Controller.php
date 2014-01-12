<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Internal\Content\Widget\Missing;




class Controller extends \Ip\WidgetController{


    public function getTitle() {
        return __('Missing', 'ipAdmin', false);
    }



    public function generateHtml($revisionId, $widgetId, $instanceId, $data, $layout)
    {

        if (ipIsManagementState()) {
            return parent::generateHtml($revisionId, $widgetId, $instanceId, $data, $layout);
        } else {
            return '';
        }
    }
}
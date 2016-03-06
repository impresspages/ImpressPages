<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal\Content\Widget\Missing;


class Controller extends \Ip\WidgetController
{


    public function getTitle()
    {
        return __('Missing', 'Ip-admin', false);
    }


    public function generateHtml($revisionId, $widgetId, $data, $skin)
    {

        if (ipIsManagementState()) {
            return parent::generateHtml($revisionId, $widgetId, $data, $skin);
        } else {
            return '';
        }
    }
}

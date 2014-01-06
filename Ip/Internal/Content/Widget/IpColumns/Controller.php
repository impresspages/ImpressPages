<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal\Content\Widget\IpColumns;


class Controller extends \Ip\WidgetController
{

    public function getTitle()
    {
        return __('Columns', 'ipAdmin', false);
    }

    public function generateHtml($widgetId, $instanceId, $data, $layout)
    {

        $data['widgetId'] = $widgetId;
        return parent::generateHtml($widgetId, $instanceId, $data, $layout);
    }


}
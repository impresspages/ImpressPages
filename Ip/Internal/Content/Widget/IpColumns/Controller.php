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

    public function generateHtml($revisionId, $widgetId, $instanceId, $data, $layout)
    {
        $data['revisionId'] = $revisionId;
        $data['widgetId'] = $widgetId;
        return parent::generateHtml($revisionId, $widgetId, $instanceId, $data, $layout);
    }


}
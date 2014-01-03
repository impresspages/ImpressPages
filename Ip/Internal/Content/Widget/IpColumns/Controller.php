<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Internal\Content\Widget\IpColumns;


class Controller extends \Ip\WidgetController {
	
	public function getTitle() {
        return __('Columns', 'ipAdmin', false);
    }


    public function generateHtml($instanceId, $data, $layout) {
        if (!isset($data['baseId'])) {
            return '';
        }
        return parent::generateHtml($instanceId, $data, $layout);
    }


}
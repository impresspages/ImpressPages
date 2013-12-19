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


    public function previewHtml($instanceId, $data, $layout) {
        if (!isset($data['baseId'])) {
            return '';
        }
        return parent::previewHtml($instanceId, $data, $layout);
    }


}
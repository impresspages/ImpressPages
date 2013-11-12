<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Content\Widget;


class IpColumns extends \Ip\Module\Content\Widget {
	
	public function getTitle() {
        return __('Columns', 'ipAdmin');
    }


    public function previewHtml($instanceId, $data, $layout) {
        if (!isset($data['baseId'])) {
            return '';
        }
        return parent::previewHtml($instanceId, $data, $layout);
    }

    public function managementHtml($instanceId, $data, $layout) {
        // use $instanceId as seed for the names of our blocks because it is unique.
        // but since we will get a new instanceId after page is published, we need
        // to persist it in widget data.
        if (!array_key_exists('baseId', $data) || !$data['baseId']) {
            $data['baseId'] = $instanceId;
        }
        if (empty($data['columns'])) {
            $data['columns'] = 2;
        }
        return parent::managementHtml($instanceId, $data, $layout);
    }
}
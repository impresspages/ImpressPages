<?php
/**
 * @package ImpressPages

 *
 */
namespace Modules\standard\content_management\widget;


class IpColumns extends \Modules\standard\content_management\Widget {
	
	public function getTitle() {
        $parametersMod = \Ip\ServiceLocator::getParametersMod();
        return $parametersMod->getValue('standard', 'content_management', 'widget_columns', 'widget_title');
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
<?php
/**
 * @package ImpressPages

 *
 */
namespace Modules\standard\content_management\widget;

if (!defined('CMS')) exit;



class IpTitle extends \Modules\standard\content_management\Widget{

    public function managementHtml($instanceId, $data, $layout) {
        $curUrl = \Ip\ServiceLocator::getRequest()->getUrl();
        $parts = explode('?', $curUrl);
        $curUrl = $parts[0];
        $data['curUrl'] = $curUrl;
        return parent::managementHtml($instanceId, $data, $layout);
    }


    public function getTitle() {
        global $parametersMod;
        return $parametersMod->getValue('standard', 'content_management', 'widget_title', 'title');
    }
    
}
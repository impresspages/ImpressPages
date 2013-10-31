<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Content\Widget;




class IpTitle extends \Ip\Module\Content\Widget{

    public function managementHtml($instanceId, $data, $layout) {
        $curUrl = \Ip\Request::getUrl();
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
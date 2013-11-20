<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Content\Widget\IpTitle;




class Controller extends \Ip\Module\Content\WidgetController{

    public function managementHtml($instanceId, $data, $layout) {
        $curUrl = ipRequest()->getUrl();
        $parts = explode('?', $curUrl);
        $curUrl = $parts[0];
        $data['curUrl'] = $curUrl;
        return parent::managementHtml($instanceId, $data, $layout);
    }


    public function getTitle() {
        return __('Title', 'ipAdmin');
    }
    
}
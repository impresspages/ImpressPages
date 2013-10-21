<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Content\widget;




class IpTable extends \Ip\Module\Content\Widget{


    public function getTitle() {
        global $parametersMod;
        return $parametersMod->getValue('standard', 'content_management', 'widget_table', 'widget_title');
    }
    
}
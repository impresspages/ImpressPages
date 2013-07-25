<?php
/**
 * @package ImpressPages

 *
 */
namespace Modules\standard\content_management\widget;

if (!defined('CMS')) exit;



class IpTable extends \Modules\standard\content_management\Widget{


    public function getTitle() {
        global $parametersMod;
        return $parametersMod->getValue('standard', 'content_management', 'widget_table', 'widget_title');
    }
    
}
<?php
/**
 * @package ImpressPages

 *
 */
namespace Modules\standard\content_management\widget;

if (!defined('CMS')) exit;



class IpText extends \Modules\standard\content_management\Widget{


    public function getTitle() {
        global $parametersMod;
        return $parametersMod->getValue('standard', 'content_management', 'widget_text', 'menu_mod_text');
    }
    
}
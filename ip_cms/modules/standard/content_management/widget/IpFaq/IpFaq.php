<?php
/**
 * @package ImpressPages

 *
 */
namespace Modules\standard\content_management\widget;

if (!defined('CMS')) exit;

class IpFaq extends \Modules\standard\content_management\Widget{


    public function getTitle() {
        global $parametersMod;
        return $parametersMod->getValue('standard', 'content_management', 'widget_faq', 'widget_title');
    }

}
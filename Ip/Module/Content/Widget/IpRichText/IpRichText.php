<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Content\Widget;




class IpRichText extends \Ip\Module\Content\Widget{


    public function getTitle() {
        global $parametersMod;
        return __('Rich text', 'ipAdmin');
    }
    
}
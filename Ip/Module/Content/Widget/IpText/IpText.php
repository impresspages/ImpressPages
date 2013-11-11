<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Content\Widget;




class IpText extends \Ip\Module\Content\Widget{


    public function getTitle() {
        global $parametersMod;
        return __('Text', 'ipAdmin');
    }
    
}
<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Content\Widget;




class IpHtml extends \Ip\Module\Content\Widget{


    public function getTitle() {
        global $parametersMod;
        return __('HTML code', 'ipAdmin');
    }
    
}
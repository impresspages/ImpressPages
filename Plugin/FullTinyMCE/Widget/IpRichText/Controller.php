<?php
/**
 * @package ImpressPages

 *
 */
namespace Plugin\FullTinyMCE\Widget\IpRichText;




class Controller extends \Ip\Module\Content\WidgetController{


    public function getTitle() {
        return __('Rich text', 'ipAdmin');
    }
    
}
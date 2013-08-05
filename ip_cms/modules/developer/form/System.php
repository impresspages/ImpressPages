<?php
/**
 * @package ImpressPages
 *
 */

namespace Modules\developer\form;




class System
{
    public function init()
    {
        $site = \Ip\ServiceLocator::getSite();
        $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery/jquery.js');
        $site->addJavascript(BASE_URL.MODULE_DIR.'developer/form/public/form.js');
        $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/farbtastic/farbtastic.js');
        $site->addCSS(BASE_URL.LIBRARY_DIR.'js/farbtastic/farbtastic.css');
    }
}
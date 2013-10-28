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
        $site->addJavascript(\Ip\Config::libraryUrl('js/jquery/jquery.js'));
        $site->addJavascript(\Ip\Config::oldModuleUrl('developer/form/public/form.js'));
    }
}
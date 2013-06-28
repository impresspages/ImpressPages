<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
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
    }
}
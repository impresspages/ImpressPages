<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Modules\developer\inline_management;


class System
{
    function init()
    {
        global $site;

        $site->addJavascript(BASE_URL.MODULE_DIR.'developer/inline_management/public/inlineManagement.js');
    }
}



<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

/**
 * 
 * Graphic User Interface bootstrap
 *
 */

namespace IpUpdate\Library;

class Bootstrap
{
    public function run()
    {
        require_once(__DIR__.'/Config.php');
        require_once(IUL_BASE_DIR.'Autoloader.php');
        $autoloader = new \IpUpdate\Library\Autoloader();
        $autoloader->register(IUL_BASE_DIR);
    }
}


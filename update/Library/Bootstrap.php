<?php
/**
 * @package ImpressPages
 *
 *
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
        require_once(__DIR__ . '/Config.php');
        require_once(IUL_BASE_DIR . 'Autoloader.php');
        $autoloader = new \IpUpdate\Library\Autoloader();
        $autoloader->register(IUL_BASE_DIR);
        date_default_timezone_set('Europe/London');
    }
}


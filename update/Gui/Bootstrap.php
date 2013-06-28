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

namespace IpUpdate\Gui;

class Bootstrap
{
    public function run()
    {
        require_once(__DIR__ . '/Config.php');
        
        $this->fixMagicQuotes();
        require_once(IUG_BASE_DIR . 'Autoloader.php');
        $autoloader = new \IpUpdate\Gui\Autoloader();
        $autoloader->register(IUG_BASE_DIR);

        //bootstrap IpUpdate library
        require_once(__DIR__ . '/../Library/Bootstrap.php');
        $libraryBootstrap = new \IpUpdate\Library\Bootstrap();
        $libraryBootstrap->run();
        
        $request = Request::getInstance();
        $request->execute();
        $request->sendOutput();
    }

    private function fixMagicQuotes()
    {
        if (get_magic_quotes_gpc()) { //fix magic quotes option
            $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
            while (list($key, $val) = each($process)) {
                foreach ($val as $k => $v) {
                    unset($process[$key][$k]);
                    if (is_array($v)) {
                        $process[$key][stripslashes($k)] = $v;
                        $process[] = &$process[$key][stripslashes($k)];
                    } else {
                        $process[$key][stripslashes($k)] = stripslashes($v);
                    }
                }
            }
            unset($process);
        }
    }
}



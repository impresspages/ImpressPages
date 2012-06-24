<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Gui;

class Bootstrap {
    public function run() {
        $this->fixMagicQuotes();

        // define('CMS', true); // make sure other files are accessed through this file.
        // define('BACKEND', true); // make sure other files are accessed through this file.
        // define('UPDATE_INCLUDE_DIR', 'includes/');



        // $navigation = new Navigation ();
        // $scripts = new Scripts ();
        // $update = new Update ();
        // $htmlOutput = new HtmlOutput ();


        // $update->execute();


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



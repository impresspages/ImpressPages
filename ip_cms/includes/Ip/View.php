<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Ip;


if (!defined('CMS')) exit;



/**
 *
 * View class
 *
 */
class View{

    private $file;
    private $data;


    private function __construct($file, $data = array()) {
        $this->file = $file;
        $this->data = $data;
    }


    public static function create($file, $data = array()) {
        $backtrace = debug_backtrace();
        if(!isset($backtrace[0]['file']) || !isset($backtrace[0]['line'])) {
            throw new CoreException("Can't find caller", CoreException::VIEW);
        }

        $sourceFile = $backtrace[0]['file'];
        if (DIRECTORY_SEPARATOR != '/') {
            $sourceFile = str_replace(DIRECTORY_SEPARATOR, '/', $sourceFile);
        }
        
        
        $foundFile = self::findFile($file, $sourceFile);
        if ($foundFile === false) {
            throw new CoreException('Can\'t find view file \''.$file. '\' (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' )', CoreException::VIEW);
        }

        foreach ($data as $key => $value) {
            if (! preg_match('/^[a-zA-Z0-9_-]+$/', $key) || $key == '') {
                $source = '';                
                if(isset($backtrace[0]['file']) && $backtrace[0]['line']) {
                    $source = "(Error source: ".($backtrace[0]['file'])." line: ".($backtrace[0]['line'])." ) ";
                }
                throw new CoreException("Incorrect view variable name '".$key."' ".$source, CoreException::VIEW);
            }
        }

        return new \Ip\View($foundFile, $data);
    }


    private static function findFile($file, $sourceFile) {
        if (strpos($file, BASE_DIR) !== 0) {
            $file = dirname($sourceFile).'/'.$file;
        }

        
        
        $moduleView = ''; //relative link to view according to modules root.
        if (strpos($file, BASE_DIR.MODULE_DIR) === 0) {
            $moduleView = substr($file, strlen(BASE_DIR.MODULE_DIR));
        }
        
        if ($moduleView == '' && strpos($file, BASE_DIR.PLUGIN_DIR) === 0) {
            $moduleView = substr($file, strlen(BASE_DIR.PLUGIN_DIR));
        }
      
        if ($moduleView == '' && strpos($file, BASE_DIR.THEME_DIR.'modules/') === 0) {
            $moduleView = substr($file, strlen(BASE_DIR.THEME_DIR.'modules/'));
        }
        if ($moduleView != '') {
            if (file_exists(BASE_DIR.THEME_DIR.THEME.'/modules/'.$moduleView)) {
                return BASE_DIR.THEME_DIR.THEME.'/modules/'.$moduleView;
            }

            if (file_exists(BASE_DIR.PLUGIN_DIR.$moduleView)) {
                return(BASE_DIR.PLUGIN_DIR.$moduleView);
            }            
            
            if (file_exists(BASE_DIR.MODULE_DIR.$moduleView)) {
                return(BASE_DIR.MODULE_DIR.$moduleView);
            }
            
        } else {
            if (file_exists($file)) {
                return $file;
            } else {
                return false;
            }            
        }
        
        return false;
    }


    public function render () {
        global $site;
        global $log;
        global $dispatcher;
        global $parametersMod;
        global $session;

        foreach ($this->data as $key => $value) {
            eval(' $'.$key.' = $value;');
        }


        $found = false;

        ob_start();

        require ($this->file);      //file existance is checked in constructor  

        $output = ob_get_contents();
        ob_end_clean();

        return $output;

    }



}
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
    private $absolute;
    
    
    public function __construct($file, $data = array(), $absolute = false) {
        $this->file = $file;
        $this->data = $data;
        $this->absolute = $absolute;
    }
    
    
    public static function create($file, $data = array(), $absolute = false) {
        return new \Ip\View($file, $data, $absolute);
    }
    
    
    
    public function render () {
        global $site;
        global $log;
        global $dispatcher;        
        global $parametersMod;
        global $session;

        foreach ($this->data as $key => $value) {
            if (preg_match('/^[a-zA-Z0-9_-]+$/', $key)) {
                eval(' $'.$key.' = $value;');
            } else {
                $source = '';
                if(isset($backtrace[0]['file']) && $backtrace[0]['line']) {
                    $source = "(Error source: ".($backtrace[0]['file'])." line: ".($backtrace[0]['line'])." ) ";
                }
                throw new Exception("Incorrect view variable name '".$key."' ".$source);
            }    
        }
        
        $found = false;
        
        ob_start();
        
        if ($this->absolute) {
            if (file_exists($this->file)) {
                $found = true;
                require($this->file);
            }
        } else {
            if (file_exists(BASE_DIR.THEME_DIR.THEME.'/modules/'.$this->file)) {
                $found = true;
                require(BASE_DIR.THEME_DIR.THEME.'/modules/'.$this->file);
            }
            
            if (! $found && file_exists(BASE_DIR.MODULE_DIR.$this->file)) {
                $found = true;
                require(BASE_DIR.MODULE_DIR.$this->file);
            }
        }
        
        $output = ob_get_contents();
        ob_end_clean();
        
        if (!$found) {
            $source = '';
            $backtrace = debug_backtrace();
            if(isset($backtrace[0]['file']) && $backtrace[0]['line']) {
                $source = "(Error source: ".($backtrace[0]['file'])." line: ".($backtrace[0]['line'])." ) ";
            }
            
            throw new Exception("Can't find view file: ".$this->file." ".$source);
        }
        
        return $output;        
        
    }

    

}
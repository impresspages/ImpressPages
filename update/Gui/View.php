<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace IpUpdate\Gui;

/**
 *
 * View class
 *
 */
class View{
    private $file;
    private $data;
    private $doctype;
    private $languageId;

    


    /**
     * 
     * Create view file
     * @param string $file
     * @param array $data
     */
    private function __construct($file, $data = array())
    {
        global $site;
        $this->file = $file;
        $this->data = $data;
    }
    
    /**
     * 
     * Create new view object with the same doctype, but different view file and data
     * Use it to include another view file within the view
     * @param string $file
     * @param array $data
     */
    public function subview($file, $data = array())
    {
        $foundFile = self::findView($file);
        self::checkData($data);
        $view = new \Ip\View($foundFile, $data);
        $view->setDoctype($this->getDoctype());
        return $view;
    }


    public static function create($file, $data = array())
    {
        $foundFile = self::findView($file);
        self::checkData($data);
        return new View($foundFile, $data);
    }
    
    /**
     * Escape and echo text
     * @param string $text
     */
    public function esc($text, $variables = null)
    {
        if (!empty($variables) && is_array($variables)) {
            foreach($variables as $variableKey => $variableValue) {
                $text = str_replace('[[' . $variableKey . ']]', $variableValue, $text);
            }
        }
        return htmlspecialchars($text);
    }
    
    
    public function escTran($key)
    {
        $translation = \IpUpdate\Gui\Translation::getInstance()->translate($key);
        return $this->esc($translation);
    }
    
    /**
     * 
     * Set view data
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
    
    public function assign($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function getData()
    {
        return $this->data;
    }


    public function render ()
    {
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

        require ($this->file);      //file existance has been checked in constructor

        $output = ob_get_contents();
        ob_end_clean();

        return $output;

    }
    
    public function __toString()
    {
        return $this->render();
    }    


    private static function findView($file)
    {
        $backtrace = debug_backtrace();
        if(!isset($backtrace[1]['file']) || !isset($backtrace[1]['line'])) {
            throw new \IpUpdate\Gui\Exception("Can't find caller", \IpUpdate\Gui\Exception::VIEW);
        }

        $sourceFile = $backtrace[1]['file'];
        if (DIRECTORY_SEPARATOR != '/') {
            $sourceFile = str_replace(DIRECTORY_SEPARATOR, '/', $sourceFile);
        }

        $foundFile = self::findFile($file, $sourceFile);
        if ($foundFile === false) {
            throw new \IpUpdate\Gui\Exception('Can\'t find view file \''.$file. '\' (Error source: '.$backtrace[1]['file'].' line: '.$backtrace[1]['line'].' )', \IpUpdate\Gui\Exception::VIEW);
        }    
        return $foundFile;
    }
    
    private static function findFile($file, $sourceFile)
    {
        //$file = dirname($sourceFile).'/'.$file;
        $file = IUG_BASE_DIR.IUG_VIEW_DIR.$file;
        if (file_exists($file)) {
            return $file;
        } else {
            return false;
        }
        return false;
    }
    
    private static function checkData ($data)
    {
        foreach ($data as $key => $value) {
            if (! preg_match('/^[a-zA-Z0-9_-]+$/', $key) || $key == '') {
                $source = '';
                if(isset($backtrace[0]['file']) && $backtrace[0]['line']) {
                    $source = "(Error source: ".($backtrace[0]['file'])." line: ".($backtrace[0]['line'])." ) ";
                }
                throw new \IpUpdate\Gui\Exception("Incorrect view variable name '".$key."' ".$source, \IpUpdate\Gui\Exception::VIEW);
            }
        }
    }

    
    
}
<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace IpUpdate\Gui;

class Controller
{
    private $output;
    public $view;
    private $request;
    
    public function __construct(\IpUpdate\Gui\Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param \IpUpdate\Gui\View $view
     */
    public function setView(\IpUpdate\Gui\View $view)
    {
        $this->view = $view;
    }
    
    public function getOutput()
    {
        if ($this->output) {
            return $this->output;
        } else {
            if ($this->view) { 
                return $this->view->render();
            }
        }
    }
    
    /**
     * @returns \IpUpdate\Gui\Request
     */
    public function getRequest()
    {
        return $this->request;
    }
    
    public function returnJson($data) {
        $this->request->setLayout(null);
        header('Content-type: text/json; charset=utf-8'); //throws save file dialog on firefox if iframe is used
        $output = json_encode($this->utf8Encode($data));
        $this->output = $output;
    }
    
    
    public function registerAjaxErrorHandling()
    {
        \IpUpdate\Gui\AjaxErrorHandler::init();
    }
    
    /**
    *
    *  Returns $dat encoded to UTF8
    * @param mixed $dat array or string
    */
    private function utf8Encode($dat)
    {
        if (is_string($dat)) {
            if (mb_check_encoding($dat, 'UTF-8')) {
                return $dat;
            } else {
                return utf8_encode($dat);
            }
        }
        if (is_array($dat)) {
            $answer = array();
            foreach($dat as $i=>$d) {
                $answer[$i] = $this->utf8Encode($d);
            }
            return $answer;
        }
        return $dat;
    }
    

}
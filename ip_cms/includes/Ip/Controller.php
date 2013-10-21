<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip;


if (!defined('CMS')) exit;

/**
 *
 * Event dispatcher class
 *
 */
class Controller{

    public function allowAction($action){
        return true;
    }
    
    /**
     * Do any initializatoin becore actual controller method
     */
    public function init() {
    }
    
    public function returnJson($data) {
        $site = \Ip\ServiceLocator::getSite();
        header('Content-type: text/json; charset=utf-8'); //throws save file dialog on firefox if iframe is used
        $answer = json_encode($this->utf8Encode($data));
        $site->setOutput($answer);
    }
    
    public function redirect ($url) {
        $site = \Ip\ServiceLocator::getSite();
        $dispatcher = \Ip\ServiceLocator::getDispatcher();
        header("location: ".$url);
        \Db::disconnect();
        $dispatcher->notify(new \Ip\Event($site, 'site.databaseDisconnect', null));
        exit;
    }


    /**
     * Wrap content into admin layout view. Use when generating administration pages.
     * @param string $content
     * @return View
     */
    public function createAdminView($content)
    {
        if (is_object($content) && get_class($content) == 'Ip\View') {
            $content = $content->render();
        }

        $variables = array(
            'content' => $content
        );
        $view = \Ip\View::create(BASE_DIR.MODULE_DIR.'standard/configuration/view/adminLayout.php', $variables);
        return $view;
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
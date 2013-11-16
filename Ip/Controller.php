<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip;


/**
 *
 * Event dispatcher class
 *
 */
class Controller{


    /**
     * Do any initialization before actual controller method
     */
    public function init() {
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
        $view = \Ip\View::create(\Ip\Config::coreModuleFile('Config/view/adminLayout.php'), $variables);
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
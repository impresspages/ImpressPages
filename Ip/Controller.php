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



}
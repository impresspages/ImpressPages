<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Response;

/**
 *
 * Event dispatcher class
 *
 */
class Status404 extends \Ip\Response {
//TODOX fix
//is_file(\Ip\Config::themeFile('404.php')) ? '404.php' : 'main.php'

    public function send()
    {
        $event = new \Ip\Event($this, 'site.beforeError404', null);
        \Ip\ServiceLocator::getDispatcher()->notify($event);
        if (!$event->getProcessed()) {
            \Ip\ServiceLocator::getDispatcher()->notify(new \Ip\Event($this, 'site.error404', null));
        }
    }


    protected function generateError404Content(\Ip\Event $event) {
        global $parametersMod;

        $blockName = $event->getValue('blockName');
        if ($blockName != 'main' || !self::$enableError404Output) {
            return;
        }

        $data = array(
            'title' => $parametersMod->getVAlue('Config.error_title'),
            'text' => self::error404Message()
        );
        $content = \Ip\View::create(\Ip\Config::coreModuleFile('Config/view/error404.php'), $data)->render();
        $event->setValue('content', $content );
        $event->addProcessed();


    }


    /**
     * Find the reason why the user come to non-existent URL
     * @return string error message
     */
    protected function error404Message(){

        //find reason
        $message = '';
        if(!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == ''){
            $message = __('Config.error_mistyped_url', 'ipPublic');
        }else{
            if(strpos($_SERVER['HTTP_REFERER'], \Ip\Config::baseUrl('')) < 5 && strpos($_SERVER['HTTP_REFERER'], \Ip\Config::baseUrl('')) !== false){
                $message = '<p>' . __('Config.error_broken_link_inside', 'ipPublic') . '</p>';
            } elseif(strpos($_SERVER['HTTP_REFERER'], \Ip\Config::baseUrl('')) === false) {
                $message = '<p>' . __('Config.error_broken_link_outside', 'ipPublic') . '</p>';
            }
        }
        //end find reason
        return $message;
    }


}



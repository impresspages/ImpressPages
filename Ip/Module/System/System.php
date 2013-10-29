<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\System;


class System{
    private static $error404;
    private static $enableError404Output = true;
    
    public function __construct() {
        self::$error404 = false;
    }

    public function init(){
        global $site;
        global $dispatcher;

        if ($site->managementState()) {
            $site->addJavascript(\Ip\Config::oldModuleUrl('administrator/system/public/system.js'), 0);
        }

        $dispatcher->bind('site.error404', __NAMESPACE__ .'\System::catchError404');
        $dispatcher->bind(\Ip\Event\UrlChanged::URL_CHANGED, __NAMESPACE__ .'\System::urlChanged');
    }
    
    /**
     * 
     * Enable or disable error404 message display on 'main' content block. 
     * @param bool $value - enabled / disabled
     */
    public static function enableError404Output($value){
        self::$enableError404Output = $value;
    }


    public static function urlChanged (\Ip\Event\UrlChanged $event)
    {
        \DbSystem::replaceUrls($event->getOldUrl(), $event->getNewUrl());
    }
    
    public static function catchError404 (\Ip\Event $event) {
        global $parametersMod;
        global $log;
        global $site;
        global $dispatcher;
        
        $log->log("system", "error404", $site->getCurrentUrl()." ".self::error404Message());
        
        self::$error404 = true;
        self::sendError404Email();
        $dispatcher->bind('site.generateBlock', __NAMESPACE__ .'\System::generateError404Content');

        if(
            $parametersMod->getValue('standard', 'configuration', 'error_404', 'send_to_main_page')
            &&
           ($site->languageUrl != '' || $site->zoneUrl != '' || sizeof($site->getUrlVars()) > 0 || sizeof($site->getGetVars()) > 0 )
        ){
            \Ip\Response::redirect(BASE_URL);

            // TODOX make it not necessary
            $site->setOutput(null);
        }else{
            \Ip\Response::pageNotFound();
        }
    }
    
    public static function generateError404Content(\Ip\Event $event) {
        global $site;
        global $parametersMod;
        
        $blockName = $event->getValue('blockName');
        if ($blockName != 'main' || !self::$enableError404Output) {
            return;
        }
        
        $data = array(
            'title' => $parametersMod->getVAlue('standard', 'configuration', 'error_404', 'error_title'),
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
    private static function error404Message(){
        global $parametersMod;
        global $site;

        //find reason
        $message = '';
        if(!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == ''){
            $message = $parametersMod->getValue('standard','configuration','error_404', 'error_mistyped_url', $site->getCurrentLanguage()->getId());
        }else{
            if(strpos($_SERVER['HTTP_REFERER'], BASE_URL) < 5 && strpos($_SERVER['HTTP_REFERER'], BASE_URL) !== false){
                $message = $parametersMod->getValue('standard','configuration','error_404', 'error_broken_link_inside', $site->getCurrentLanguage()->getId());
            }if(strpos($_SERVER['HTTP_REFERER'], BASE_URL) === false){
                $message = $parametersMod->getValue('standard','configuration','error_404', 'error_broken_link_outside', $site->getCurrentLanguage()->getId());
            }
        }
        //end find reason
        return $message;
    }

    /**
     * 
     * send error email if needed
     */
    private static function sendError404Email () {
        global $site;
        global $parametersMod;
        $headers = 'MIME-Version: 1.0'. "\r\n";
        $headers .= 'Content-type: text/html; charset='.CHARSET."\r\n";
        $headers .= 'From: sender@sender.com' . "\r\n";
        $message = '';
        if(!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == ''){
            if(defined('ERRORS_SEND') && ERRORS_SEND && $parametersMod->getValue('standard', 'configuration','error_404', 'report_mistyped_urls', $site->getCurrentLanguage()->getId()))
            $message = self::error404Message().'
            <p> Link: <a href="'.$site->getCurrentUrl().'">'.htmlspecialchars($site->getCurrentUrl()).'</a></p>';
        }else{
            if(strpos($_SERVER['HTTP_REFERER'], BASE_URL) < 5 && strpos($_SERVER['HTTP_REFERER'], BASE_URL) !== false){
                if(defined('ERRORS_SEND') && ERRORS_SEND && $parametersMod->getValue('standard', 'configuration','error_404', 'report_broken_inside_link', $site->getCurrentLanguage()->getId()))
                $message = self::error404Message().'
             <p>Link: <a href="'.$site->getCurrentUrl().'">'.htmlspecialchars($site->getCurrentUrl()).'</a></p>
             <p>Http referer: <a href="'.$_SERVER['HTTP_REFERER'].'">'.htmlspecialchars($_SERVER['HTTP_REFERER']).'</a></p>';
            }if(strpos($_SERVER['HTTP_REFERER'], BASE_URL) === false){
                if(defined('ERRORS_SEND') && ERRORS_SEND && $parametersMod->getValue('standard', 'configuration','error_404', 'report_broken_outside_link', $site->getCurrentLanguage()->getId()))
                $message = self::error404Message().'
             <p>Link: <a href="'.$site->getCurrentUrl().'">'.htmlspecialchars($site->getCurrentUrl()).'</a></p>
             <p>Http referer: <a href="'.$_SERVER['HTTP_REFERER'].'">'.htmlspecialchars($_SERVER['HTTP_REFERER']).'</a></p>';
            }
        }
        if ($message != '') {
            //send email
            $queue = new \Ip\Module\Email\Module();
            $queue->addEmail($parametersMod->getValue('standard', 'configuration', 'main_parameters', 'email', $site->getCurrentLanguage()->getId()), $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'name', $site->getCurrentLanguage()->getId()), ERRORS_SEND, '', BASE_URL." ERROR", $message, false, true);
            $queue->send();

        }
    }


}



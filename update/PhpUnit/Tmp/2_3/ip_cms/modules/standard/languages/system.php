<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Modules\standard\languages;
if (!defined('CMS')) exit;



class System{



    function init(){
        global $dispatcher;

        $dispatcher->bind('site.generateBlock', __NAMESPACE__ .'\System::generateLanguages');
    }


    public static function generateLanguages (\Ip\Event $event) {
        global $site;
        global $parametersMod;
        $blockName = $event->getValue('blockName');
        if ( $blockName == 'ipLanguages' && $parametersMod->getValue('standard', 'languages', 'options', 'multilingual')) {
            require_once (BASE_DIR.MODULE_DIR.'standard/languages/module.php');
            $event->setValue('content', \Modules\standard\languages\Module::generateLanguageList());
            $event->addProcessed();
        }
    }

}
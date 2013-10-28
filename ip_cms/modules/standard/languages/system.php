<?php
/**
 * @package ImpressPages

 *
 */
namespace Modules\standard\languages;
if (!defined('CMS')) exit;



class System{



    function init(){
        global $dispatcher;

        $dispatcher->bind('site.generateBlock', __NAMESPACE__ .'\System::generateLanguages');

        $dispatcher->bind('site.generateSlot', __NAMESPACE__ .'\System::generateLanguagesSlot');
    }


    public static function generateLanguages (\Ip\Event $event) {
        global $parametersMod;
        $blockName = $event->getValue('blockName');
        if ( $blockName == 'ipLanguages') {
            if ($parametersMod->getValue('standard', 'languages', 'options', 'multilingual')) {
                require_once \Ip\Config::oldModuleFile('standard/languages/module.php');
                $event->setValue('content', \Modules\standard\languages\Module::generateLanguageList());
                $event->addProcessed();
            }else {
                $event->setValue('content', '');
                $event->addProcessed();
            }
        }
    }


    public static function generateLanguagesSlot (\Ip\Event $event) {
        global $parametersMod;
        $name = $event->getValue('slotName');
        if ( $name == 'ipLanguages') {
            if ($parametersMod->getValue('standard', 'languages', 'options', 'multilingual')) {
                require_once \Ip\Config::oldModuleFile('standard/languages/module.php');
                $event->setValue('content', \Modules\standard\languages\Module::generateLanguageList());
                $event->addProcessed();
            }else {
                $event->setValue('content', '');
                $event->addProcessed();
            }
        }
    }

}
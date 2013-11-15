<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Languages;

class System{



    function init(){
        \Ip\ServiceLocator::getDispatcher()->bind('site.generateSlot', __NAMESPACE__ .'\System::generateLanguagesSlot');
    }




    public static function generateLanguagesSlot (\Ip\Event $event) {
        global $parametersMod;
        $name = $event->getValue('slotName');
        if ( $name == 'ipLanguages') {
            if ($parametersMod->getValue('Config.multilingual')) {
                $event->setValue('content', Module::generateLanguageList());
                $event->addProcessed();
            }else {
                $event->setValue('content', '');
                $event->addProcessed();
            }
        }
    }

}
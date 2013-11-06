<?php
/**
 * @package		Library
 *
 *
 */
namespace Ip\Module\Languages;


class ElementUrl extends \Ip\Lib\StdMod\Element\Text{

    function checkField($prefix, $action, $area){
        global $parametersMod;


        switch ($action){
            case 'insert':
                $tmpLanguage = Db::getLanguageByUrl($_REQUEST[''.$prefix]);
                if($tmpLanguage){
                    return $parametersMod->getValue('Config.error_duplicate_url');
                }

                break;
            case 'update':
                $tmpLanguage = Db::getLanguageByUrl($_REQUEST[''.$prefix]);
                if($tmpLanguage && $tmpLanguage['id'] != $area->currentId){
                    return $parametersMod->getValue('Config.error_duplicate_url');
                }
                break;
        }

        return parent::checkField($prefix, $action, $area);
    }


}
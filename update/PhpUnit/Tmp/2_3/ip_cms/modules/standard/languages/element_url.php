<?php
/**
 * @package		Library
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Modules\standard\languages;

if (!defined('BACKEND')) exit;

require_once(BASE_DIR.MODULE_DIR.'developer/std_mod/elements/element_text.php');

class ElementUrl extends \Modules\developer\std_mod\ElementText{

    function checkField($prefix, $action, $area){
        global $parametersMod;


        switch ($action){
            case 'insert':
                $tmpLanguage = Db::getLanguageByUrl($_REQUEST[''.$prefix]);
                if($tmpLanguage){
                    return $parametersMod->getValue('standard', 'languages', 'admin_translations', 'error_duplicate_url');
                }

                break;
            case 'update':
                $tmpLanguage = Db::getLanguageByUrl($_REQUEST[''.$prefix]);
                if($tmpLanguage && $tmpLanguage['id'] != $area->currentId){
                    return $parametersMod->getValue('standard', 'languages', 'admin_translations', 'error_duplicate_url');
                }
                break;
        }

        return parent::checkField($prefix, $action, $area);
    }


}
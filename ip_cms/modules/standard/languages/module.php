<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Modules\standard\languages;

if (!defined('FRONTEND')&&!defined('BACKEND')) exit;

/** @private */
require_once (__DIR__.'/db.php');

/**
 * class to ouput the languages
 * @package ImpressPages
 */
class Module{


    public static function generateLanguageList(){
        global $site;
        $site->requireTemplate('standard/languages/template_list.php');
        return TemplateList::languages($site->getLanguages());
    }


    /**
     *
     * @deprecated Use generateLanguageList() instead.
     * @return string HTML with links to website languages
     *
     */
    public static function generatehtml(){
        global $site;
        global $parametersMod;

        if(!$parametersMod->getValue('standard', 'languages', 'options', 'multilingual'))
        return;
         
        $site->requireTemplate('standard/languages/template.php');
        $languages = array();
        foreach($site->languages as $language){
            if($language['visible']){
                $languages[] = $language;
            }
        }
        return Template::languages($languages);
    }

}
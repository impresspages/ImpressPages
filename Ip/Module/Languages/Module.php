<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Module\Languages;


/**
 * class to ouput the languages
 * @package ImpressPages
 */
class Module{


    public static function generateLanguageList(){
        global $site;
        global $parametersMod;

        if(!ipGetOption('Config.multilingual')) {
            return;
        }
         

        
        return \Ip\View::create('view/list.php', self::getViewData());
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

        if(!ipGetOption('Config.multilingual')) {
            return;
        }
        
        return \Ip\View::create('view/links.php', self::getViewData());
    }
    
    private static function getViewData() {
        global $site;
        $languages = array();
        foreach($site->getLanguages() as $language){
            if (!$language->getVisible()) {
                continue;
            }
        
            $tmpData = array();
            $tmpData['shortTitle'] = $language->getAbbreviation();
            $tmpData['longTitle'] = $language->getTitle();
            $tmpData['visible'] = $language->getVisible();
            $tmpData['current'] = $language->getCurrent();
            $tmpData['url'] = \Ip\Internal\Deprecated\Url::generate($language->getId());
            $languages[] = $tmpData;
        }
        $data = array (
            'languages' => $languages
        );
        return $data;
    }

}
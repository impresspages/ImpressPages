<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Internal\Languages;


/**
 * class to ouput the languages
 * @package ImpressPages
 */
class Model{




    //TODOX move to Ip module
    public static function generateLanguageList(){
        if(!ipGetOption('Config.multilingual')) {
            return '';
        }
         
        return \Ip\View::create('view/list.php', self::getViewData());
    }


    //TODOX move to IP module
    private static function getViewData() {
        $languages = array();

        foreach (ipContent()->getLanguages() as $language) {
            if (!$language->isVisible()) {
                continue;
            }
        
            $tmpData = array();
            $tmpData['shortTitle'] = $language->getAbbreviation();
            $tmpData['longTitle'] = $language->getTitle();
            $tmpData['visible'] = $language->isVisible();
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
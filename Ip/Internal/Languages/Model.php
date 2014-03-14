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

    public static function addLanguage($title, $abbreviation, $code, $url, $isVisible, $textDirection)
    {
        $languageOrder = ipDb()->selectValue('language', 'MAX(`languageOrder`) + 3', array());
        if (!$languageOrder) {
            $languageOrder = 1;
        }

        $params = array (
            'title' => $title,
            'abbreviation' => $abbreviation,
            'code' => $code,
            'url' => Db::newUrl($url),
            'textDirection' => $textDirection,
            'languageOrder' => $languageOrder,
            'isVisible' => $isVisible
        );
        $languageId = ipDb()->insert('language', $params);

        ipEvent('ipLanguageAdded', array('id' => $languageId));

        return $languageId;
    }

    public static function delete($id)
    {
        ipDb()->delete('language', array('id' => $id));
        ipEvent('ipBeforeLanguageDeleted', array('id' => $id));
    }

    /**
     * @return array all website languages
     */
    public static function getLanguages()
    {
        return ipDb()->selectAll('language', '*', array(), 'ORDER BY `languageOrder`');
    }






}

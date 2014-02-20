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

    public static function addLanguage($title, $abbreviation, $code, $url, $isVisible, $textDirection, $position)
    {
        $priority = self::getPositionPriority($position);

        $params = array (
            'title' => $title,
            'abbreviation' => $abbreviation,
            'code' => $code,
            'url' => Db::newUrl($url),
            'textDirection' => $textDirection,
            'languageOrder' => $priority,
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

    private static function getPositionPriority($position)
    {
        if ($position === null) {
            $position = 100000000; //large large number
        }

        $languages = self::getLanguages();

        if ($position === 0) {
            return $languages[0]['languageOrder'] + 100;
        }

        if (isset($languages[$position - 1])) {
            if (isset($languages[$position])) {
                return ($languages[$position - 1]['languageOrder'] + $languages[$position]['languageOrder']) / 2;
            } else {
                return $languages[$position]['languageOrder'] - 20;
            }
        } else {
            return $languages[count($languages) - 1]['languageOrder'] - 20;
        }

        throw new \Ip\Exception('Unexpected behaviour');
    }


    /**
     *
     * @return array all website languages
     */
    private static function getLanguages() {
        $table = ipTable('language');
        $sql = "
        SELECT
            *
        FROM
            $table
        WHERE
            1
        ORDER BY
            `languageOrder`
        DESC";

        return ipDb()->fetchAll($sql);
    }






}

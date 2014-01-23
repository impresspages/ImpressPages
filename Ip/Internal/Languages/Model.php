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

    public static function addLanguage($title, $abbreviation, $code, $url, $visible, $textDirection, $position)
    {
        $priority = self::getPositionPriority($position);

        $params = array (
            'd_long' => $title,
            'd_short' => $abbreviation,
            'code' => $code,
            'url' => Db::newUrl($url),
            'text_direction' => $textDirection,
            'row_number' => $priority,
            'visible' => $visible
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
            return $languages[0]['row_number'] + 100;
        }

        if (isset($languages[$position - 1])) {
            if (isset($languages[$position])) {
                return ($languages[$position - 1]['row_number'] + $languages[$position]['row_number']) / 2;
            } else {
                return $languages[$position]['row_number'] - 20;
            }
        } else {
            return $languages[count($languages) - 1]['row_number'] - 20;
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
            `row_number`
        DESC";

        return ipDb()->fetchAll($sql);
    }






}

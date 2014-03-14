<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Internal\Content;


/**
 *
 * Event dispatcher class
 *
 */
class Helper
{

    /**
     * @param $data
     * @return Language
     */
    public static function createLanguage($data)
    {
        $language = new \Ip\Language($data['id'], $data['code'], $data['url'], $data['title'], $data['abbreviation'], $data['isVisible'], $data['textDirection']);
        return $language;
    }
}

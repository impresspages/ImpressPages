<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal\Languages;


class Service
{
    const TEXT_DIRECTION_LTR = 'ltr';
    const TEXT_DIRECTION_RTL = 'rtl';

    public static function addLanguage($title, $abbreviation, $code, $url, $isVisible, $textDirection)
    {
        return Model::addLanguage($title, $abbreviation, $code, $url, $isVisible, $textDirection);
    }

    public static function getLanguages()
    {
        return Model::getLanguages();
    }

    public static function delete($languageId)
    {
        Model::delete($languageId);
    }

}

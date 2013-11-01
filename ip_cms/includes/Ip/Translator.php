<?php
/**
 * @package   ImpressPages
 */

namespace Ip {

    class Translator
    {

        /**
         * @var $translator \Zend\I18n\Translator\Translator
         */
        protected static $translator;

        public static function init($locale = 'en_EN')
        {
            $translator = new \Zend\I18n\Translator\Translator();

            $translator->setLocale($locale);

            $translator->addTranslationFilePattern(
                'gettext',
                \Ip\Config::themeFile('languages/'),
                '%s.mo',
                'theme-' . \Ip\Config::theme()
            );

//
//            $translator->addTranslationFilePattern(
//                'phparray',
//                \Ip\Config::themeFile('languages/'),
//                '%s.php',
//                'theme-' . \Ip\Config::theme()
//            );

            static::$translator = $translator;
        }

        public static function translate($text, $domain)
        {
            return static::$translator->translate($text, $domain);
        }

        public static function translatePlural($singular, $plural, $number, $domain)
        {
            return static::$translator->translatePlural($singular, $plural, $number, $domain);
        }

        public static function translateKeyword($keyword, $domain)
        {

        }
    }
}

namespace {

    function __($text, $domain)
    {
        return \Ip\Translator::translate($text, $domain);
    }

    function _n($singular, $plural, $number, $domain)
    {
        return \Ip\Translator::translatePlural($singular, $plural, $number, $domain);
    }

    function _x($text, $context, $domain)
    {
        return $text;
    }

    function _nx($single, $plural, $number, $context, $domain)
    {

    }

    function _k($text, $domain)
    {
        return '{{' . $text .'}}';
    }
}
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

        public static function init()
        {
            $translator = new \Zend\I18n\Translator\Translator();
            $translator->setLocale('lt_LT');
            $translator->addTranslationFilePattern(
                'gettext',
                \Ip\Config::themeFile('languages/'),
                '%s.mo',
                'theme-' . \Ip\Config::theme()
            );
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
}
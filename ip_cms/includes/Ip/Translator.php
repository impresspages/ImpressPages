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

        public static function init($locale)
        {
            $translator = new \Zend\I18n\Translator\Translator();
            // TODOX set according to language
            $translator->setLocale($locale);
            if (0) {
                $translator->addTranslationFilePattern(
                    'gettext',
                    \Ip\Config::themeFile('languages/'),
                    '%s.mo',
                    'theme-' . \Ip\Config::theme()
                );
            }

            $translator->addTranslationFilePattern(
                'phparray',
                \Ip\Config::themeFile('languages/'),
                '%s.php',
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
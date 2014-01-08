<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Translations;

class Translator
{
    /**
     * @var $translator \Zend\I18n\Translator\Translator
     */
    protected $translator;

    public function __construct()
    {
        $this->translator = new \Zend\I18n\Translator\Translator();
        $this->translator->getPluginManager()->setInvokableClass('json', 'Ip\Translator\JsonLoader');
        $this->translator->setLocale('en');
    }

    public function addTranslationFilePattern($type, $directory, $pattern, $domain)
    {
        $this->translator->addTranslationFilePattern(
            $type,
            $directory,
            $pattern,
            $domain
        );

        return $this;
    }

    public function translate($text, $domain)
    {
        return $this->translator->translate($text, $domain);
    }

    public function setLocale($locale)
    {
        $this->translator->setLocale($locale);
        return $this;
    }
}

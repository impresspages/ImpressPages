<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Translator;

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
    }

    public function addTranslationFilePattern($type, $directory, $pattern, $domain)
    {
        $this->translator->addTranslationFilePattern(
            $type,
            $directory,
            $pattern,
            $domain
        );
    }

    public function translate($text, $domain)
    {
        return $this->translator->translate($text, $domain);
    }
}

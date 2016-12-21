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

    protected $publicLocale;
    protected $adminLocale;


    protected $domains = [];

    protected $adminDomains = [];

    public function __construct()
    {
        $this->translator = new \Zend\I18n\Translator\Translator();
        $this->translator->getPluginManager()->setInvokableClass('json', 'Ip\Internal\Translations\JsonLoader');

        $this->setLocale('en');
        $this->setAdminLocale('en');
    }

    public function addTranslationFilePattern($type, $directory, $pattern, $domain)
    {
        $this->translator->addTranslationFilePattern(
            $type,
            $directory,
            $pattern,
            $domain
        );

        $this->domains[$domain] = true;

        if (substr($domain, -6) == '-admin') {
            $this->adminDomains[$domain] = 'admin';
        }

        return $this;
    }

    public function translate($text, $domain)
    {
        if (isset($this->adminDomains[$domain]) && $this->adminLocale != $this->publicLocale) {
            $this->translator->setLocale($this->adminLocale);
            $result = $this->translator->translate($text, $domain);
            $this->translator->setLocale($this->publicLocale);

            return $result;
        }

        return $this->translator->translate($text, $domain);
    }

    public function setLocale($locale)
    {
        $this->publicLocale = $locale;
        $this->translator->setLocale($locale);
        return $this;
    }

    public function getLocale()
    {
        return $this->publicLocale;
    }

    public function setAdminLocale($locale)
    {
        $this->adminLocale = $locale;
        return $this;
    }

    public function getAdminLocale()
    {
        return $this->adminLocale;
    }

    public function getRegisteredDomains()
    {
        return array_keys($this->domains);
    }
}

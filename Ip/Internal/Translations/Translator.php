<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Translations;


/**
 * Translator
 */
class Translator
{

    /**
     * @var $translator \Zend\I18n\Translator\Translator
     */
    protected $translator;
    protected $publicLocale;
    protected $adminLocale;
    protected $domains = array();
    protected $adminDomains = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translator = new \Zend\I18n\Translator\Translator();
        $this->translator->getPluginManager()->setInvokableClass('json', 'Ip\Internal\Translations\JsonLoader');

        $this->setLocale('en');
        $this->setAdminLocale('en');
    }

    /**
     * Add translation file pattern
     *
     * @param string $type
     * @param string $directory
     * @param string $pattern
     * @param string $domain
     * @return array
     */
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

    /**
     * Translate
     *
     * @param string $text
     * @param string $domain
     * @return string
     */
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

    /**
     * Set locale
     *
     * @param string $locale
     * @return string
     */
    public function setLocale($locale)
    {
        $this->publicLocale = $locale;
        $this->translator->setLocale($locale);
        return $this;
    }

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->publicLocale;
    }

    /**
     * Set admin locale
     *
     * @param string $locale
     * @return object
     */
    public function setAdminLocale($locale)
    {
        $this->adminLocale = $locale;
        return $this;
    }

    /**
     * Get admin locale
     *
     * @return string
     */
    public function getAdminLocale()
    {
        return $this->adminLocale;
    }

    /**
     * Get registered domains
     *
     * @return array
     */
    public function getRegisteredDomains()
    {
        return array_keys($this->domains);
    }

}

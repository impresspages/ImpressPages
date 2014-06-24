<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Translations;


/**
 * Event
 */
class Event
{

    /**
     * Add language
     *
     * @param array $info
     */
    public static function ipLanguageAdded_20($info)
    {
        $downloader = new Downloader();

        $languageCode = ipContent()->getLanguage($info['id'])->getCode();

        $domains = \Ip\ServiceLocator::translator()->getRegisteredDomains();

        foreach ($domains as $domain) {
            $downloader->downloadTranslation($domain, $languageCode, ipApplication()->getVersion());
        }
    }

    /**
     * Update language
     *
     * @param array $info
     */
    public static function ipLanguageUpdated($info)
    {
        if ($info['new']['code'] == $info['old']['code']) {
            return;
        }

        $downloader = new Downloader();

        $languageCode = $info['new']['code'];

        $domains = \Ip\ServiceLocator::translator()->getRegisteredDomains();

        foreach ($domains as $domain) {
            $downloader->downloadTranslation($domain, $languageCode, ipApplication()->getVersion());
        }

    }

} 

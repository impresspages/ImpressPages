<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Seo;


class Filter
{
    /**
     * Adds schema.org JSON-LD to public pages.
     */
    public static function ipHead($head, $info)
    {
        $page = ipContent()->getCurrentPage();
        $language = ipContent()->getCurrentLanguage();
        if (
            !ipGetOption('Config.structuredDataEnabled', 1)
            || !$page || !$language
            || ipRoute()->isAdmin()
            || ipIsManagementState()
        ) {
            return $head;
        }

        return $head . StructuredData::toScript(StructuredData::forPage($page, $language));
    }
}

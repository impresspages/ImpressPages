<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Seo;


/**
 * schema.org JSON-LD describing the website, its owner and the current page's place in the menu.
 */
class StructuredData
{
    /**
     * @param \Ip\Page $page
     * @param \Ip\Language $language
     * @return array JSON-LD document with an @graph of Organization (or LocalBusiness), WebSite and BreadcrumbList
     */
    public static function forPage($page, $language)
    {
        $baseUrl = ipConfig()->baseUrl();
        $name = trim((string)ipGetOptionLang('Config.websiteTitle', $language->getCode(), ''));
        if ($name === '') {
            $name = parse_url($baseUrl, PHP_URL_HOST) ?: $baseUrl;
        }

        $organizationId = $baseUrl . '#organization';
        $graph = array(
            self::organization($organizationId, $name, $language),
            array_filter(array(
                '@type' => 'WebSite',
                '@id' => $baseUrl . '#website',
                'url' => $language->getLink(),
                'name' => $name,
                'description' => self::oneLine(ipGetOptionLang('Config.websiteDescription', $language->getCode(), '')),
                'inLanguage' => $language->getCode(),
                'publisher' => array('@id' => $organizationId),
            ), [self::class, 'notEmpty']),
        );

        $breadcrumb = self::breadcrumb($page, $language, $name);
        if ($breadcrumb) {
            $graph[] = $breadcrumb;
        }

        $data = array(
            '@context' => 'https://schema.org',
            '@graph' => $graph,
        );

        return ipFilter('ipStructuredData', $data, array('page' => $page, 'language' => $language));
    }

    /**
     * @param array $data
     * @return string <script> element, safe to place in <head>
     */
    public static function toScript($data)
    {
        $json = json_encode(
            $data,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_INVALID_UTF8_SUBSTITUTE
        );
        if ($json === false) {
            return '';
        }
        return '<script type="application/ld+json">' . $json . '</script>' . "\n";
    }

    protected static function organization($id, $name, $language)
    {
        $type = ipGetOption('Config.organizationType', 'Organization') === 'LocalBusiness' ? 'LocalBusiness' : 'Organization';

        $address = [];
        foreach (Model::addressFields() as $option => $property) {
            $value = self::oneLine(ipGetOption('Config.' . $option, ''));
            if ($value !== '') {
                $address[$property] = $value;
            }
        }
        if ($address) {
            $address = array('@type' => 'PostalAddress') + $address;
        }

        $sameAs = [];
        foreach (preg_split('/\s+/', (string)ipGetOption('Config.organizationSameAs', '')) as $url) {
            if (preg_match('#^https?://#i', $url) && filter_var($url, FILTER_VALIDATE_URL)) {
                $sameAs[] = $url;
            }
        }

        $logo = Model::logoUrl();

        return array_filter(array(
            '@type' => $type,
            '@id' => $id,
            'name' => $name,
            'url' => ipConfig()->baseUrl(),
            'logo' => $logo,
            // Google expects an image for a LocalBusiness
            'image' => $type === 'LocalBusiness' ? $logo : null,
            'telephone' => self::oneLine(ipGetOption('Config.organizationTelephone', '')),
            'address' => $address,
            'sameAs' => $sameAs,
        ), [self::class, 'notEmpty']);
    }

    /**
     * Home page, then the current page's ancestors, then the page itself. None on the home page.
     */
    protected static function breadcrumb($page, $language, $siteName)
    {
        if (!$page || $page->getId() == ipContent()->getDefaultPageId($language->getCode())) {
            return null;
        }

        $items = array(array('name' => $siteName, 'url' => $language->getLink()));
        foreach (ipContent()->getBreadcrumb($page->getId()) as $crumb) {
            if ($crumb->getId() == ipContent()->getDefaultPageId($language->getCode())) {
                continue;
            }
            $items[] = array('name' => $crumb->getTitle(), 'url' => $crumb->getLink());
        }

        $elements = [];
        foreach ($items as $index => $item) {
            $elements[] = array(
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => self::oneLine($item['name']),
                'item' => $item['url'],
            );
        }

        return array(
            '@type' => 'BreadcrumbList',
            'itemListElement' => $elements,
        );
    }

    protected static function oneLine($value)
    {
        return trim(preg_replace('/\s+/', ' ', (string)$value));
    }

    protected static function notEmpty($value)
    {
        return $value !== null && $value !== '' && $value !== [];
    }
}

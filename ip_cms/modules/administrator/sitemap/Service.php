<?php
/**
 * @package ImpressPages
 *
 */
namespace Modules\administrator\sitemap;



class Service {


    /**
     * @return Service
     */
    public static function instance()
    {
        return new Service();
    }

    public function generateSitemapIcon()
    {
        $site = \Ip\ServiceLocator::getSite();
        $sitemapZone = $newsletterBox = $site->getZoneByModule('administrator', 'sitemap');
        if (!$sitemapZone) {
            return;
        }

        $sitemapUrl = $site->generateUrl(null, $sitemapZone->getName());

        $variables = array (
            'sitemapUrl' => $sitemapUrl
        );

        $sitemapIconView = \Ip\View::create('view/icon.php', $variables);
        return $sitemapIconView->render();
    }

}
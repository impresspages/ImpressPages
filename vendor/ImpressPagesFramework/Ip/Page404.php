<?php
/**
 * @package ImpressPagegetCurrentPage
 *
 *
 */

namespace Ip;


class Page404 extends Page
{
    public function __construct()
    {
    }

    public function getTitle()
    {
        return ipGetOptionLang('Config.websiteTitle', null, 'Page not found');
    }

    public function getMetaTitle()
    {
        return ipGetOptionLang('Config.websiteTitle', null, 'Page not found');
    }

    public function generateContent()
    {
    }

    public function getType()
    {
        return 'error404';
    }
}

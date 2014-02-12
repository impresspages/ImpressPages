<?php
/**
 * @package ImpressPagegetCurrentPage
 *
 *
 */

namespace Ip;


use Ip\Page;

class Page404 extends Page
{
    public function __construct()
    {
    }

    public function getNavigationTitle()
    {
        return ipGetOption('Config.websiteTitle', 'Page not found');
    }

    public function getPageTitle()
    {
        return ipGetOption('Config.websiteTitle', 'Page not found');
    }

    public function generateContent()
    {
    }

    public function getType()
    {
        return 'error404';
    }
}

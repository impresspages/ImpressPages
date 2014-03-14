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

    public function getTitle()
    {
        return ipGetOption('Config.websiteTitle', 'Page not found');
    }

    public function getMetaTitle()
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

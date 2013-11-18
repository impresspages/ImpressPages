<?php
/**
 * @package ImpressPagegetCurrentPage
 *
 *
 */

namespace Ip;


use Ip\Page;

class Page404 extends Page {
    public function getButtonTitle()
    {
        return ipGetOption('Config.websiteTitle');
    }

    public function getPageTitle()
    {
        return ipGetOption('Config.websiteTitle');
    }

    public function generateContent()
    {
    }

    public function getType()
    {
        return 'error404';
    }
}
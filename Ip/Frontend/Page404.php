<?php
/**
 * @package ImpressPagegetCurrentPage
 *
 *
 */

namespace Ip\Frontend;


class Page404 extends Element {
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
}
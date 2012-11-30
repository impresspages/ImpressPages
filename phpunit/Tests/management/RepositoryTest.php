<?php
    /**
     * @package   ImpressPages
     * @copyright Copyright (C) 2012 ImpressPages LTD.
     * @license   GNU/GPL, see ip_license.html
     */

class RepositoryTestTest extends \PhpUnit\SeleniumTestCase
{

 
    public function testNewFilesUpload()
    {
        $installation = new \PhpUnit\Helper\Installation();
        $installation->install();

        $url = $installation->getInstallationUrl();

        $ipActions = new \PhpUnit\Helper\IpActions($this, $installation);
        $ipActions->login();
        $ipActions->addWidget('IpFile');
        $this->waitForElementPresent('css=.ipAdminWidget-IpFile .ipmBrowseButton');
        $this->click('css=.ipAdminWidget-IpFile .ipmBrowseButton');
        $this->waitForElementPresent('css=.ipModRepositoryPopup .ipmBrowseButton');

        $this->type("css=.plupload input", "/var/www/index.html");

//        script to display plupload file input
//        $this->getEval( "
//        var window = this.browserbot.getUserWindow();
//        //window.content.jQuery('#ipModRepositoryTabUpload').hide();
//        window.content.jQuery('.plupload').css('opacity', '1');
//        window.content.jQuery('.plupload').css('overflow', 'scroll');
//        window.content.jQuery('.plupload').css('height', '100px');
//        window.content.jQuery('.plupload').css('width', '300px');
//        window.content.jQuery('.plupload').css('z-index', '10');
//        window.content.jQuery('.plupload input').css('font-size', '14px');
//        window.content.jQuery('.plupload input').css('margin-top', '50px');
//        ");


    }
}
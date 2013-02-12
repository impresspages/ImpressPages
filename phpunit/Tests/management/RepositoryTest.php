<?php
    /**
     * @package   ImpressPages
     * @copyright Copyright (C) 2012 ImpressPages LTD.
     * @license   GNU/GPL, see ip_license.html
     */

class RepositoryTestTest extends \PhpUnit\SeleniumTestCase
{

 
    public function testNewFilesUploadRemoval()
    {
        $installation = new \PhpUnit\Helper\Installation();
        $installation->install();

        $ipActions = new \PhpUnit\Helper\IpActions($this, $installation);
        $ipActions->login();

        //add file widget
        $ipActions->addWidget('IpFile');
        $this->waitForElementPresent('css=.ipAdminWidget-IpFile .ipmBrowseButton');
        $this->click('css=.ipAdminWidget-IpFile .ipmBrowseButton');
        $this->waitForElementPresent('css=.ipModuleRepositoryPopup .ipmBrowseButton');



        //try to upload file
        $this->assertEquals(file_exists($installation->getConfig('BASE_DIR').$installation->getConfig('FILE_DIR').'testFile.txt'), FALSE);
        $this->type("css=.plupload input", TEST_FIXTURE_DIR."Repository/testFile.txt");
        $this->waitForElementPresent('css=#ipModuleRepositoryTabUpload .ipmFiles .ipmFile');
        $this->click('css=#ipModuleRepositoryTabUpload .ipaConfirm');
        sleep(1); //wait for popup to close
        $this->assertElementNotPresent('css=#ipModuleRepositoryTabUpload');
        $this->click('css=.ipActionWidgetCancel');
        $this->waitForElementNotPresent('css=.ipActionWidgetCancel');
        $this->assertEquals(file_exists($installation->getConfig('BASE_DIR').$installation->getConfig('FILE_DIR').'testFile.txt'), TRUE);

        //check if file is automatically removed if not used
        \PhpUnit\Helper\Time::changeTime(60*60*24*7 + 1); //+7 days and 1s.
        $this->open($installation->getInstallationUrl().'ip_cron.php'); //cron should remove our file because it is not used by any widget yet
        \PhpUnit\Helper\Time::restoreTime(); //+7 days and 1s.
        $this->assertEquals(file_exists($installation->getConfig('BASE_DIR').$installation->getConfig('FILE_DIR').'testFile.txt'), FALSE);

        //add file widget again
        $ipActions->login(); //because we are in ip_cron now
        $ipActions->addWidget('IpFile');
        $this->waitForElementPresent('css=.ipAdminWidget-IpFile .ipmBrowseButton');
        $this->click('css=.ipAdminWidget-IpFile .ipmBrowseButton');
        $this->waitForElementPresent('css=.ipModuleRepositoryPopup .ipmBrowseButton');

        //try to upload file
        $this->assertEquals(file_exists($installation->getConfig('BASE_DIR').$installation->getConfig('FILE_DIR').'testFile.txt'), FALSE);
        $this->type("css=.plupload input", TEST_FIXTURE_DIR."Repository/testFile.txt");
        $this->waitForElementPresent('css=#ipModuleRepositoryTabUpload .ipmFiles .ipmFile');
        $this->click('css=#ipModuleRepositoryTabUpload .ipaConfirm');
        sleep(1); //wait for popup to close
        $this->assertElementNotPresent('css=#ipModuleRepositoryTabUpload');
        $this->click('css=.ipActionWidgetSave');
        $this->waitForElementNotPresent('css=.ipActionWidgetCancel');
        $this->assertEquals(file_exists($installation->getConfig('BASE_DIR').$installation->getConfig('FILE_DIR').'testFile.txt'), TRUE);


        //check if file is NOT automatically removed
        \PhpUnit\Helper\Time::changeTime(60*60*24*15 + 1); //+7 days and 1s.
        $this->open($installation->getInstallationUrl().'ip_cron.php'); //cron should remove our file because it is not used by any widget yet
        \PhpUnit\Helper\Time::restoreTime(); //+7 days and 1s.
        $this->assertEquals(file_exists($installation->getConfig('BASE_DIR').$installation->getConfig('FILE_DIR').'testFile.txt'), TRUE);

    }





//        script to display plupload file input
//        $this->getEval( "
//        var window = this.browserbot.getUserWindow();
//        //window.content.jQuery('#ipModuleRepositoryTabUpload').hide();
//        window.content.jQuery('.plupload').css('opacity', '1');
//        window.content.jQuery('.plupload').css('overflow', 'scroll');
//        window.content.jQuery('.plupload').css('height', '100px');
//        window.content.jQuery('.plupload').css('width', '300px');
//        window.content.jQuery('.plupload').css('z-index', '10');
//        window.content.jQuery('.plupload input').css('font-size', '14px');
//        window.content.jQuery('.plupload input').css('margin-top', '50px');
//        ");


}







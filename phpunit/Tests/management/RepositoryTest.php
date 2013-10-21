<?php
    /**
     * @package   ImpressPages
     *
     *
     */

class RepositoryTestTest extends \PhpUnit\SeleniumTestCase
{

 
    public function testNewFilesUploadRemoval()
    {
        $installation = new \PhpUnit\Helper\Installation();
        $installation->install();

        $ipActions = new \PhpUnit\Helper\IpActions($this, $installation);
        $ipActions->login();

        $this->windowMaximize();

        //add file widget
        $ipActions->addWidget('IpFile');
        $this->waitForElementPresent('css=.ipAdminWidget-IpFile .ipmBrowseButton');
        $this->click('css=.ipAdminWidget-IpFile .ipmBrowseButton');
        $this->waitForElementPresent('css=.ipModuleRepositoryPopup .ipmBrowseButton');



        //try to upload file
        $this->assertEquals(file_exists($installation->getConfig('BASE_DIR').$installation->getConfig('FILE_DIR').'repository/testFile.txt'), FALSE);
        $this->type("css=.plupload input", TEST_BASE_DIR.TEST_FIXTURE_DIR."Repository/testFile.txt");
        $this->waitForElementPresent('css=#ipModuleRepositoryTabUpload .ipmRecentList li');
        $this->click('css=#ipModuleRepositoryTabUpload .ipmRepositoryActions .ipaConfirm');
        sleep(1); //wait for popup to close
        $this->assertElementNotPresent('css=#ipModuleRepositoryTabUpload');
        $this->click('css=.ipActionWidgetCancel');
        $this->waitForElementNotPresent('css=.ipActionWidgetCancel');
        $this->assertEquals(file_exists($installation->getConfig('BASE_DIR').$installation->getConfig('FILE_DIR').'repository/testFile.txt'), TRUE);

        //check if file is NOT automatically removed if not used
        //check if file is NOT automatically removed if not used
        \PhpUnit\Helper\Time::changeTime(60*60*24*7 + 1); //+7 days and 1s.
        $this->open($installation->getInstallationUrl().'ip_cron.php'); //cron should remove our file because it is not used by any widget yet
        \PhpUnit\Helper\Time::restoreTime(); //+7 days and 1s.
        $this->assertEquals(file_exists($installation->getConfig('BASE_DIR').$installation->getConfig('FILE_DIR').'repository/testFile.txt'), TRUE);

        $ipActions->login();

        //add file widget and try to add our last uploaded file
        $ipActions->addWidget('IpFile');
        $this->waitForElementPresent('css=.ipAdminWidget-IpFile .ipmBrowseButton');
        $this->click('css=.ipAdminWidget-IpFile .ipmBrowseButton');
        $this->waitForElementPresent('css=.ipModuleRepositoryPopup .ipmBrowseButton');

        $this->type("css=.plupload input", TEST_BASE_DIR.TEST_FIXTURE_DIR."Repository/testFile.txt");
        $this->waitForElementPresent('css=#ipModuleRepositoryTabUpload .ipmList li');
        $this->click('css=#ipModuleRepositoryTabUpload .ipmRepositoryActions .ipaConfirm');

        sleep(2); //wait for popup to close
        $this->waitForElementPresent('css=.ipWidget_ipFile_container input');
        $this->click('css=.ipaConfirm');
        $this->waitForElementPresent('css=.ipWidget-IpFile a');
        $this->assertText('css=.ipPreviewWidget.ipWidget-IpFile ul a', 'testFile_1.txt');
    }

}







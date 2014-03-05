<?php
    /**
     * @package   ImpressPages
     *
     *
     */

/**
 * @group Sauce
 * @group Selenium
 */
class RepositoryTest extends \PhpUnit\Helper\MinkTestCase
{
    public function testNewFilesUploadRemoval()
    {
        $installation = new \PhpUnit\Helper\Installation();
        $installation->install();

        $adminHelper = new \PhpUnit\Helper\User\Admin($this->session(), $installation);
        $adminHelper->login();

        // $this->windowMaximize();

        //add file widget

        sleep(1);
        $adminHelper->addWidget('File');

        // Try to upload file
        $this->assertFalse(file_exists($installation->getInstallationDir() . 'file/repository/testFile.txt'));
        $this->find('.plupload input')->setValue(TEST_FIXTURE_DIR."Repository/testFile.txt");

        $this->find('.ipWidget-File .ipsWidgetDelete');
        $this->find('#ipModuleRepositoryTabUpload .ipmRepositoryActions .ipsSelectionConfirm')->click();

        // wait for popup to close
        $popupClosed = $this->waitForElementNotPresent('#ipModuleRepositoryTabUpload');
        $this->assertTrue($popupClosed);

        $this->find('#ipWidgetFilePopup .ipsCancel')->click();

        $this->find('.ipWidget-File .ipsWidgetDelete');
        $this->session()->executeScript("$('.ipWidget-File .ipsWidgetDelete').click()");
        $removed = $this->waitForElementNotPresent('.ipWidget-File');
        $this->assertTrue($removed);

        $this->assertTrue(file_exists($installation->getInstallationDir() . 'file/repository/testFile.txt'));

        $adminHelper->logout();
        $adminHelper->login();
        sleep(1);

        //add file widget and try to add our last uploaded file
        $adminHelper->addWidget('File');

        $this->find('.plupload input')->setValue(TEST_FIXTURE_DIR."Repository/testFile.txt");

        $this->find('.ipWidget-File .ipsWidgetDelete');
        $this->find('#ipModuleRepositoryTabUpload .ipmRepositoryActions .ipsSelectionConfirm')->click();

        $this->find('.ipWidget_ipFile_container input');
        $this->find('#ipWidgetFilePopup .ipsConfirm')->click();

        $this->assertEquals('testFile_1.txt', $this->find('.ipWidget.ipWidget-File ul a')->getText());
    }
}







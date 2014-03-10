<?php
/**
 * @package   ImpressPages
 *
 *
 */

/**
 * @group ignoreOnTravis
 */
class UpdateTest extends \PhpUnit\SeleniumTestCase
{
    public function testGeneral()
    {
        $installation = new \PhpUnit\Helper\Installation('2.0rc2');
        $installation->install();

        $url = $installation->getInstallationUrl();

        //check installation successful
        $this->open($url);
        $this->assertElementPresent('css=.sitename');
        //$this->assertNoErrors(); 2.0rc2 throws warnings on PHP 5.4

        //check update review page is fine
        $updateService = new \IpUpdate\Library\Service($installation->getInstallationDir());
        $installation->setupUpdate();

        $this->open($url.'update');
        $this->waitForElementPresent('css=.actProceed');

        $this->assertTextPresent('Form widget has been introduced');
        $this->assertTextPresent('Now ImpressPages core does not include any JavaScript by default');

        //start update process
        $this->click('css=.actProceed');

        //assert success
        $this->waitForElementPresent('css=.seleniumCompleted');

        //check update was successful
        $this->open($url);
        $this->assertElementPresent('css=.sitename');
        $this->assertNoErrors();

    }

    public function testWritePermissionError()
    {
        $installation = new \PhpUnit\Helper\Installation('2.0rc2');
        $installation->install();

        $url = $installation->getInstallationUrl();

        //check installation successful
        $this->open($url);
        $this->assertElementPresent('css=.sitename');
        //$this->assertNoErrors(); 2.0rc2 throws warnings on PHP 5.4

        //checkupdate review page is fine
        $updateService = new \IpUpdate\Library\Service($installation->getInstallationDir());
        $installation->setupUpdate();

        $fs = new \PhpUnit\Helper\FileSystem2();
        $fs->clean($installation->getInstallationDir().'Ip/');
        symlink(TEST_UNWRITABLE_DIR, $installation->getInstallationDir().'Ip/unwritableDir');

        $this->open($url.'update');
        $this->waitForElementPresent('css=.actProceed');

        $this->assertTextPresent('Form widget has been introduced');
        $this->assertTextPresent('Now ImpressPages core does not include any JavaScript by default');

        //start update process
        $this->click('css=.actProceed');

        //wait for error
        $this->waitForElementPresent('css=.seleniumWritePermission');

        //fix error
        unlink($installation->getInstallationDir().'Ip/unwritableDir');

        //resume update process
        $this->click('css=.actProceed');


        //assert success
        $this->waitForElementPresent('css=.seleniumCompleted');


        //check update was successful
        $this->open($url);
        $this->assertElementPresent('css=.sitename');
        $this->assertNoErrors();
    }

    public function testInProgressError()
    {
        $installation = new \PhpUnit\Helper\Installation('2.0rc2');
        $installation->install();

        $url = $installation->getInstallationUrl();
        $dir = $installation->getInstallationDir();

        //check installation successful
        $this->open($url);
        $this->assertElementPresent('css=.sitename');
        //$this->assertNoErrors(); 2.0rc2 throws warnings on PHP 5.4

        //setup update
        $updateService = new \IpUpdate\Library\Service($installation->getInstallationDir());
        $installation->setupUpdate();

        //fake another update process in progress
        $tmpStorageDir = $installation->getConfig('baseDir') . 'file/tmp/update/';
        $fs = new \PhpUnit\Helper\FileSystem();
        mkdir($tmpStorageDir);
        file_put_contents($tmpStorageDir.'inProgress', '1');
        $fs->chmod($tmpStorageDir, 0777);

        //open update page
        $this->open($url.'update');
        $this->waitForElementPresent('css=.actProceed');
        $this->click('css=.actProceed');
        //start update process
        $this->waitForElementPresent('css=h1');
        $this->assertTextPresent('Another update process in progress');

        //reset the lock
        $this->click('css=.actResetLock');

        //assert success
        $this->waitForElementPresent('css=.seleniumCompleted');

        //check update was successful
        $this->open($url);
        $this->assertElementPresent('css=.sitename');
        $this->assertNoErrors();
    }

    public function testUnknownVersionError()
    {
        $installation = new \PhpUnit\Helper\Installation('2.0rc2');
        $installation->install();

        $url = $installation->getInstallationUrl();
        $dir = $installation->getInstallationDir();

        //setup unknown version number

        $sql = "
        UPDATE
            " . ipTable('variables') . "
        SET
            `value` = 'unknown'
        WHERE
            `name` = 'version'
        ";
        // TODOX use correct connection
        // $conn = $installation->getDbConn();
        ipDb()->execute($sql);



        //setup update
        $installation->setupUpdate();



        $this->open($url.'update');
        $this->waitForElementPresent('css=.seleniumCompleted');
        $this->assertTextPresent('Your system has been successfully updated');

    }

    public function testUpdateButtonOnSystemTab()
    {
        $installation = new \PhpUnit\Helper\Installation();
        $installation->install();



        $sql = "update `".$installation->getDbPrefix()."variables` set `value` = '2.3' where
        `name` = 'version'";
        // TODOX use correct connection
        // $conn = $installation->getDbConn();
        ipDb()->execute($sql);

        $ipActions = new \PhpUnit\Helper\IpActions($this, $installation);
        $ipActions->login();
        $ipActions->openModule('system');
        $this->waitForElementPresent('css=.actStartUpdate');

        $this->click('css=.actStartUpdate');
        $this->waitForText('css=h1', 'Overview');
        $this->assertNoErrors();

    }

}

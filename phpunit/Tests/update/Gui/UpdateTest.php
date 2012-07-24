<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2012 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */

class UpdateTest extends \IpUpdate\PhpUnit\UpdateSeleniumTestCase
{

 
    public function testGeneral()
    {
        $installation = new \IpUpdate\PhpUnit\Helper\Installation('2.0rc2');
        $installation->install();
        
        $url = $installation->getInstallationUrl();
        
        //check installation successful
        $this->open($url);
        $this->assertElementPresent('css=.sitename');
        $this->assertNoErrors();
        
        //checkupdate review page is fine
        $updateService = new \IpUpdate\Library\Service($installation->getInstallationDir());
        $installation->setupUpdate($updateService->getDestinationVersion());
        $this->open($url.'update');
        $this->assertNoErrors();
        $this->assertTextPresent('IpForm widget has been introduced');
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
        $installation = new \IpUpdate\PhpUnit\Helper\Installation('2.0rc2');
        $installation->install();
        
        $url = $installation->getInstallationUrl();
        
        //check installation successful
        $this->open($url);
        $this->assertElementPresent('css=.sitename');
        $this->assertNoErrors();
        
        //checkupdate review page is fine
        $updateService = new \IpUpdate\Library\Service($installation->getInstallationDir());
        $installation->setupUpdate($updateService->getDestinationVersion());
        
        $fs = new \IpUpdate\Library\Helper\FileSystem();
        $fs->clean($installation->getInstallationDir().'ip_cms/');
        symlink(TEST_UNWRITABLE_DIR, $installation->getInstallationDir().'ip_cms/unwritableDir');

        $this->open($url.'update');
        $this->assertNoErrors();
        $this->assertTextPresent('IpForm widget has been introduced');
        $this->assertTextPresent('Now ImpressPages core does not include any JavaScript by default');

        //start update process
        $this->click('css=.actProceed');
        
        //wait for error
        $this->waitForElementPresent('css=.seleniumWritePermission');

        //fix error
        unlink($installation->getInstallationDir().'ip_cms/unwritableDir');

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
        $installation = new \IpUpdate\PhpUnit\Helper\Installation('2.0rc2');
        $installation->install();
        
        $url = $installation->getInstallationUrl();
        $dir = $installation->getInstallationDir();
        
        //check installation successful
        $this->open($url);
        $this->assertElementPresent('css=.sitename');
        $this->assertNoErrors();
        
        //setup update
        $updateService = new \IpUpdate\Library\Service($installation->getInstallationDir());
        $installation->setupUpdate($updateService->getDestinationVersion());
        
        //fake another update process in progress
        $tmpStorageDir = $installation->getConfig('BASE_DIR').$installation->getConfig('TMP_FILE_DIR').'update/';
        $fs = new \IpUpdate\Library\Helper\FileSystem();
        $fs->createWritableDir($tmpStorageDir);
        file_put_contents($tmpStorageDir.'inProgress', '1');

        //open update page
        $this->open($url.'update');
        $this->click('css=.actProceed');

        //start update process
        $this->assertNoErrors();
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
        $installation = new \IpUpdate\PhpUnit\Helper\Installation('2.0rc2');
        $installation->install();
        
        $url = $installation->getInstallationUrl();
        $dir = $installation->getInstallationDir();

        //setup unknown version number
        $conn = $installation->getDbConn();
        $sql = "
        UPDATE
            `".$installation->getConfig('DB_PREF')."variables`
        SET
            `value` = 'unknown'
        WHERE
            `name` = 'version'
        ";
        $rs = mysql_query($sql);
        if (!$rs) {
            throw new \Exception("Can't update installation version. ".mysql_error());
        }
        
        
        
        //setup update
        $updateService = new \IpUpdate\Library\Service($installation->getInstallationDir());
        $installation->setupUpdate('2.4');
        
        
        
        $this->open($url.'update');
        $this->assertNoErrors();
        $this->assertTextPresent('Your system has been successfully updated');

    }

}
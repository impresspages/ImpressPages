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
        
        //check update was successful
        $this->open($url);
        $this->assertElementPresent('css=.sitename');
        $this->assertNoErrors();        
    }
    
}
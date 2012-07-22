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
    }
    
}
<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2012 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */

class UpdateTest extends \IpUpdate\PhpUnit\UpdateSeleniumTestCase
{

 
    public function testTitle()
    {
        $installation = new \IpUpdate\PhpUnit\Helper\Installation('2.0rc2');
        $installation->install();
        
        $url = $installation->getInstallationUrl();
        $this->open($url);
        $this->assertElementPresent('css=.sitename');
        
    }
    
}
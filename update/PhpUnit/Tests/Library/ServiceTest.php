<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2012 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */

class ServiceTest extends \IpUpdate\PhpUnit\UpdateTestCase
{
    public function testCurrentVersion()
    {
        $installation = new \IpUpdate\PhpUnit\Helper\Installation('2.3');
        $installation->install();

        $service = new \IpUpdate\Library\Service($installation->getInstallationDir());
        $version = $service->getCurrentVersion();
        $this->assertEquals('2.3', $version);

        $installation->uninstall();
    }
    
    public function testProcess()
    {
        $installation = new \IpUpdate\PhpUnit\Helper\Installation('2.3');
        $installation->install();

        $service = new \IpUpdate\Library\Service($installation->getInstallationDir());
        $version = $service->getCurrentVersion();
        $this->assertEquals('2.3', $version);

        $installation->uninstall();
    }
}

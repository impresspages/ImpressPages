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
        $service = new IpUpdate\Library\Service(INSTALLATION_DIR);
        $version = $service->getCurrentVersion();
        
        $this->assertEquals('2.3', $version);
    }
}

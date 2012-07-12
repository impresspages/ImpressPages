<?php

class ServiceTest extends \IpUpdate\PhpUnit\UpdateTestCase
{
    public function testCurrentVersion()
    {
        $service = new IpUpdate\Library\Service(INSTALLATION_DIR);
        $version = $service->getCurrentVersion();
        
        $this->assertEquals('2.3', $version);
    }
}

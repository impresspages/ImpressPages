<?php

class ServiceTest extends UpdateTestCase
{
    public function testCurrentVersion()
    {
        $service = new IpUpdate\Library\Service(INSTALLATION_DIR);
        $version = $service->getCurrentVersion();
        
        $this->assertEquals('2.3', $version);
    }
}

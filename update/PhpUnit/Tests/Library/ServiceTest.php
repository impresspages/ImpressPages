<?php

class ServiceTest extends PHPUnit_Framework_TestCase
{
    public function testCurrentVersion()
    {
        $service = new IpUpdate\Library\Service(INSTALLATION_DIR);
        $version = $service->getCurrentVersion();
        
        $this->assertEquals('2.3', $version);
    }
}

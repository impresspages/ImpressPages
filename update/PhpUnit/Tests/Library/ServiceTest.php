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
        
        //installl
        
        $installation = new \IpUpdate\PhpUnit\Helper\Installation('2.3');
        $installation->install();

        $service = new \IpUpdate\Library\Service($installation->getInstallationDir());

        $version = $service->getCurrentVersion();
        $this->assertEquals('2.3', $version);
        $this->assertUrlResponse($installation->getInstallationUrl(), 200);
        
        $configurationParser = new \IpUpdate\Library\Model\ConfigurationParser();
        $cf = $configurationParser->parse($installation->getInstallationDir());
        $updateModel = new \IpUpdate\Library\Model\Update($cf);
        
        //check new version download
        $updateModel->proceed(\IpUpdate\Library\Model\Update::STEP_CLOSE_WEBSITE);
        
        //check maintenance mode
        
        file_put_contents($installation->getInstallationDir().'maintenance.php', '<?p'.'hp echo \'MAINTENANCE\'; ?>');
        $updateModel->proceed(\IpUpdate\Library\Model\Update::STEP_CLOSE_WEBSITE);
        
        $version = $service->getCurrentVersion();
        $this->assertUrlResponse($installation->getInstallationUrl(), 503, 'MAINTENANCE');
        
        //update
        
        $service->proceed();
        $version = $service->getCurrentVersion();
        $this->assertEquals('2.4', $version);
        
        //clean up
        $installation->uninstall();
    }
    
    private function assertUrlResponse($url, $responseCode = null, $content = null)
    {
        // INIT CURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $answer = curl_exec($ch);
        
        if ($responseCode !== null) {
            $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $this->assertEquals($responseCode, $httpStatus);
        }
        
        if ($content != null) {
            $this->assertEquals($answer, $content);
        }
        
    }
}

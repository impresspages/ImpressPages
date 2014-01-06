<?php
/**
 * @package   ImpressPages
 *
 *
 */

class ServiceTest extends \PhpUnit\GeneralTestCase
{
    /**
     * @group ignoreOnTravis
     * @large
     */
    public function testCurrentVersion()
    {
        // TODOX fix before release
        $this->markTestSkipped();

        $installation = new \PhpUnit\Helper\Installation('2.3');
        $installation->install();
        $service = new \IpUpdate\Library\Service($installation->getInstallationDir());
        $version = $service->getCurrentVersion();
        $this->assertEquals('2.3', $version);

        $installation->uninstall();
    }

    /**
     * @group ignoreOnTravis
     * @large
     */
    public function testProcess()
    {
        // TODOX fix before release
        $this->markTestSkipped();

        $installation = new \PhpUnit\Helper\Installation('2.0rc2');
        $installation->install();

        $service = new \IpUpdate\Library\Service($installation->getInstallationDir());

        $version = $service->getCurrentVersion();
        $this->assertEquals('2.0rc2', $version);
        $this->assertUrlResponse($installation->getInstallationUrl(), 200);

        $configurationParser = new \IpUpdate\Library\Model\ConfigurationParser();
        $cf = $configurationParser->parse($installation->getInstallationDir());
        $updateModel = new \IpUpdate\Library\Model\Update($cf);

        //check new version download
        $updateModel->proceed(\IpUpdate\Library\Model\Update::STEP_CLOSE_WEBSITE);
        
        //check maintenance mode

        file_put_contents($installation->getInstallationDir().'maintenance.php', '<?p'.'hp echo \'MAINTENANCE\'; ?>');

        $version = $service->getCurrentVersion();
        $this->assertUrlResponse($installation->getInstallationUrl(), 503, 'MAINTENANCE');
        
        //check if old files have been removed
        $updateModel->proceed(\IpUpdate\Library\Model\Update::STEP_REMOVE_OLD_FILES);
        $this->assertEquals(2, count(scandir($installation->getInstallationDir().'Ip')));
        $this->assertEquals('', file_get_contents($installation->getInstallationDir().'ip_backend_worker.php'));
        $this->assertEquals('', file_get_contents($installation->getInstallationDir().'ip_license.html'));
        $this->assertEquals('', file_get_contents($installation->getInstallationDir().'sitemap.php'));

        //database migrations
        $service->proceed(\IpUpdate\Library\Model\Update::STEP_RUN_MIGRATIONS);

        //put new files
        $service->proceed(\IpUpdate\Library\Model\Update::STEP_WRITE_NEW_FILES);
        $this->assertEquals(true, count(scandir($installation->getInstallationDir().'Ip')) > 2);
        $this->assertEquals(true, strlen(file_get_contents($installation->getInstallationDir().'ip_license.html')) > 10);
        $this->assertEquals(true, strlen(file_get_contents($installation->getInstallationDir().'sitemap.php')) > 10);

        //publish website
        $service->proceed(\IpUpdate\Library\Model\Update::STEP_FINISH);
        $this->assertUrlResponse($installation->getInstallationUrl(), 200);

        $version = $service->getCurrentVersion();
        $this->assertEquals(CURRENT_VERSION, $version);


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

<?php
/**
 * @package   ImpressPages
 *
 *
 */

class NetTest extends \PhpUnit\GeneralTestCase
{

    public function testDownloadFile()
    {
        $net = new \PhpUnit\Helper\Net();
        $destination = TEST_TMP_DIR.'netDownloadFileTest.txt';
        $this->assertEquals(false, file_exists($destination));
        $success = $net->downloadFile('http://www.google.com/robots.txt', $destination);
        $this->assertEquals(true, $success);
        $this->assertEquals(true, file_exists($destination));
        $this->assertEquals(true, filesize($destination) > 1000);
    }

}

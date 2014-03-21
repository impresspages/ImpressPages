<?php

namespace Tests\Ip\Internal\Translations;

use PhpUnit\Helper\TestEnvironment;
use \Ip\Internal\Translations\Downloader;

class DownloaderTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        TestEnvironment::setup();
    }

    public function testDownloadLanguage()
    {
        $translationFilename = ipFile('file/translations/original/Ip-lt.json');

        $this->assertFalse(file_exists($translationFilename), 'Translation file should not exist');

        $downloader = new Downloader();
        $wasDownloaded = $downloader->downloadTranslation('Ip', 'lt', '4.0.2');
        $this->assertTrue($wasDownloaded, 'Translation was not downloaded');

        $this->assertTrue(file_exists($translationFilename), 'Translation file was not downloaded');

        $json = json_decode(file_get_contents($translationFilename), true);

        $this->assertTrue(!empty($json['Name']), 'Downloaded json translation has no required data');
    }
}

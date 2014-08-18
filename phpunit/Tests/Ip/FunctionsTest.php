<?php


namespace Tests\Ip;


use PhpUnit\Helper\TestEnvironment;

class FunctionsTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        TestEnvironment::setupCode();
    }

    public function testIpformatBytes()
    {
        $answer = ipFormatBytes('100', 'test');
        $this->assertEquals('100 B', $answer);

        $answer = ipFormatBytes('1200', 'test');
        $this->assertEquals('1 KB', $answer);

        $answer = ipFormatBytes('1600', 'test');
        $this->assertEquals('2 KB', $answer);

        $answer = ipFormatBytes('1500', 'test', 1);
        $this->assertEquals('1 KB', $answer);  //kilobytes don't use precision

        $answer = ipFormatBytes('1500000', 'test', 1);
        $this->assertEquals('1,4 MB', $answer);  //megabytes uses precision

        $answer = ipFormatBytes('1600000000', 'test');
        $this->assertEquals('1 GB', $answer);  //rounded

    }


    public function testIpFormatPrice()
    {
        $answer = ipFormatPrice(1000, 'USD', 'test');
        $this->assertEquals('$10.00', $answer);

    }

    public function testIpFormatDate()
    {
        date_default_timezone_set('Etc/GMT-0');
        $answer = ipFormatDate(1401190316, 'test');
        $this->assertEquals('5/27/14', $answer);

    }

    public function testIpFormatTime()
    {
        date_default_timezone_set('Etc/GMT-0');
        $answer = ipFormatTime(1401190316, 'test');
        $this->assertEquals('11:31 AM', $answer);

    }

    public function testIpFormatDateTime()
    {
        date_default_timezone_set('Etc/GMT-0');
        $answer = ipFormatDateTime(1401190316, 'test');
        $this->assertEquals(1, in_array($answer, array('5/27/14 11:31 AM', '5/27/14, 11:31 AM'))); //different PHP versions give different results

    }

    public function testIpFillPlaceholders()
    {
        $content = '<p>Test {websiteTitle}</p>';
        $content = ipFillPlaceholders($content);
        $this->assertEquals('<p>Test TestSite</p>', $content);

        $content = '<p>Test {CUSTOM}</p>';
        $content = ipFillPlaceholders($content, 'Plugin', array('{CUSTOM}' => 'REPLACED'));
        $this->assertEquals('<p>Test REPLACED</p>', $content);


    }

}

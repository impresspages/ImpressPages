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
        date_default_timezone_set('GMT');
        $answer = ipFormatDate(1401190316, 'test');
        $this->assertEquals('5/27/14', $answer);

    }

    public function testIpFormatTime()
    {
        date_default_timezone_set('GMT');
        $answer = ipFormatTime(1401190316, 'test');
        $this->assertEquals('2:31 PM', $answer);

    }

    public function testIpFormatDateTime()
    {
        date_default_timezone_set('GMT');
        $answer = ipFormatDateTime(1401190316, 'test');
        $this->assertEquals('5/27/14 2:31 PM', $answer);

    }


}

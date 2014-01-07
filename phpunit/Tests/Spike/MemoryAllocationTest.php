<?php
/**
 * @package   ImpressPages
 */

namespace Tests;


use PhpUnit\Helper\TestEnvironment;

class MemoryAllocationTest extends \PHPUnit_Framework_TestCase
{
    public function testIniSet()
    {
        $memoryLimit = ini_get('memory_limit');
        $this->assertNotEmpty($memoryLimit);
        $limitNumber = (int)$memoryLimit;
        $this->assertTrue($limitNumber > 10);

        $secondMemoryLimit = ($limitNumber + 10.5) . 'M';
        $result = ini_set('memory_limit', $secondMemoryLimit);

        $this->assertEquals($memoryLimit, $result);

        $thirdMemoryLimit = ($limitNumber * 2) . 'M';
        $result = ini_set('memory_limit', $thirdMemoryLimit);

        $this->assertEquals($secondMemoryLimit, $result);

        $overMemoryLimit = '5000G';
        $result = ini_set('memory_limit', $overMemoryLimit);

        $this->assertEquals($result, $thirdMemoryLimit);
        $this->assertEquals($overMemoryLimit, ini_get('memory_limit'));

        // TODO '-1'
    }
}
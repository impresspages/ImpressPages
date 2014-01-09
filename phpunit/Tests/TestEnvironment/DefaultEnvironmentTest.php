<?php
/**
 * @package   ImpressPages
 */

class DefaultEnvironmentTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultTestEnvironment()
    {
        \PhpUnit\Helper\TestEnvironment::setup();

        $this->assertEquals(realpath(TEST_CODEBASE_DIR), ipConfig()->getRaw('BASE_DIR'));

        $this->assertTrue(file_exists(ipFile('file/repository/')), 'file/repository doesn\'t exist.');

        $this->assertNotEmpty(ipDispatcher(), 'Dispatcher not loaded.');
    }


}
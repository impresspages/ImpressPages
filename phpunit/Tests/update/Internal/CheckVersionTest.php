<?php
/**
 * @package   ImpressPages
 *
 *
 */

class CheckVersionTest extends \PhpUnit\GeneralTestCase
{
    /**
     * @large
     */
    public function testVersionConstant()
    {
        $code = file_get_contents(TEST_CODEBASE_DIR.'Ip/Core/Application.php');

        $position =  strpos($code, 'define(\'IP_VERSION\', \''.RECENT_VERSION.'\');');

        $this->assertNotEquals($position, FALSE);

    }

}

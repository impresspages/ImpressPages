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
        $code = file_get_contents(TEST_CODEBASE_DIR.'install/constants.php');

        $position =  strpos($code, '\''.CURRENT_VERSION.'\'');
        $this->assertNotEquals($position, FALSE, 'Ip/Application.php has no version string.');


    }

}

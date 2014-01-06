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
        $code = file_get_contents(TEST_CODEBASE_DIR.'Ip/Application.php');

        $position =  strpos($code, '\''.CURRENT_VERSION.'\';');
        $this->assertNotEquals($position, FALSE);

        $code = file_get_contents(TEST_CODEBASE_DIR.'Ip/Internal/Install/sql/data.sql');
        $position =  strpos($code, CURRENT_VERSION);
        $this->assertNotEquals($position, FALSE);

        $code = file_get_contents(TEST_CODEBASE_DIR.'update/Library/Model/Update.php');
        $position =  strpos($code, CURRENT_VERSION);
        $this->assertNotEquals($position, FALSE);

    }

}

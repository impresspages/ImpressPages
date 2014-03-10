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
        $this->assertNotEquals($position, FALSE, 'Ip/Application.php has no version string.');

        $code = file_get_contents(TEST_CODEBASE_DIR.'install/Plugin/Install/sql/data.sql');
        $position =  strpos($code, CURRENT_VERSION);
        $this->assertNotEquals($position, FALSE, 'Install/sql/data.sql has no version string.');

//        $code = file_get_contents(TEST_CODEBASE_DIR.'update/Library/Model/Update.php');
//        $position =  strpos($code, CURRENT_VERSION);
//        $this->assertNotEquals($position, FALSE, 'Update code has no version string.');

    }

}

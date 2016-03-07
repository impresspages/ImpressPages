<?php
/**
 * @package   ImpressPages
 *
 *
 */

namespace Tests\Update\Internal;

class CheckVersionTest extends \PhpUnit\GeneralTestCase
{
    /**
     * @large
     */
    public function testVersionConstant()
    {
//        $code = file_get_contents(TEST_CODEBASE_DIR.'install/constants.php');
//
//        $position =  strpos($code, '\''.CURRENT_VERSION.'\'');
//        $this->assertNotEquals($position, FALSE, 'install/constants.php has no version string.');
//
//        $position =  strpos($code, '\''.CURRENT_DBVERSION.'\'');
//        $this->assertNotEquals($position, FALSE, 'install/constants.php has no dbversion string.');

        $code = file_get_contents(TEST_CODEBASE_DIR.'vendor/ImpressPagesFramework/Ip/Application.php');

        $position =  strpos($code, '\''.CURRENT_VERSION.'\'');
        $this->assertNotEquals($position, FALSE, 'Ip/Application.php has no version string.');

        $code = file_get_contents(TEST_CODEBASE_DIR . 'vendor/ImpressPagesCms/Ip/Internal/Update/Model.php');
        $position =  strpos($code, 'return '.CURRENT_DBVERSION.';');
        $this->assertNotEquals($position, FALSE, 'vendor/ImpressPagesCms/Ip/Internal/Update/Model.php has no dbVersion string.');

    }

}

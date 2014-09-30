<?php
/**
 * @package   ImpressPages
 */

namespace Tests\Ip;

use PhpUnit\Helper\TestEnvironment;

class ContentTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        TestEnvironment::setupCode();
    }



    public function testAddDeleteMenu()
    {
        //assert not exist
        $languageCode = ipContent()->getCurrentLanguage()->getCode();
        $menu = ipContent()->getMenu($languageCode, 'create_test');
        $this->assertEquals(false, $menu);

        //create
        ipContent()->addMenu($languageCode, 'create_test', 'CreateTest');
        $menu = ipContent()->getMenu($languageCode, 'create_test');
        $this->assertEquals(true, !empty($menu));

        //delete
        ipContent()->deleteMenu($languageCode, 'create_test');
        $menu = ipContent()->getMenu($languageCode, 'create_test');
        $this->assertEquals(false, $menu);

    }


}

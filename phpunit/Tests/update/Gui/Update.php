<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2012 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */

class UpdateTest extends \PHPUnit_Extensions_Selenium2TestCase
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl('http://localhost/');
    }
 
    public function testTitle()
    {
        $this->url('http://localhost/ip2.x');
        $this->assertEquals('Example WWW Page', $this->title());
    }
    
}
<?php
/**
 * Created by PhpStorm.
 * User: algimantas
 * Date: 11/13/13
 * Time: 11:02 AM
 */

namespace Tests\TestEnvironment;


use PhpUnit\Helper\TestEnvironment;

class TestInstallation extends \PHPUnit_Framework_TestCase
{
    public function testInstall()
    {
        TestEnvironment::setup();

        $installation = new \PhpUnit\Helper\Installation(); //development version
        $installation->install();

        $driver = new \Behat\Mink\Driver\GoutteDriver();
        $session = new \Behat\Mink\Session($driver);
        $session->start();

        $session->visit($installation->getInstallationUrl());

        $this->assertEquals($installation->getInstallationUrl(), $session->getCurrentUrl());

        $page = $session->getPage();
        $this->assertNotEmpty($page);

        $this->assertNotContains('on line', $page->getContent());
        $this->assertFalse($page->has('css', '.error'));

        $headline = $page->find('css', '.homeHeadline');
        $this->assertNotEmpty($headline);
        $this->assertEquals('ImpressPages theme Air', $headline->getText());
    }
}

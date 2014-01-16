<?php

namespace Tests\Functional;

use PhpUnit\Helper\TestEnvironment;

class AddDeleteWidgetTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        TestEnvironment::setup();
    }

    /**
     * @group Sauce
     * @group Selenium
     */
    public function testLogin()
    {
        // install fresh copy of ImpressPages:
        $installation = new \PhpUnit\Helper\Installation(); //development version
        $installation->install();

        $session = \PhpUnit\Helper\Session::factory();

        $installationUrl = $installation->getInstallationUrl();

        $session->visit($installationUrl . 'admin');

        $loginPage = $session->getPage();

        $loginButton = $loginPage->find('css', '.ipsLoginButton');

        $this->assertNotEmpty($loginButton);

        $loginField = $loginPage->find('css', '.form-control[name=login]');
        $loginField->setValue($installation->getAdminLogin());

        $passwordField = $loginPage->find('css', '.form-control[name=password]');
        $passwordField->setValue($installation->getAdminPass());

        $loginButton->click();

        $session->wait(10000,"typeof $ !== 'undefined' && $('.ipActionPublish').size() > 0");
        $page = $session->getPage();
        $this->assertEmpty($page->find('css', '.ipsLoginButton'), 'Could not log in.');
        $this->assertNotEmpty($page->find('css', '.ipActionPublish'));


    }


}

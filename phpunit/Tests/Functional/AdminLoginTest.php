<?php

namespace Tests\Functional;

class AdminLoginTest extends \PhpUnit\Helper\MinkTestCase
{

    /**
     * @group Sauce
     * @group Selenium
     */
    public function testLogin()
    {
        $session = $this->session();

        // install fresh copy of ImpressPages:
        $installation = new \PhpUnit\Helper\Installation(); //development version
        $installation->install();

        $adminHelper = new \PhpUnit\Helper\User\Admin($session, $installation);

        $adminHelper->login();

        $page = $session->getPage();
        $this->assertEmpty($page->find('css', '.ipsLoginButton'), 'Could not log in.');
        $this->assertNotEmpty($page->find('css', '.ipsContentPublish'));
    }

}

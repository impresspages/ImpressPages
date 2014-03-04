<?php

namespace Tests\Functional;

class AdminLoginTest extends \Helper\MinkTestCase
{

    /**
     * @group Sauce
     * @group Selenium
     */
    public function testLogin()
    {
        // install fresh copy of ImpressPages:
        $installation = new \PhpUnit\Helper\Installation(); //development version
        $installation->install();

        $adminHelper = new \PhpUnit\Helper\User\Admin($this->session, $installation);

        $adminHelper->login();

        $page = $this->session->getPage();
        $this->assertEmpty($page->find('css', '.ipsLoginButton'), 'Could not log in.');
        $this->assertNotEmpty($page->find('css', '.ipsContentPublish'));
    }

}

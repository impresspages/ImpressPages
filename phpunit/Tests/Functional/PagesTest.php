<?php

namespace Tests\Functional;

use PhpUnit\Helper\TestEnvironment;

class AdminLoginTest extends \PHPUnit_Framework_TestCase
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

        $adminHelper = new \PhpUnit\Helper\User\Admin($session, $installation);

        $adminHelper->login();

        $page = $session->getPage();

        $session->executeScript("window.location = ip.jQuery('.ipsAdminMenuBlock ul li:nth-child(2) a').attr('href')");

        //wait for page tree to load
        $session->wait(10000, "typeof $ !== 'undefined' && $('.jstree-leaf').length > 0");

        //create new page
        $addPageButton = $page->find('css', '.ipsAddPage');
        $addPageButton->click();

        $pageTitle = 'New page title';
        $session->wait(10000, "typeof $ !== 'undefined' && $('.ipsAddModal').is(':visible')");
        $session->executeScript("ip.jQuery('.ipsAddModal input[name=title]').val('" . $pageTitle . "')");

        $pagesBeforeSubmit = $session->evaluateScript("return ip.jQuery('.ipsTree ul li').length");
        $addPageSubmit = $page->find('css', '.ipsAddModal .ipsAdd');
        $addPageSubmit->click();

        $session->wait(10000, "typeof $ !== 'undefined' && ip.jQuery('.ipsTree ul li').length > " . $pagesBeforeSubmit );
        $lastPageTitle = $session->evaluateScript("return ip.jQuery('.ipsTree ul li:last-child a').text()");
        $this->assertEquals($pageTitle, substr($lastPageTitle, -strlen($pageTitle))); //stripping some ugly whitespaces from the beginning that can't be removed using trim

        $session->executeScript("ip.jQuery('.ipsTree ul li:last-child a').trigger('click')");

        $session->wait(10000, "typeof $ !== 'undefined' && $('.ipsEdit').is(':visible') ");
        $lastPageLink = $page->find('css', ".ipsEdit");
        $lastPageLink->click();

        $session->wait(10000, "typeof $ !== 'undefined' && $('#ipBlock-main').length != 0");

    }


}

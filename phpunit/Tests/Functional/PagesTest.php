<?php

namespace Tests\Functional;

use PhpUnit\Helper\MinkTestCase;

class PagesTest extends MinkTestCase
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

        $session = $this->session();

        $adminHelper = new \PhpUnit\Helper\User\Admin($session, $installation);

        $adminHelper->login();

        $page = $session->getPage();

        $session->executeScript("window.location = jQuery('.ipsAdminMenuBlock ul li:nth-child(2) a').attr('href')");

        //wait for page tree to load
        $session->wait(10000, "typeof $ !== 'undefined' && $('.jstree-leaf').length > 0");

        //create new page
        $addPageButton = $page->find('css', '.ipsAddPage');
        $addPageButton->click();

        $pageTitle = 'New page title';
        $session->wait(10000, "typeof $ !== 'undefined' && $('.ipsAddModal').is(':visible')");
        $page = $session->getPage();
        $page->find('css', '.ipsAddModal input[name=title]')->setValue($pageTitle);

        $pagesBeforeSubmit = $session->evaluateScript("return jQuery('.ipsPages ul li').length");
        $page->find('css', '.ipsAddModal .ipsAdd')->click();

        $session->wait(10000, "typeof $ !== 'undefined' && jQuery('.ipsPages ul li').length > " . $pagesBeforeSubmit);
        $lastPageTitle = $session->evaluateScript("return jQuery('.ipsPages ul li:last-child a').text()");
        $this->assertEquals(
            $pageTitle,
            substr($lastPageTitle, -strlen($pageTitle))
        ); //stripping some ugly whitespaces from the beginning that can't be removed using trim

        $session->executeScript("jQuery('.ipsPages ul li:last-child a').trigger('click')");

        $session->wait(10000, "typeof $ !== 'undefined' && $('.ipsEdit').is(':visible') ");
        $lastPageLink = $page->find('css', ".ipsEdit");
        $lastPageLink->click();

        $session->wait(10000, "typeof $ !== 'undefined' && $('#ipBlock-main').length != 0");
        $page = $session->getPage();
        $title = $page->find('css', 'title');
        $this->assertNotEmpty($title);

        $this->assertEquals($pageTitle, $title->getHtml());

    }


}

<?php

namespace Tests\Functional;

use PhpUnit\Helper\MinkTestCase;
use \PhpUnit\Helper\TestEnvironment;

class InstallTest extends MinkTestCase
{
    /**
     * @group Sauce
     * @group Selenium
     */
    public function testInstallCurrent($customPort = NULL)
    {
        $installation = new \PhpUnit\Helper\Installation(); //development version
        $installation->putInstallationFiles(TEST_TMP_DIR . 'installTest/');

        $session = $this->session();

        $session->visit(TEST_TMP_URL . 'installTest/install/');

        $page = $session->getPage();
        $this->assertNotEmpty($page, 'Page should not be empty');

        $title = $page->find('css', 'title');
        $this->assertNotEmpty($title, 'Title should not be empty');
        $this->assertEquals('ImpressPages installation wizard', $title->getHtml());


        $page->findById('ipsConfigWebsiteName')->setValue('TestSiteName');
        $page->findById('ipsConfigWebsiteEmail')->setValue('test@example.com');
        $page->findById('ipsConfigTimezone')->selectOption('Europe/London');
        $page->find('css', '.btn-primary')->click();

        sleep(1);


        $page->find('css', '.btn-primary')->click();
        $this->assertEquals('Database installation', $page->find('css', 'h1')->getText());
        // There is a hidden error message


        $testDbHelper = new \PhpUnit\Helper\TestDb();

        $dbHost = $testDbHelper->getDbHost();
        if ($customPort) {
            $dbHost .= ':' . $customPort;
        }
        $dbServer = $page->findById('db_server');
        $dbServer->blur();
        $dbServer->setValue($dbHost);

        $page->findById('db_user')->setValue($testDbHelper->getDbUser());
        $page->findById('db_pass')->setValue('wrong');
        $page->findById('db_db')->setValue($testDbHelper->getDbName());
        $page->findById('db_prefix')->setValue('ipt_');
        $page->find('css', '.btn-primary')->click();
        sleep(2);
        $alert = $page->find('css', '.ipsErrorContainer .alert');
        $this->assertNotEmpty($alert);
        $this->assertEquals('Can\'t connect to database.', $alert->getText());



        $page->findById('db_pass')->setValue($testDbHelper->getDbPass());
        $page->find('css', '.btn-primary')->click();

        sleep(3);


        $this->assertNotContains('on line', $page->getContent());
        $this->assertFalse($page->has('css', '.error'));
        $successLine = $page->find('css', '.ipsSuccess');
        $text = $successLine->getText();
        $this->assertEquals('ImpressPages has been successfully installed.', $text);

//        $page->clickLink('Front page');
//
//        sleep(1);
//
//        $this->assertEquals(TEST_TMP_URL . 'installTest/', $session->getCurrentUrl());
//
//        $this->assertNotContains('on line', $page->getContent());
//        $this->assertFalse($page->has('css', '.error'));
//
//        $title = $page->find('css', '.logo a');
//        $this->assertNotEmpty($title);
//        $this->assertEquals('TestSiteName', $title->getText());

        $session->stop();

    }

//    public function testCustomPort()
//    {
//        $this->testInstallCurrent(3306);
//    }

    /**
     * @param \Behat\Mink\Element\DocumentElement $page
     */
    protected function assertNoErrors($page)
    {
        $this->assertNotContains('on line', $page->getContent());
        $this->assertFalse($page->has('css', '.error'));
    }

}

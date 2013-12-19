<?php
/**
 * @package   ImpressPages
 */

namespace Tests\Module\Install\Functional;

use PhpUnit\Helper\TestEnvironment;

class TranslationTest extends \PHPUnit_Framework_TestCase
{

    public function testLayoutEnTranslation()
    {
        TestEnvironment::initCode();

        // Required for test
        $_SESSION = array('step' => 2);

        // TODOX check whether test needs translation init

        $view = \Ip\View::create(ipFile('Ip/Internal/Install/view/layout.php'), array('content' => ''));
        $html = $view->render();

        $page = \PhpUnit\Helper\Mink\Html::getPage($html);

        $title = $page->find('css', 'title');
        $this->assertNotEmpty($title);

        $this->assertEquals('ImpressPages CMS installation wizard', $title->getText());

        $version = $page->find('css', '#installationNotice span');

        $this->assertEquals('Version 4.0', $version->getText());
    }

    public function testLayoutLtTranslation()
    {
        TestEnvironment::initCode();

        // Required for test
        $_SESSION = array('step' => 2);

        // TODOX init lithuanian translations

        $view = \Ip\View::create(ipFile('Plugin/Install/view/layout.php'), array('content' => ''));
        $html = $view->render();

        $page = \PhpUnit\Helper\Mink\Html::getPage($html);

        $title = $page->find('css', 'title');
        $this->assertNotEmpty($title);

        $this->assertEquals('ImpressPages TVS diegimo vedlys', $title->getText());

        $version = $page->find('css', '#installationNotice span');

        $this->assertEquals('Versija 4.0', $version->getText());
    }


}
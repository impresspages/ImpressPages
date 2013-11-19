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

        \Ip\Translator::init('en');
        \Ip\Translator::addTranslationFilePattern('phparray', ipGetConfig()->coreModuleFile('Install/languages'), '%s.php', 'ipInstall');

        $view = \Ip\View::create(ipGetConfig()->coreModuleFile('Install/view/layout.php'), array('content' => ''));
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

        \Ip\Translator::init('lt');
        \Ip\Translator::addTranslationFilePattern('phparray', ipGetConfig()->coreModuleFile('Install/languages'), '%s.php', 'ipInstall');

        $view = \Ip\View::create(ipGetConfig()->coreModuleFile('Install/view/layout.php'), array('content' => ''));
        $html = $view->render();

        $page = \PhpUnit\Helper\Mink\Html::getPage($html);

        $title = $page->find('css', 'title');
        $this->assertNotEmpty($title);

        $this->assertEquals('ImpressPages TVS diegimo vedlys', $title->getText());

        $version = $page->find('css', '#installationNotice span');

        $this->assertEquals('Versija 4.0', $version->getText());
    }


}
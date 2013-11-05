<?php
/**
 * @package   ImpressPages
 */

namespace Tests\Unit;

use \PhpUnit\Helper\TestEnvironment;

class InstallTest extends \PHPUnit_Framework_TestCase
{
    public function testPrepare()
    {

    }

    public function testThis()
    {

        $config = include TEST_CODEBASE_DIR . 'install/ip_config-template.php';
        \Ip\Config::init($config);

        require_once \Ip\Config::getCore('CORE_DIR') . 'Ip/autoloader.php';

        \Ip\Core\Application::init();


        $driver = new \Behat\Mink\Driver\GoutteDriver();
        $session = new \Behat\Mink\Session($driver);
        $session->start();

        $session->visit(\Ip\Config::baseUrl('install/'));

        $page = $session->getPage();

        $this->assertEquals('ImpressPages CMS installation wizard', $page->find('css', 'title')->getText());
    }
}
<?php
/**
 * @package   ImpressPages
 */

namespace Tests\Ip;

class RequestTest extends \PhpUnit\GeneralTestCase
{
    public function setup()
    {
        \PhpUnit\Helper\TestEnvironment::setupCode();
    }

    public function testGetAndPost()
    {
        $request = new \Ip\Request();
        $request->setQuery(array(
            'rise' => 'and shine',
            'look' => 'and smile',
        ));

        \Ip\ServiceLocator::addRequest($request);

        $this->assertEquals('and smile', ipRequest()->getQuery('look'));

        \Ip\ServiceLocator::removeRequest();

        $this->assertNull(ipRequest()->getQuery('look'));
    }

    public function testRelativePath()
    {
        $server = array(
            'HTTP_HOST' => 'local.ip4.x.org',
            'SERVER_NAME' => 'local.ip4.x.org',
            'SERVER_ADDR' => '127.0.0.1',
            'SERVER_PORT' => '80',
            'REMOTE_ADDR' => '127.0.0.1',
            'DOCUMENT_ROOT' => '/var/www/ip4.x',
            'REQUEST_SCHEME' => 'http',
            'CONTEXT_DOCUMENT_ROOT' => '/var/www/ip4.x',
            'SCRIPT_FILENAME' => '/var/www/ip4.x/index.php',
            'REDIRECT_URL' => '/admin/',
            'REQUEST_METHOD' => 'GET',
            'QUERY_STRING' => '',
            'REQUEST_URI' => '/admin/',
            'SCRIPT_NAME' => '/index.php',
            'PHP_SELF' => '/index.php',
        );

        $config = include TEST_FIXTURE_DIR . 'config/default.php';
        $config['baseUrl'] = '';
        $config['baseDir'] = '';
        $ipConfig = new \Ip\Config($config, $server);
        \Ip\ServiceLocator::setConfig($ipConfig);

        $request = new \Ip\Request();
        $request->setServer($server);

        $this->assertEquals('admin/', $request->getRelativePath());

        $server = array(
            'HTTP_HOST' => 'localhost',
            'SERVER_NAME' => 'localhost',
            'SERVER_ADDR' => '127.0.0.1',
            'SERVER_PORT' => '80',
            'REMOTE_ADDR' => '127.0.0.1',
            'DOCUMENT_ROOT' => '/var/www',
            'REQUEST_SCHEME' => 'http',
            'CONTEXT_DOCUMENT_ROOT' => '/var/www',
            'SCRIPT_FILENAME' => '/var/www/index.php',
            'REDIRECT_URL' => '/admin/',
            'REQUEST_METHOD' => 'GET',
            'QUERY_STRING' => NULL,
            'REQUEST_URI' => '/',
            'SCRIPT_NAME' => '/index.php',
            'PHP_SELF' => '/index.php',
        );

        $request = new \Ip\Request();
        $request->setServer($server);

        $this->assertEquals('', $request->getRelativePath());
    }
}

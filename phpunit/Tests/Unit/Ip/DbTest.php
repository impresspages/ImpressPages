<?php
/**
 * @package   ImpressPages
 */

namespace Tests\Unit\Ip;


class Db extends \PHPUnit_Framework_TestCase
{
    public function testConnect()
    {
        $config = include TEST_CODEBASE_DIR . 'install/ip_config-template.php';
        \Ip\Config::init($config);

        require_once \Ip\Config::getCore('CORE_DIR') . 'Ip/autoloader.php';

        $this->assertNotEmpty(\Ip\Config::getRaw('db'));

        \Ip\Db::getConnection();

        $this->assertEmpty(\Ip\Config::getRaw('db'));
    }
} 
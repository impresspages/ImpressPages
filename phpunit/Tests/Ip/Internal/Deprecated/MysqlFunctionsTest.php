<?php
/**
 * @package   ImpressPages
 */

namespace Tests\Ip\Internal\Deprecated;


use PhpUnit\Helper\TestEnvironment;

class MysqlFunctionsTest extends \PHPUnit_Framework_TestCase
{
    public function testThis()
    {
        TestEnvironment::initCode();

        ip_deprecated_mysql_query('DROP TABLE IF EXISTS `test_mysql_deprecated`');

        $sql = "CREATE TABLE `test_mysql_deprecated` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `text` varchar(255) NOT NULL DEFAULT '',
                `code` varchar(255) NOT NULL,
                PRIMARY KEY (`id`)
                ) ENGINE=MyISAM  DEFAULT CHARSET=utf8";

        ip_deprecated_mysql_query($sql);

        $sampleText = ip_deprecated_mysql_real_escape_string('Sample text');

        ip_deprecated_mysql_query("INSERT INTO `test_mysql_deprecated` VALUES (NULL, '$sampleText', 'sampleCode')");
        ip_deprecated_mysql_query("INSERT INTO `test_mysql_deprecated` VALUES (NULL, 'line 1', 'code1')");
        ip_deprecated_mysql_query("INSERT INTO `test_mysql_deprecated` VALUES (NULL, 'line 2', 'code2')");
        ip_deprecated_mysql_query("INSERT INTO `test_mysql_deprecated` VALUES (NULL, 'line 3', 'code3')");

        // TODOX test if works correctly with zero rows
        $rs = ip_deprecated_mysql_query('SELECT * FROM `test_mysql_deprecated`');
        $this->assertNotEmpty($rs);

        $row = ip_deprecated_mysql_fetch_assoc($rs);

        $this->assertNotEmpty($row);
        $this->assertEquals('Sample text', $row['text']);
        $this->assertEquals('sampleCode', $row['code']);

        ip_deprecated_mysql_query('DROP TABLE IF EXISTS `test_mysql_deprecated`');
    }

    public function testError()
    {
        TestEnvironment::initCode();

        ip_deprecated_mysql_query('SELECT * FROM `invalidTableName`');

        $error = ip_deprecated_mysql_error();
        $this->assertNotEmpty($error);

        $this->assertContains('table or view not found', $error);
    }
}
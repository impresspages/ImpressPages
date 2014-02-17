<?php
/**
 * @package   ImpressPages
 *
 *
 */

class ConfigurationParserTest extends \PhpUnit\GeneralTestCase
{
    /**
     * @group ignoreOnTravis
     */
    public function testParse()
    {
        $this->markTestSkipped();

        $configurationParser = new IpUpdate\Library\Model\ConfigurationParser();
        $configuration = $configurationParser->parse(TEST_FIXTURE_DIR.'update/Library/Model/ConfigurationParser/');

        $this->assertEquals($configuration['sessionName'], 'ses328617118');
        $this->assertEquals($configuration['dbPrefix'], 'ip_');
        // END DB

        // GLOBAL
        $this->assertEquals($configuration['baseDir'], '/var/www/test/'); //root DIR with trainling slash at the end. If you have moved your site to another place, change this line to correspond your new domain.
        $this->assertEquals($configuration['baseUrl'], 'http://www.example.com/'); //root url with trainling slash at the end. If you have moved your site to another place, change this line to correspond your new domain.

        $this->assertEquals($configuration['developmentEnvironment'], 1); //displays error and debug information. Change to 0 before deployment to production server
        $this->assertEquals($configuration['errorsShow'], 1);  //0 if you don't wish to display errors on the page
        // END GLOBAL

        // FRONTEND
        $this->assertEquals($configuration['charset'], 'UTF-8'); //system characterset
        $this->assertEquals($configuration['MYSQL_CHARSET'], 'utf8');
        $this->assertEquals($configuration['THEME'], 'Air'); //theme from themes directory
        $this->assertEquals($configuration['DEFAULT_DOCTYPE'], 'DOCTYPE_HTML5'); //look /Ip/View.php for available options.
    }
}

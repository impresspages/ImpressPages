<?php
/**
 * @package   ImpressPages
 *
 *
 */

class ConfigurationParserTest extends \PhpUnit\GeneralTestCase
{
    public function testParse()
    {
        $configurationParser = new IpUpdate\Library\Model\ConfigurationParser();
        $configuration = $configurationParser->parse(TEST_FIXTURE_DIR.'update/Library/Model/ConfigurationParser/');

        $this->assertEquals($configuration['SESSION_NAME'], 'ses328617118');
        $this->assertEquals($configuration['DB_PREF'], 'ip_');
        // END DB

        // GLOBAL
        $this->assertEquals($configuration['BASE_DIR'], '/var/www/test/'); //root DIR with trainling slash at the end. If you have moved your site to another place, change this line to correspond your new domain.
        $this->assertEquals($configuration['BASE_URL'], 'http://www.example.com/'); //root url with trainling slash at the end. If you have moved your site to another place, change this line to correspond your new domain.
        $this->assertEquals($configuration['FILE_DIR'], 'file/'); //uploded files directory
        $this->assertEquals($configuration['TMP_FILE_DIR'], 'file/tmp/'); //temporary files directory
        $this->assertEquals($configuration['FILE_REPOSITORY_DIR'], 'file/repository/'); //files repository. Used for TinyMCE and others where user can browse the files.

        $this->assertEquals($configuration['DEVELOPMENT_ENVIRONMENT'], 1); //displays error and debug information. Change to 0 before deployment to production server
        $this->assertEquals($configuration['ERRORS_SHOW'], 1);  //0 if you don't wish to display errors on the page
        $this->assertEquals($configuration['ERRORS_SEND'], 'mangirdas@impresspages.org'); //insert email address or leave blank. If email is set, you will get an email when an error occurs.
        // END GLOBAL

        // BACKEND
        $this->assertEquals($configuration['INCLUDE_DIR'], 'ip_cms/includes/'); //system directory
        $this->assertEquals($configuration['LIBRARY_DIR'], 'ip_libs/'); //general classes and third party libraries
        $this->assertEquals($configuration['MODULE_DIR'], 'ip_cms/modules/'); //system modules directory
        $this->assertEquals($configuration['PLUGIN_DIR'], 'ip_plugins/'); //plugins directory
        $this->assertEquals($configuration['THEME_DIR'], 'ip_themes/'); //themes directory

        $this->assertEquals($configuration['BACKEND_MAIN_FILE'], 'admin.php'); //backend root file
        $this->assertEquals($configuration['BACKEND_WORKER_FILE'], 'ip_backend_worker.php'); //backend worker root file
        // END BACKEND

        // FRONTEND
        $this->assertEquals($configuration['CHARSET'], 'UTF-8'); //system characterset
        $this->assertEquals($configuration['MYSQL_CHARSET'], 'utf8');
        $this->assertEquals($configuration['THEME'], 'Blank'); //theme from themes directory
        $this->assertEquals($configuration['DEFAULT_DOCTYPE'], 'DOCTYPE_HTML5'); //look ip_cms/includes/Ip/View.php for available options.
    }
}

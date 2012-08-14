<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2012 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */

class InstallTest extends \PhpUnit\SeleniumTestCase
{
    public function testInstallCurrent()
    {
        $this->prepareForInstallation();

        $this->clickAndWait('css=.button_act');
        $this->assertText ('css=h1', 'System check');
        $this->assertNoErrors();

        $this->clickAndWait('css=.button_act');
        $this->assertText('css=h1', 'ImpressPages Legal Notices');
        $this->assertNoErrors();

        $this->clickAndWait('css=.button_act');
        $this->assertText('css=h1', 'Database installation');
        //$this->assertNoErrors(); //there is hidden error message

        $testDbHelper = new \PhpUnit\Helper\TestDb();
        $this->type('css=#db_server', $testDbHelper->getDbHost());
        $this->type('css=#db_user', $testDbHelper->getDbUser());
        $this->type('css=#db_pass', $testDbHelper->getDbPass());
        $this->type('css=#db_db', $testDbHelper->getDbName());
        $this->click('css=.button_act');
        $this->waitForVisible('css=#config_site_name');
        //$this->assertNoErrors();  //there is hidden error message


        $this->type('css=#config_site_name', 'TestSiteName');
        $this->type('css=#config_site_email', 'test@example.com');
        $this->type('css=#config_login', 'admin');
        $this->type('css=#config_pass', 'admin');
        $this->type('css=#config_email', 'test@example.com');
        $this->select('css=#config_timezone', 'value=Europe/London');
        $this->clickAndWait('css=.button_act');
        $this->assertText('css=h1', 'ImpressPages CMS successfully installed.');
        $this->assertNoErrors();


        $this->clickAndWait('css=#content a');
        $this->assertNoErrors();
        $this->assertText('css=.sitename', 'TestSiteName');

    }

    public function testSystemCheck() {
        $this->prepareForInstallation();
        $this->clickAndWait('css=.button_act');

        $this->open(TEST_TMP_URL.'install/?step=1');
        sleep(5);
        $this->assertElementNotPresent('css=span.error');

        unlink(TEST_TMP_DIR.'.htaccess');
        $this->open(TEST_TMP_URL.'install/?step=1');
        sleep(5);
        $this->assertVisible('css=span.error');
        file_put_contents(TEST_TMP_DIR.'.htaccess', '');
        $this->open(TEST_TMP_URL.'install/?step=1');
        sleep(5);
        $this->assertElementNotPresent('css=span.error');


        file_put_contents(TEST_TMP_DIR.'index.html', '');
        $this->open(TEST_TMP_URL.'install/?step=1');
        sleep(5);
        $this->assertVisible('css=span.error');
        unlink(TEST_TMP_DIR.'index.html');
        $this->open(TEST_TMP_URL.'install/?step=1');
        sleep(5);
        $this->assertElementNotPresent('css=span.error');



    }



    private function prepareForInstallation()
    {
        $tmpInstallDir = TEST_TMP_DIR;
        $testUrl = TEST_TMP_URL;

        $folders = array(
            'audio',
            'file',
            'image',
            'install',
            'ip_cms',
            'ip_configs',
            'ip_libs',
            'ip_plugins',
            'ip_themes',
            'update',
            'video'
        );

        $files = array(
            'admin.php',
            'favicon.ico',
            'index.php',
            'ip_backend_frames.php',
            'ip_backend_worker.php',
            'ip_config.php',
            'ip_cron.php',
            'ip_license.html',
            'readme.txt',
            'robots.txt',
            'sitemap.php',
            '.htaccess'
        );


        $fs = new \PhpUnit\Helper\FileSystem();
        foreach($folders as $folder) {
            $fs->cpDir(CODEBASE_DIR.$folder, $tmpInstallDir.$folder);
        }
        foreach($files as $file) {
            copy(CODEBASE_DIR.$file, $tmpInstallDir.$file);
        }

        file_put_contents($tmpInstallDir.'robots.txt', '');
        file_put_contents($tmpInstallDir.'ip_config.php',
            '<?php

 if(!isset($_GET[\'install\']))
    header("location: install/?install=1");
        ');

        $this->open($testUrl);
        $this->assertNoErrors();
    }

}

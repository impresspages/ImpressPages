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

        $this->clickAndWait('css=.button_act');
        $this->assertText ('css=h1', 'System check');
        $this->assertNoErrors();

        $this->clickAndWait('css=.button_act');
        $this->assertText('css=h1', 'ImpressPages Legal Notices');
        $this->assertNoErrors();

        $this->clickAndWait('css=.button_act');


        sleep(100000);


    }
}

<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2012 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */

class RemoveDeletedDirTest extends \PhpUnit\MigrationTestCase
{

    public function testMigrationDoesntFailIfDirMissing()
    {

        $config = $this->getInstallationConfig();
        $migrationScript = new \IpUpdate\Library\Migration\To2_7\Script();
        $migrationScript->removeDeletedDir($config);


        $this->assertEquals(false, file_exists($config['BASE_DIR'].$config['FILE_DIR'].'deleted'));
    }


    public function testMigrationRemovedDeletedDir()
    {
        $config = $this->getInstallationConfig();
        mkdir($config['BASE_DIR'].$config['FILE_DIR'].'deleted/');
        $myFile = $config['BASE_DIR'].$config['FILE_DIR'].'deleted/anyfile.txt';
        $fh = fopen($myFile, 'w');
        fclose($fh);

        $migrationScript = new \IpUpdate\Library\Migration\To2_7\Script();
        $migrationScript->removeDeletedDir($config);


        $this->assertEquals(false, file_exists($config['BASE_DIR'].$config['FILE_DIR'].'deleted'));
    }




}
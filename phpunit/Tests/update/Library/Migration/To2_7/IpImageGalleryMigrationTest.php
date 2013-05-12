<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2012 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */

class IpImageGalleryMigrationTest extends \PhpUnit\MigrationTestCase
{


    public function getDataSet()
    {
        return $this->createXMLDataSet(TEST_FIXTURE_DIR.'update/Library/Migration/To3_0/ipImageGallery.xml');
    }


    public function testIpImageMigration()
    {
        $config = $this->getInstallationConfig();

        $myFile = $config['BASE_DIR'].$config['FILE_DIR']."square_logo.jpg";
        $fh = fopen($myFile, 'w');
        $myFile = $config['BASE_DIR'].$config['FILE_DIR']."square_logo_1.jpg";
        $fh = fopen($myFile, 'w');
        $myFile = $config['BASE_DIR'].$config['FILE_DIR']."square_logo_2.jpg";
        $fh = fopen($myFile, 'w');

        $this->assertEquals(1, $this->getConnection()->getRowCount('ip_m_content_management_widget'), "Pre-Condition");
        $migrationScript = new \IpUpdate\Library\Migration\To3_0\Script();
        $migrationScript->migrateWidgets($config);


        /**
         * @var $sourceTable \PHPUnit_Extensions_Database_DataSet_QueryTable
         */
        $sourceTable = $this->getConnection()->createQueryTable('ip_m_content_management_widget', 'SELECT * FROM ip_m_content_management_widget');
        $expectedTable = $this->createXMLDataSet(TEST_FIXTURE_DIR.'update/Library/Migration/To3_0/ipImageGalleryResult.xml')->getTable("ip_m_content_management_widget");

        $this->assertTablesEqual($expectedTable, $sourceTable);


        $this->assertEquals(true, file_exists($config['BASE_DIR'].$config['FILE_DIR']."square_logo.jpg"));
        $this->assertEquals(false, file_exists($config['BASE_DIR'].$config['FILE_DIR']."square_logo_1.jpg"));
        $this->assertEquals(false, file_exists($config['BASE_DIR'].$config['FILE_DIR']."square_logo_2.jpg"));
    }



}
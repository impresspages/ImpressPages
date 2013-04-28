<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2012 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */

class IpImageMigrationTest extends \PhpUnit\MigrationTestCase
{


    public function getDataSet()
    {
        return $this->createXMLDataSet(TEST_FIXTURE_DIR.'update/Library/Migration/To2_7/ipImage.xml');
    }


    public function testIpImageMigration()
    {
        $dataSet = $this->getConnection()->createDataSet(array('ip_m_content_management_widget'));


        $this->assertEquals(1, $this->getConnection()->getRowCount('ip_m_content_management_widget'), "Pre-Condition");
        $migrationScript = new \IpUpdate\Library\Migration\To2_7\Script();
        $migrationScript->migrateWidgets($this->getInstallationConfig());


        /**
         * @var $sourceTable \PHPUnit_Extensions_Database_DataSet_QueryTable
         */
        $sourceTable = $this->getConnection()->createQueryTable('ip_m_content_management_widget', 'SELECT * FROM ip_m_content_management_widget');
        $expectedTable = $this->createXMLDataSet(TEST_FIXTURE_DIR.'update/Library/Migration/To2_7/ipImageResult.xml')->getTable("ip_m_content_management_widget");

//        echo  $sourceTable->getValue(0, 'data');echo "\n";
//        echo  $expectedTable->getValue(0, 'data');exit;
        $this->assertTablesEqual($expectedTable, $sourceTable);


//        $this->assertEquals(1, $this->getConnection()->getRowCount('ip_m_content_management_widget'), "Pre-Condition");


//        $guestbook = new Guestbook();
//        $guestbook->addEntry("suzy", "Hello world!");
//
//        $this->assertEquals(3, $this->getConnection()->getRowCount('guestbook'), "Inserting failed");
//
//
//        $test->test();



//        $newData = $migrationScript->migrateIpImage(1, array(
//            'imageOrignal' => 'original.jpg',
//            'imageBig' => 'big.jpg',
//            'imageSmall' => 'small.jpg'
//        ));
//
//        $this->assertEqual('original.jpg', $newData['imageOriginal']);
//        $this->assertEqual(false, isset($newData['imageBig']));
//        $this->assertEqual(false, isset($newData['imageSmall']));


    }



}
<?php
/**
 * @package   ImpressPages
 */

namespace Tests\Ip;

use PhpUnit\Helper\TestEnvironment;

class DbGeneralTest extends \PhpUnit\GeneralTestCase
{


    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        $dataSet =  $this->createXMLDataSet(TEST_FIXTURE_DIR.'Ip/db.xml');
        return $dataSet;
    }



    public function testMaxAge()
    {
        $minute = 60;
        ipDb()->insert('email_queue', array('lockedAt' => date("Y-m-d H:i:s", time() + $minute * 100)));
        ipDb()->insert('email_queue', array('lockedAt' => date("Y-m-d H:i:s", time() - $minute * 11)));
        ipDb()->insert('email_queue', array('lockedAt' => date("Y-m-d H:i:s", time() - $minute * 12)));
        ipDb()->insert('email_queue', array('lockedAt' => date("Y-m-d H:i:s", time() - $minute * 30)));
        ipDb()->insert('email_queue', array('lockedAt' => date("Y-m-d H:i:s", time() - $minute * 100)));

        $sql = 'SELECT count(1) FROM ' . ipTable('email_queue') . ' WHERE ' . ipDb()->sqlMaxAge('lockedAt', 20, 'MINUTE');
        $count = ipDb()->fetchValue($sql, array());
        $this->assertEquals(3, $count);

        $sql = 'SELECT count(1) FROM ' . ipTable('email_queue') . ' WHERE ' . ipDb()->sqlMinAge('lockedAt', 20, 'MINUTE');
        $count = ipDb()->fetchValue($sql, array());
        $this->assertEquals(2, $count);

        ipDb()->delete('email_queue', array());

        $hour = 60 * 60;
        ipDb()->insert('email_queue', array('lockedAt' => date("Y-m-d H:i:s", time() + $hour * 100)));
        ipDb()->insert('email_queue', array('lockedAt' => date("Y-m-d H:i:s", time() - $hour * 11)));
        ipDb()->insert('email_queue', array('lockedAt' => date("Y-m-d H:i:s", time() - $hour * 12)));
        ipDb()->insert('email_queue', array('lockedAt' => date("Y-m-d H:i:s", time() - $hour * 30)));
        ipDb()->insert('email_queue', array('lockedAt' => date("Y-m-d H:i:s", time() - $hour * 100)));

        $sql = 'SELECT count(1) FROM ' . ipTable('email_queue') . ' WHERE ' . ipDb()->sqlMaxAge('lockedAt', 20, 'HOUR');
        $count = ipDb()->fetchValue($sql, array());
        $this->assertEquals(3, $count);

        $sql = 'SELECT count(1) FROM ' . ipTable('email_queue') . ' WHERE ' . ipDb()->sqlMinAge('lockedAt', 20, 'HOUR');
        $count = ipDb()->fetchValue($sql, array());
        $this->assertEquals(2, $count);



    }



}

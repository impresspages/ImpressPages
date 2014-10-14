<?php
/**
 * @package   ImpressPages
 */

namespace Tests\Ip\Internal\Email;

/**
 * @group ignoreOnTravis
 * @group Selenium
 */
class DbTest extends \PhpUnit\GeneralTestCase
{


    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        $dataSet =  $this->createXMLDataSet(TEST_FIXTURE_DIR.'Ip/Internal/Email/db.xml');
        return $dataSet;
    }



    public function testSentOrLockedCount()
    {
        $minute = 60;
        ipDb()->insert('email_queue', array('lockedAt' => date("Y-m-d H:i:s", time() + $minute * 100)));
        ipDb()->insert('email_queue', array('lockedAt' => date("Y-m-d H:i:s", time() - $minute * 11)));
        ipDb()->insert('email_queue', array('lockedAt' => date("Y-m-d H:i:s", time() - $minute * 12), 'send' => date("Y-m-d H:i:s", time() - $minute * 12)));
        ipDb()->insert('email_queue', array('lockedAt' => date("Y-m-d H:i:s", time() - $minute * 30), 'send' => date("Y-m-d H:i:s", time() - $minute * 12)));
        ipDb()->insert('email_queue', array('lockedAt' => date("Y-m-d H:i:s", time() - $minute * 100), 'send' => date("Y-m-d H:i:s", time() - $minute * 12)));
        ipDb()->insert('email_queue', array('send' => date("Y-m-d H:i:s", time() - $minute * 21)));

        $locked = \Ip\Internal\Email\Db::sentOrLockedCount(20);
        $this->assertEquals(3, $locked);

    }



}

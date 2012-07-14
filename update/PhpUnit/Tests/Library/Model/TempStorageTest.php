<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2012 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */

class TempStorageTest extends \IpUpdate\PhpUnit\UpdateTestCase
{


    public function testStorage()
    {
        $tmpStorage = new IpUpdate\Library\Model\TempStorage(TMP_DIR);
        $key1 = 'key1';
        $val1 = 'val1';

        $this->assertEquals($tmpStorage->exist($key1), false);

        $tmpStorage->setValue($key1, 'val1');

        $this->assertEquals($tmpStorage->exist($key1), true);
        $this->assertEquals($tmpStorage->getValue($key1), $val1);

        $tmpStorage->remove($key1);
        $this->assertEquals($tmpStorage->exist($key1), false);
    }



}

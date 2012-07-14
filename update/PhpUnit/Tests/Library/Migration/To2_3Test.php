<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2012 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */

class To2_3Test extends \IpUpdate\PhpUnit\UpdateTestCase
{

    public function testGetDownloadUrl()
    {
        $script = new \IpUpdate\Library\Migration\To2_3\Script();
        $downloadUrl = $script->getDownloadUrl();
        $this->assertEquals('http://sourceforge.net/projects/impresspages/files/ImpressPages_2_3.zip/download', $downloadUrl);
    }



}
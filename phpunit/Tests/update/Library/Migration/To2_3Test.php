<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2012 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */

class To2_3Test extends \PhpUnit\GeneralTestCase
{

    /**
     * @large
     */
    public function testGetDownloadUrl()
    {
        $script = new \IpUpdate\Library\Migration\To2_3\Script();
        $downloadUrl = $script->getDownloadUrl();
        $this->assertEquals(true, $downloadUrl == 'http://download.impresspages.org/ImpressPages_2_3.zip' || $downloadUrl == 'http://localhost/sourceforge.net/ImpressPages_2_3.zip');
    }



}
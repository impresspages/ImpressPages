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
        $fs = new \PhpUnit\Helper\FileSystem();
        $fs->cpDir('');
    }
}

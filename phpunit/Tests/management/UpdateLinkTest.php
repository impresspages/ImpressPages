<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2012 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */

class UpdateLinkTest extends \PhpUnit\SeleniumTestCase
{
    public function testLinkUpdate()
    {
        $installation = $this->getInstallation();

        $ipActions = new \PhpUnit\Helper\IpActions($this);
        $ipActions->login($installation);

        $ipActions->addWidget('IpHtml');
        $this->type('css=.ipAdminWidget-IpHtml textarea', '<a class="seleniumUpdateLinkTest" href="'.$installation->getInstallationUrl().'">TEST</a>');

        $ipActions->confirmWidget();
        $ipActions->publish();

        $this->storeAttribute('css=.ipWidget-IpHtml a.seleniumUpdateLinkTest@href', 'linkValue');
        $linkValue = $this->getExpression('${linkValue}');
        $this->assertEquals($installation->getInstallationUrl().'?cms_action=manage', $linkValue);

        $installation->getInstallationDir();

        $baseName = $this->removeTrailingSlash(basename($installation->getInstallationUrl()));
        $newSubdir = 'moved';
        $fs = new \PhpUnit\Helper\FileSystem();
        $fs->cpDir($installation->getInstallationDir(), $installation->getInstallationDir().'../'.$newSubdir.'/');
        $fs->cleanDir($installation->getInstallationDir());

        $newInstallation = clone $installation;
        $newInstallation->setInstallationDir(str_replace($baseName, $newSubdir, $installation->getInstallationDir()));
        $newInstallation->setInstallationUrl(str_replace($baseName, $newSubdir, $installation->getInstallationUrl()));
        $configurationHelper = new \PhpUnit\Helper\Configuration();

        $configurationHelper->changeConfigurationConstantValue($newInstallation, 'BASE_URL', $installation->getInstallationUrl(), $newInstallation->getInstallationUrl());
        $configurationHelper->changeConfigurationConstantValue($newInstallation, 'BASE_DIR', $installation->getInstallationDir(), $newInstallation->getInstallationDir());

        $ipActions->login($newInstallation);
        $this->assertNoErrors();

        $this->storeAttribute('css=.ipAdminNavLinks ul > li:eq(2) > ul > li:eq(3) > a@href', 'systemModuleLink');
        $systemModuleLink = $this->getExpression('${systemModuleLink}');
        $this->open($systemModuleLink);
        $this->click('css=.content a.button');
        $this->waitForText('css=.note p', 'exact:Cache was cleared.');
        $this->assertNoErrors();

        $this->open($newInstallation->getInstallationUrl());

        $this->storeAttribute('css=.ipWidget-IpHtml a.seleniumUpdateLinkTest@href', 'linkValue');
        $linkValue = $this->getExpression('${linkValue}');
        $this->assertEquals($newInstallation->getInstallationUrl(), $linkValue);

    }

    private function removeTrailingSlash($path)
    {
        return preg_replace('{/$}', '', $path);
    }

}

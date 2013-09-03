<?php
/**
 * @package   ImpressPages
 *
 *
 */

class UpdateLinkTest extends \PhpUnit\SeleniumTestCase
{


    public function testPageUrlChanged()
    {
        $installation = $this->getInstallation();

        //create internal link on main page to second link on left menu
        $ipActions = new \PhpUnit\Helper\IpActions($this, $installation);
        $ipActions->login();

        $this->windowMaximize();

        $ipActions->addWidget('IpHtml');
        $this->storeAttribute('css=.side nav ul li:eq(1) a@href', 'linkValue');
        $linkValue = $this->getExpression('${linkValue}');
        $this->type('css=.ipAdminWidget-IpHtml textarea', '<a class="seleniumUpdateLinkTest" href="'.$linkValue.'">TEST</a>');
        $ipActions->confirmWidget();
        $ipActions->publish();

        //change url left menu second link
        $this->open($linkValue);
        $this->waitForElementPresent('css=.ipAdminPanel .ipaOptions');
        $this->click('css=.ipAdminPanel .ipaOptions');
        $this->waitForElementPresent('css=.ipaOptionsDialog .ipaOptionsConfirm');

        $this->click('css=.ipaOptionsDialog .tabs li:eq(1) a');
        $this->type('css=.ipaOptionsDialog #seoUrl', 'new_url');
        $this->click('css=.ipaOptionsDialog .ipaOptionsConfirm');
        $this->waitForElementNotPresent('css=.ipaOptionsDialog .ipaOptionsConfirm');
        $ipActions->publish();

        //add html widget to check its presents later
        $ipActions->addWidget('IpHtml');
        $this->storeAttribute('css=.side nav ul li:eq(1) a@href', 'linkValue');
        $linkValue = $this->getExpression('${linkValue}');
        $this->type('css=.ipAdminWidget-IpHtml textarea', '<span class="seleniumPageUrlChangedTest" href="'.$linkValue.'">TEST</span>');
        $ipActions->confirmWidget();
        $ipActions->publish();


        //assert our url still works
        $this->open($installation->getInstallationUrl());
        $this->storeAttribute('css=.ipWidget-IpHtml a.seleniumUpdateLinkTest@href', 'linkValue');
        $linkValue = $this->getExpression('${linkValue}');
        $this->open($linkValue);
        $this->assertElementPresent('css=span.seleniumPageUrlChangedTest');
    }


    public function testLinkWebsiteMove()
    {
        $installation = $this->getInstallation();

        $ipActions = new \PhpUnit\Helper\IpActions($this, $installation);
        $ipActions->login();

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
        $configurationHelper->changeConfigurationConstantValue($newInstallation, 'BASE_DIR', TEST_BASE_DIR.$installation->getInstallationDir(), TEST_BASE_DIR.$newInstallation->getInstallationDir());

        $ipActions = new \PhpUnit\Helper\IpActions($this, $newInstallation);
        $ipActions->login();
        $this->assertNoErrors();

        $ipActions->openModule('system');
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

<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2012 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */

class AddDeleteWidgetTest extends \PhpUnit\SeleniumTestCase
{
    public function testInstallCurrent()
    {
        $installation = new \PhpUnit\Helper\Installation(); //development version
        $installation->install();
        $this->open($installation->getInstallationUrl().'admin.php');


        $this->type('css=.loginInput:eq(0)', $installation->getAdminLogin());
        $this->type('css=.loginInput:eq(1)', $installation->getAdminLogin());

        $this->clickAndWait('css=.loginSubmit');

        $ipActions = new \PhpUnit\Helper\IpActions($this);

        $ipActions->addWidget('IpText');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('IpTitle');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('IpSeparator');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('IpTextImage');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('IpImage');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('IpImageGallery');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('IpLogoGallery');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('IpFile');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('IpTable');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('IpNewsletter');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('IpHtml');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('IpRichText');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('IpFaq');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('IpForm');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();

    }


}

<?php
/**
 * @package   ImpressPages
 *
 *
 */

/**
 * @group ignoreOnTravis
 */
class AddDeleteWidgetTest extends \PhpUnit\SeleniumTestCase
{
    public function testAddRemoveWidgets()
    {
        $installation = $this->getInstallation();

        $ipActions = new \PhpUnit\Helper\IpActions($this, $installation);
        $ipActions->login();

        $this->windowMaximize();

        $ipActions->addWidget('IpTitle');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('IpText');
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
        $ipActions->selectFirstFileInRepository();
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('IpFile');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('IpHtml');
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
        $ipActions->addWidget('IpColumns');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();

    }


}

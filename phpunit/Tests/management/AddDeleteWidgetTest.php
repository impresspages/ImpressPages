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

        $ipActions->addWidget('Title');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('Text');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('Separator');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('TextImage');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('Image');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('Gallery');
        $ipActions->selectFirstFileInRepository();
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('File');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('Html');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('Faq');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('Form');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $ipActions->addWidget('Columns');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();

    }


}

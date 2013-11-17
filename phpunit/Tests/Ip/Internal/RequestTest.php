<?php
/**
 * @package   ImpressPages
 */

namespace Tests\Ip\Internal;

class RequestTest extends \PhpUnit\GeneralTestCase
{
    public function testGetAndPost()
    {
        \PhpUnit\Helper\TestEnvironment::initCode();

        \Ip\ServiceLocator::addRequest(new \Ip\Request());

        $request = new \Ip\Request();
        $request->setGet(array(
            'rise' => 'and shine',
            'look' => 'and smile',
        ));

        \Ip\ServiceLocator::addRequest($request);

        $this->assertEquals('and smile', ipGetRequest()->getQuery('look'));

        \Ip\ServiceLocator::removeRequest();

        $this->assertNull(ipGetRequest()->getQuery('look'));
    }
}
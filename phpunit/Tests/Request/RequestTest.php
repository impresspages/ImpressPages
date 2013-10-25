<?php
/**
 * @package   ImpressPages
 */

class RequestTest extends \PhpUnit\GeneralTestCase
{
    public function testGetAndPost()
    {
        \PhpUnit\Helper\TestEnvironment::initCode();

        $_GET = array(
            'rise' => 'and shine',
            'look' => 'and smile',
        );

        $_POST = array(
            'dark' => 'bear'
        );

        $request = new \Ip\Internal\Request();
        \Ip\ServiceLocator::replaceRequestService($request);

        $this->assertEquals('and smile', \Ip\Request::getQuery('look'));
    }
}
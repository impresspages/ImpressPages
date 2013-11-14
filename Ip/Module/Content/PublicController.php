<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Module\Content;


class PublicController extends \Ip\Controller
{
    public function index()
    {
        $response = new \Ip\Response();
        $response->setContent('test');

        return $response;
    }
}
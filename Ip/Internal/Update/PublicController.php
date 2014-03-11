<?php
/**
 * @package   ImpressPages
 */


namespace Ip\Internal\Update;


class PublicController extends \Ip\Controller
{
    public function index()
    {
        return 'test';
        return new \Ip\Response\PageNotFound();
    }
}

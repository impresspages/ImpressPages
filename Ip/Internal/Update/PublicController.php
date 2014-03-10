<?php
/**
 * @package   ImpressPages
 */


namespace Ip\Internal\Update;


class PublicController extends \Ip\Controller
{
    public function index()
    {
        return new \Ip\Response\PageNotFound();
    }
}

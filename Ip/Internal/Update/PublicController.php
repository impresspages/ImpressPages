<?php
/**
 * @package   ImpressPages
 */


namespace Ip\Internal\Update;


class PublicController extends \Ip\Controller
{
    public function index()
    {
        //UpdateModel::runMigrations();
        return new \Ip\Response\PageNotFound();
    }
}

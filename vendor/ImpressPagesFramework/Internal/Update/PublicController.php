<?php
/**
 * @package   ImpressPages
 */


namespace Ip\Internal\Update;


class PublicController extends \Ip\Controller
{
    public function index()
    {
        Model::runMigrations();
        return new \Ip\Response\Json(array('success' => 1));
    }
}

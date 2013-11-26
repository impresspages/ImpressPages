<?php
namespace Plugin\Setup;

class PublicController extends \Ip\Controller {
    public function index()
    {
        return new \Ip\Response\Json(array(1));
    }
}
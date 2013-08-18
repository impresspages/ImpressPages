<?php
namespace Ip\Module\Plugins;


class Backend extends \Ip\Controller{

    public function index()
    {
        $site = \Ip\ServiceLocator::getSite();
        $site->setOutput('test');
    }
}


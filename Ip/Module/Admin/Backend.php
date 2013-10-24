<?php
namespace Ip\Module\Admin;


class Backend{


    public function deprecatedBootstrap()
    {

        $site = \Ip\ServiceLocator::getSite();
        if (!defined('BACKEND')) {
            define('BACKEND', true);
        }

        global $cms;
        $cms = new \Ip\Backend\Cms();

        $cms->makeActions();

        $cms->manage();
        $site->setOutput('');
    }
}
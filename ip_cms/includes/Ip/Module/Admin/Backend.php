<?php
namespace Ip\Module\Admin;


class Backend{


    public function deprecatedBootstrap()
    {

        $site = \Ip\ServiceLocator::getSite();
        if (!defined('BACKEND')) {
            define('BACKEND', true);
        }

        require_once (BASE_DIR.BACKEND_DIR.'cms.php');
        require_once (BASE_DIR.BACKEND_DIR.'db.php');

        global $cms;
        $cms = new \Backend\Cms();

        $cms->makeActions();

        return $cms->manage();
    }
}
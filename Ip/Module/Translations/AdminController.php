<?php
namespace Ip\Module\Translations;


class AdminController extends \Ip\Grid1\Controller{

    protected function config()
    {
        return array (
            'type' => 'table',
            'table' => DB_PREF . 'translation'
        );
    }

}


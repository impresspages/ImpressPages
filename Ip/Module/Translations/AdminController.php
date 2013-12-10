<?php
namespace Ip\Module\Translations;


class AdminController extends \Ip\Crud1\Controller{

    public function crudConfig()
    {
        return array (
            'table' => DB_PREF . 'translations'
        );
    }

}


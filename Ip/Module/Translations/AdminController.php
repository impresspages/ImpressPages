<?php
namespace Ip\Module\Translations;


class AdminController extends \Ip\Crud1\Controller{

    public function crudConfigAction()
    {
        return array (
            'table' => DB_PREF . 'translations'
        );
    }

}


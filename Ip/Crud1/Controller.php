<?php
/**
 * @package		ImpressPages
 */

namespace Ip\Crud1;


/**
 * Some function to speed up ecommerce products development
 * @package Library\Php\Ecommerce
 */
abstract class Controller extends \Ip\Controller{
    public function index()
    {

    }

    public function crud()
    {
        $worker = new Worker($this->crudConfig());
        return $worker->handleAction();
    }

    /**
     * @return array
     */
    abstract protected function crudConfig();

//    /**
//     * @return \Ip\Crud1\Controller
//     */
//    protected function getCrudObject()
//    {
//        return new
//        $config = array (
//            'table' => DB_PREF . 'translations'
//        );
//        $crud = new \Ip\Crud1\Controller($config);
//        return $crud;
//    }
}
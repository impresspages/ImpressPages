<?php
/**
 * @package		ImpressPages
 */

namespace Ip\Grid1;


/**
 * Some function to speed up ecommerce products development
 * @package Library\Php\Ecommerce
 */
abstract class Controller extends \Ip\Controller{
    public function index()
    {
        ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/grid1/src/grid1.js'));
        ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/grid1/src/grid1Init.js'));

        $controllerClass = get_class($this);
        $controllerClassParts = explode('\\', $controllerClass);
        $aa = $controllerClassParts[2] . '.grid';
        $gateway = array('aa' => $aa);

        $variables = array(
            'gateway' => $gateway
        );
        $content = \Ip\View::create('view/placeholder.php', $variables)->render();
        return $content;
    }

    public function grid()
    {
        $worker = new Worker($this->config());
        $result =  $worker->handleMethod(ipRequest());
        return new \Ip\Response\JsonRpc($result);
    }

    /**
     * @return array
     */
    abstract protected function config();

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
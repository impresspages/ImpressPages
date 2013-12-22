<?php
/**
 * @package        ImpressPages
 */

namespace Ip\Grid;

abstract class Controller extends \Ip\Controller
{
    public function index()
    {
        ipAddJs(ipFileUrl('Ip/Internal/Ip/assets/grid/src/grid.js'));
        ipAddJs(ipFileUrl('Ip/Internal/Ip/assets/grid/src/gridInit.js'));

        $controllerClass = get_class($this);
        $controllerClassParts = explode('\\', $controllerClass);

        $aa = $controllerClassParts[count($controllerClassParts) - 2] . '.grid';

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
        $result = $worker->handleMethod(ipRequest());
        return new \Ip\Response\JsonRpc($result);
    }

    /**
     * @return array
     */
    abstract protected function config();


}
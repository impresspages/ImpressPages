<?php
/**
 * @package        ImpressPages
 */

namespace Ip;

abstract class GridController extends \Ip\Controller
{
    public function index()
    {
        ipAddJs(ipFileUrl('Ip/Internal/Grid/assets/grid.js'));
        ipAddJs(ipFileUrl('Ip/Internal/Grid/assets/gridInit.js'));

        $controllerClass = get_class($this);
        $controllerClassParts = explode('\\', $controllerClass);

        $aa = $controllerClassParts[count($controllerClassParts) - 2] . '.grid';

        $gateway = array('aa' => $aa);

        $variables = array(
            'gateway' => $gateway
        );
        $content = \Ip\View::create('Internal/Grid/view/placeholder.php', $variables)->render();
        return $content;
    }

    public function grid()
    {
        $worker = new \Ip\Internal\Grid\Worker($this->config());
        $result = $worker->handleMethod(ipRequest());
        return new \Ip\Response\JsonRpc($result);
    }

    /**
     * @return array
     */
    abstract protected function config();


}
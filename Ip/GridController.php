<?php
/**
 * @package        ImpressPages
 */

namespace Ip;

abstract class GridController extends \Ip\Controller
{
    public function index()
    {
        ipAddJs('Ip/Internal/Grid/assets/grid.js');
        ipAddJs('Ip/Internal/Grid/assets/gridInit.js');
        ipAddJs('Ip/Internal/Grid/assets/subgridField.js');


        $controllerClass = get_class($this);
        $controllerClassParts = explode('\\', $controllerClass);

        $aa = $controllerClassParts[count($controllerClassParts) - 2] . '.grid';

        $gateway = array('aa' => $aa);

        $variables = array(
            'gateway' => ipActionurl($gateway)
        );
        $content = ipView('Internal/Grid/view/placeholder.php', $variables)->render();
        return $content;
    }

    public function grid()
    {
        $worker = new \Ip\Internal\Grid\Worker($this->config());
        $result = $worker->handleMethod(ipRequest());

        if (is_array($result) && !empty($result['error']) && !empty($result['errors'])) {
            return new \Ip\Response\Json($result);
        }

        return new \Ip\Response\JsonRpc($result);
    }

    /**
     * @return array
     */
    abstract protected function config();


}

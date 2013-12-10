<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Grid1\Model;


class Table extends \Ip\Grid1\Model{

    protected $config = null;
    public function __construct($config)
    {
        $this->config = $config;
    }


    public function handleMethod(\Ip\Request $request)
    {
        $data = $request->getRequest();
        if (empty($data['method'])) {
            throw new \Ip\CoreException('Missing request data');
        }
        $method = $data['method'];
        if (!isset($data['params'])) {
            $data['params'] = array();
        }

        switch($method) {
            case 'init':
                return $this->refresh();
                break;
        }

    }



    protected function refresh()
    {
        $html = \Ip\View::create('../view/table.php')->render();
        $commands = array(
            $this->commandSetHtml($html)
        );
        return $commands;
    }

}
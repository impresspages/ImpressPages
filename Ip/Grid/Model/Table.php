<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Grid\Model;


class Table extends \Ip\Grid\Model
{

    /**
     * @var Config
     */
    protected $config = null;

    public function __construct($config)
    {
        $this->config = new Config($config);

        $this->actions = new Actions($this->config);
    }

    public function handleMethod(\Ip\Request $request)
    {
        $data = $request->getRequest();
        if (empty($data['method'])) {
            throw new \Ip\CoreException('Missing request data');
        }
        $method = $data['method'];

        if (empty($data['hash'])) {
            $data['hash'] = '';
        }
        $hash = $data['hash'];

        if (isset($data['params'])) {
            $params = $data['params'];
        } else {
            $params = array();
        }
        $statusVariables = Status::parse($hash);
        switch ($method) {
            case 'init':
                return $this->init($statusVariables);
                break;
            case 'page':
                return $this->page($params, $statusVariables);
                break;
            case 'delete':
                return $this->delete($params, $statusVariables);
                break;
        }
    }

    protected function init($statusVariables)
    {
        $display = new Display($this->config);
        $commands = array();
        $html = $display->fullHtml($statusVariables);
        $commands[] = Commands::setHtml($html);
        return $commands;
    }

    protected function page($params, $statusVariables)
    {
        $statusVariables['page'] = $params['page'];
        $commands = array();
        $commands[] = Commands::setHash(Status::build($statusVariables));
        $display = new Display($this->config);
        $html = $display->fullHtml($statusVariables);
        $commands[] = Commands::setHtml($html);
        return $commands;
    }

    protected function delete($params, $statusVariables)
    {
        $actions = new Actions($this->config);
        $actions->delete($params['id']);
        $display = new Display($this->config);
        $html = $display->fullHtml($statusVariables);
        $commands[] = Commands::setHtml($html);
        return $commands;
    }

}
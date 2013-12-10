<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Grid1;


class Worker {

    protected $config = null;
    protected $model = null;

    /**
     * @param array $config
     */
    public function __construct($config)
    {
        $this->config = $config;
        if (empty($this->config['type'])) {
            $this->config['type'] = 'table';
        }
        switch($this->config['type']) {
            case 'table':
                $this->model = new ModelTable();
                break;
            default:
                throw new \Ip\CoreException('Undefined Grid type');
        }
    }

    public function handleAction(\Ip\Request $request)
    {
        $data = $request->getRequest();
        if (empty($data['method']) || !isset($data['params'])) {
            throw new \Ip\CoreException('Missing request data');
        }
        switch($data['method']) {
            case 'init':
                return $this->init($data);
                break;
            default:
                throw new \Ip\CoreException('Unknown grid action.');
        }

    }


    protected function init($post)
    {
        $commands = array();
        $commands[] = $this->refreshCommand();
        return new \Ip\Response\JsonRpc($commands);
    }

    protected function refreshCommand()
    {
        return array(
            'command' => 'setHtml',
            'html' => \Ip\View::create('view/table.php')->render()
        );
    }



}
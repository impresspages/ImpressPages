<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Grid;


class Worker
{

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
        switch ($this->config['type']) {
            case 'table':
                $this->model = new Model\Table($this->config);
                break;
            default:
                throw new \Ip\Exception('Undefined Grid type');
        }
    }

    public function handleMethod(\Ip\Request $request)
    {
        $commands = $this->model->handleMethod($request);
        return $commands;
    }


}
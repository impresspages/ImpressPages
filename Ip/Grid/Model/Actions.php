<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Grid\Model;


/**
 * Table helper class designated to do Grid actions
 * @package Ip\Grid\Model
 */
class Actions
{
    protected $config;

    public function __constrcut($config)
    {
        $this->config = $config;
    }

    public function init()
    {
        return array (
            $this->refreshCommand($statusVariables)
        );
    }

    public function page()
    {
        $statusVariables['page'] = $data['params']['page'];

        $commands = array();
        return $this->refreshCommands($statusVariables);

    }

    public function delete()
    {

        $this->delete($data['params']);

        return $this->refreshCommands($statusVariables);
    }
}
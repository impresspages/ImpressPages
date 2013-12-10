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

        if (empty($this->config['table'])) {
            throw new \Ip\CoreException('\'table\' configuration value missing.');
        }

        if (empty($this->config['fields'])) {
            $this->config['fields'] = $this->getTableFields($this->config['table']);
        }
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


    protected function getTableFields($tableName)
    {
        $sql = "SHOW COLUMNS FROM `".str_replace("`","", $tableName)."`";
        $result = ipDb()->fetchColumn($sql);
        return $result;
    }

    protected function fetch()
    {
        $sql = "
        SELECT
          *
        FROM
          `".str_replace("`","", $this->config['table'])."`
        WHERE
          1
        ";

        $result = ipDb()->fetchAll($sql);

        return $result;
    }


    protected function refresh()
    {
        $variables = array(
            'data' => $this->fetch()
        );
        $html = \Ip\View::create('../view/table.php', $variables)->render();
        $commands = array(
            $this->commandSetHtml($html)
        );
        return $commands;
    }

}
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
    /**
     * @var $config Config
     */
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }



    public function delete($id)
    {
        $sql = "
        DELETE FROM
            " . $this->config->tableName() . "
        WHERE
            " . $this->config->idField() . " = :id
        ";

        $params = array(
            'id' => $id
        );

        ipDb()->execute($sql, $params);
    }
}
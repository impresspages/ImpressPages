<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Grid\Model;

/**
 * Table helper class designated to prepare data for display
 * @package Ip\Internal\Grid\Model
 */
class Db
{

    /**
     * @var Config
     */
    protected $config = null;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }


    public function recordCount($where)
    {
        return ipDb()->fetchValue(
            "SELECT COUNT(*) FROM " . $this->config->tableName() . " " . $this->config->joinQuery(
            ) . " WHERE " . $where . " "
        );
    }

    public function fetch($from, $count, $where = 1)
    {


        $sql = "
        SELECT
          " . $this->config->selectFields() . "
        FROM
          " . $this->config->tableName() . "
          " . $this->config->joinQuery() . "
        WHERE
          " . $where . "
        ORDER BY
            " . $this->config->orderBy() . "
        LIMIT
            $from, $count
        ";

        $result = ipDb()->fetchAll($sql);

        return $result;
    }


    public function fetchRow($id)
    {
        $sql = "
        SELECT
          " . $this->config->selectFields() . "
        FROM
          " . $this->config->tableName() . "
          " . $this->config->joinQuery() . "
        WHERE
          " . $this->config->tableName() . ".`" . $this->config->idField() . "` = :id
        ";

        $params = array(
            'id' => $id
        );

        $result = ipDb()->fetchRow($sql, $params);

        return $result;
    }

}

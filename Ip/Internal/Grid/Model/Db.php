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

    protected $statusVariables = null;

    public function __construct(Config $config, $statusVariables)
    {
        $this->config = $config;
        $this->statusVariables = $statusVariables;
    }

    public function buildSqlWhere()
    {
        $where = $this->config->filter();
        $depth = Status::depth($this->statusVariables);
        if ($depth > 1) {
            $where .= ' and (' . $where . ') and ' . $this->config->tableName() . '.`' . $this->config->connectionField() . '` = ' . ipDb()->getConnection()->quote($this->statusVariables['gridParentId' . ($depth - 1)]);
        }

        $searchVariables = array();
        foreach ($this->statusVariables as $key => $value) {
            if (preg_match('/^s_/', $key)) {
                $searchVariables[substr($key, 2)] = $value;
            }
        }

        if (!empty($searchVariables)) {

            foreach ($this->config->fields() as $fieldData) {
                if (!empty($fieldData['type']) && $fieldData['type'] == 'Tab') {
                    continue;
                }
                $fieldObject = $this->config->fieldObject($fieldData);
                $fieldQuery = $fieldObject->searchQuery($searchVariables);
                if ($fieldQuery) {
                    if ($where != ' ') {
                        $where .= ' and ';
                    }
                    $where .= $fieldQuery;
                }
            }
        }


        return $where;
    }

    public function breadcrumbTitle($depth)
    {
        if ($depth == 0) {
            return $this->config->getTitle();
        }
        $subgridConfig = $this->config->subgridConfig($this->statusVariables, $depth - 1);
        $id = $this->statusVariables['gridParentId' . $depth];

        $title = ipDb()->fetchValue(
            "SELECT " . $subgridConfig->tableName() . ".`" . $subgridConfig->getBreadcrumbField() . "` FROM " . $subgridConfig->tableName() . " " . $subgridConfig->joinQuery(
            ) . " WHERE " . $subgridConfig->tableName() . '.`' . $subgridConfig->idField() . '` = ' . ipDb()->getConnection()->quote($id) . " "
        );
        return $title;
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
            " . $this->config->orderBy($this->statusVariables) . "
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

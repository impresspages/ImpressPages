<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Grid\Model;


/**
 * Table helper class designated to do Grid actions
 * @package Ip\Internal\Grid\Model
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
        $db = new Db($this->config);

        $fields = $this->config->fields();
        $curData = $db->fetchRow($id);
        foreach ($fields as $field) {
            $fieldObject = $this->config->fieldObject($field);
            $fieldObject->onDelete($id, $curData);
        }


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

    public function update($id, $data)
    {
        $db = new Db($this->config);

        $fields = $this->config->fields();
        $dbData = array();
        foreach($fields as $field) {
            $fieldObject = $this->config->fieldObject($field);
            $fieldData = $fieldObject->updateData($data);
            if (!is_array($fieldData)) {
                throw new \Ip\Exception("updateData method in class " . get_class($fieldObject) . " has to return array.");
            }
            $dbData = array_merge($dbData, $fieldData);
        }
        ipDb()->update($this->config->rawTableName(), $dbData, array($this->config->idField() => $id));
    }

    public function create($data)
    {
        $db = new Db($this->config);

        $fields = $this->config->fields();
        $dbData = array();
        foreach($fields as $field) {
            $fieldObject = $this->config->fieldObject($field);
            $fieldData = $fieldObject->createData($data);
            if (!is_array($fieldData)) {
                throw new \Ip\Exception("createData method in class " . get_class($fieldObject) . " has to return array.");
            }
            $dbData = array_merge($dbData, $fieldData);
        }

        $sortField = $this->config->sortField();
        if ($sortField) {
            if ($this->config->createPosition() == 'top') {
                $orderValue = ipDb()->selectValue($this->config->rawTableName(), $sortField, array(), ' ORDER BY ' . $sortField . ' DESC');
                $dbData[$sortField] = $orderValue + 1;
            } else {
                $orderValue = ipDb()->selectValue($this->config->rawTableName(), $sortField, array(), ' ORDER BY ' . $sortField .  ' ASC');
                $dbData[$sortField] = $orderValue - 1;
            }
        }

        ipDb()->insert($this->config->rawTableName(), $dbData);
    }

    public function move($id, $targetId, $beforeOrAfter)
    {
        $sortField = $this->config->sortField();

        $priority = ipDb()->selectValue($this->config->rawTableName(), $sortField, array('id' => $targetId));
        if ($priority === false) {
            throw new \Ip\Exception('Target record doesn\'t exist');
        }

        $sql = "
        SELECT
            `".str_replace('`', '', $sortField)."`
        FROM
            " . $this->config->tableName() . "
        WHERE
            `" . $sortField . "` " . ($beforeOrAfter == 'before' ? ' > ' : ' < ') . "  :rowNumber
        ORDER BY
            `" . $sortField . "` " . ($beforeOrAfter == 'before' ? ' ASC ' : ' DESC ') . "
        ";

        $params = array(
            'rowNumber' => $priority
        );

        $priority2 = ipDb()->fetchValue($sql, $params);
        if ($priority2 === false) {
            if ($beforeOrAfter == 'before') {
                $priority2 = $priority + 10;
            } else {
                $priority2 = $priority - 10;
            }
        }

        $avgPriority = ($priority + $priority2) / 2;

        ipDb()->update($this->config->rawTableName(), array($this->config->sortField() => $avgPriority), array($this->config->idField() => $id));
    }
}

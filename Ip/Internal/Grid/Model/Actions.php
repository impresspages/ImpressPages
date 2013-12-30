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
        $fields = $this->config->fields();
        $dbData = array();
        foreach($fields as $field) {
            $fieldObject = $this->config->fieldObject($field);
            $fieldData = $fieldObject->updateData($data);
            if (!is_array($fieldData)) {
                throw new \Ip\CoreException("updateData method in class " . get_class($fieldObject) . " has to return array.");
            }
            $dbData = array_merge($dbData, $fieldData);
        }
        ipDb()->update($this->config->rawTableName(), $dbData, array($this->config->idField() => $id));
    }

    public function move($id, $targetId, $beforeOrAfter)
    {
        $sortField = $this->config->sortField();

        $row = ipDb()->select('row_number', $this->config->rawTableName(), array('id' => $targetId));
        if (empty($row[0])) {
            throw new \Ip\CoreException('Target record doesn\'t exist');
        }
        $priority = $row[0]['row_number'];



        $sql = "
        SELECT
            `row_number`
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
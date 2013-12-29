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

        $targetRow = ipDb()->select('row_number', $this->config->rawTableName(), array('id' => $targetId));
        if (!$targetRow) {
            throw new \Ip\CoreException('Target record doesn\'t exist');
        }


        $sql = "
        SELECT
            `row_number`
        FROM
            " . $this->config->tableName() . "
        WHERE
            `" . $sortField . "` " . ($beforeOrAfter == 'before') ? ' < ' : '' . "
        ORDER BY
            `" . $sortField . "`
            " . ($beforeOrAfter == 'before') ? ' DESC ' : ' ASC ' . "
        LIMIT
            1
        ";

        $params = array(

        );

        ipDb()->fetchValue($sql, $params);
    }
}
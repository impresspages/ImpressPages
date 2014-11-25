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
     * @var $subgridConfig Config
     */
    protected $subgridConfig;
    protected $statusVariables;

    public function __construct($subgridConfig, $statusVariables)
    {
        $this->subgridConfig = $subgridConfig;
        $this->statusVariables = $statusVariables;
    }


    public function delete($id)
    {
        $db = new Db($this->subgridConfig, $this->statusVariables);

        $fields = $this->subgridConfig->fields();
        $curData = $db->fetchRow($id);
        foreach ($fields as $field) {
            $fieldObject = $this->subgridConfig->fieldObject($field);
            $fieldObject->beforeDelete($id, $curData);
        }


        $sql = "
        DELETE
            " . $this->subgridConfig->tableName() . "
        FROM
            " . $this->subgridConfig->tableName() . "
            " . $this->subgridConfig->joinQuery() . "
        WHERE
            " . $this->subgridConfig->tableName() . "." . $this->subgridConfig->idField() . " = :id
        ";

        $params = array(
            'id' => $id
        );

        if ($this->subgridConfig->beforeDelete()) {
            call_user_func($this->subgridConfig->beforeDelete(), $params['id']);
        }

        ipDb()->execute($sql, $params);
        if ($this->subgridConfig->afterDelete()) {
            call_user_func($this->subgridConfig->afterDelete(), $params['id']);
        }

        //remove records in child grids
        foreach ($fields as $field) {
            $fieldObject = $this->subgridConfig->fieldObject($field);
            $fieldObject->afterDelete($id, $curData);

            if ($field['type'] == 'Grid') {
                $childStatusVariables = Status::genSubgridVariables($this->statusVariables, $field['gridId'], $id);

                $subActions = new Actions(new Config($field['config']), $childStatusVariables);
                $childConfig = new Config($field['config']);
                $db = new Db($childConfig, $childStatusVariables);
                $where = $db->buildSqlWhere();
                $sql = "
                    SELECT
                        `" . $childConfig->idField() . "`
                    FROM
                        " . $childConfig->tableName() . "
                    WHERE
                        $where
                ";
                $idsToDelete = ipDb()->fetchColumn($sql);
                foreach ($idsToDelete as $idToDelete) {
                    $subActions->delete($idToDelete);
                }

            }
        }

    }

    public function update($id, $data)
    {
        $db = new Db($this->subgridConfig, $this->statusVariables);
        $oldData = $db->fetchRow($id);

        $fields = $this->subgridConfig->fields();
        $dbData = array();
        foreach ($fields as $field) {
            if (empty($field['field']) ||  $field['field'] == $this->subgridConfig->idField() || isset($field['allowUpdate']) && !$field['allowUpdate'] || !empty($field['type']) && $field['type'] == 'Tab') {
                continue;
            }
            $fieldObject = $this->subgridConfig->fieldObject($field);

            $fieldObject->beforeUpdate($id, $oldData, $data);
            $fieldData = $fieldObject->updateData($data);
            if (!is_array($fieldData)) {
                throw new \Ip\Exception("updateData method in class " . esc(
                    get_class($fieldObject)
                ) . " has to return array.");
            }
            $dbData = array_merge($dbData, $fieldData);
        }

        if ($this->subgridConfig->updateFilter()) {
            $dbData = call_user_func($this->subgridConfig->updateFilter(), $id, $dbData);
        }

        $this->updateDb($this->subgridConfig->rawTableName(), $dbData, $id);

        foreach ($fields as $field) {
            $this->subgridConfig->fieldObject($field)->afterUpdate($id, $oldData, $data);
        }

    }

    protected function updateDb($table, $update, $id)
    {
        if (empty($update)) {
            return false;
        }

        $sql = "UPDATE " . ipTable($table) . " " . $this->subgridConfig->joinQuery() . " SET ";
        $params = array();
        foreach ($update as $column => $value) {
            if ($column == $this->subgridConfig->idField()) {
                continue; //don't update id field
            }
            $sql .= "`{$column}` = ? , ";
            if (is_bool($value)) {
                $value = $value ? 1 : 0;
            }
            $params[] = $value;
        }
        $sql = substr($sql, 0, -2);

        $sql .= " WHERE ";


        $sql .= " " . ipTable($table) . ".`" . $this->subgridConfig->idField() . "` = ? ";
        $params[] = $id;


        return ipDb()->execute($sql, $params);
    }


    public function create($data)
    {
        $fields = $this->subgridConfig->fields();
        $dbData = array();
        foreach ($fields as $field) {
            if (!empty($field['type']) && $field['type'] == 'Tab') {
                continue;
            }

            $fieldObject = $this->subgridConfig->fieldObject($field);
            $fieldObject->beforeCreate(null, $data);
            $fieldData = $fieldObject->createData($data);
            if (!is_array($fieldData)) {
                throw new \Ip\Exception("createData method in class " . esc(
                    get_class($fieldObject)
                ) . " has to return array.");
            }
            $dbData = array_merge($dbData, $fieldData);
        }

        $sortField = $this->subgridConfig->sortField();
        if ($sortField) {
            if ($this->subgridConfig->createPosition() == 'top') {
                $orderValue = ipDb()->selectValue($this->subgridConfig->rawTableName(), "MIN(`$sortField`)", array());
                $dbData[$sortField] = is_numeric($orderValue) ? $orderValue - 1 : 1; // 1 if null
            } else {
                $orderValue = ipDb()->selectValue($this->subgridConfig->rawTableName(), "MAX(`$sortField`)", array());
                $dbData[$sortField] = is_numeric($orderValue) ? $orderValue + 1 : 1; // 1 if null
            }
        }

        $depth = Status::depth($this->statusVariables);
        if ($depth > 1) {
            $dbData[$this->subgridConfig->connectionField()] = $this->statusVariables['gridParentId' . ($depth - 1)];
        }

        if ($this->subgridConfig->createFilter()) {
            $dbData = call_user_func($this->subgridConfig->createFilter(), $dbData);
        }

        $recordId = ipDb()->insert($this->subgridConfig->rawTableName(), $dbData);

        foreach ($fields as $field) {
            $fieldObject = $this->subgridConfig->fieldObject($field);
            $fieldObject->afterCreate($recordId, $data);
        }
        return $recordId;
    }

    public function move($id, $targetId, $beforeOrAfter)
    {
        $sortField = $this->subgridConfig->sortField();

        $priority = ipDb()->selectValue($this->subgridConfig->rawTableName(), $sortField, array('id' => $targetId));
        if ($priority === false) {
            throw new \Ip\Exception('Target record doesn\'t exist');
        }

        $tableName = $this->subgridConfig->tableName();

        if ($beforeOrAfter == 'before') {
            $sql = "
            SELECT
                `{$sortField}`
            FROM
                {$tableName}
                " . $this->subgridConfig->joinQuery() . "
            WHERE
                `{$sortField}` < :rowNumber
            ORDER BY
                `$sortField` DESC";
        } else {
            $sql = "
            SELECT
                `{$sortField}`
            FROM
                {$tableName}
            WHERE
                `{$sortField}` > :rowNumber
            ORDER BY
                `$sortField` ASC";
        }

        $params = array(
            'rowNumber' => $priority
        );

        $priority2 = ipDb()->fetchValue($sql, $params);

        if ($priority2 === null) {
            if ($beforeOrAfter == 'before') {
                $newPriority = $priority - 5;
            } else {
                $newPriority = $priority + 5;
            }
        } else {
            $newPriority = ($priority + $priority2) / 2;
        }

        ipDb()->update(
            $this->subgridConfig->rawTableName(),
            array($sortField => $newPriority),
            array($this->subgridConfig->idField() => $id)
        );
    }

    public function movePosition($id, $position)
    {
        if ($position < 1) {
            $position = 1;
        }
        $sortField = $this->subgridConfig->sortField();

        $sql = "
            SELECT
                `" . $sortField . "`
            FROM
                " . $this->subgridConfig->tableName() . "
            WHERE
                " . $this->subgridConfig->idField() . " != " . (int)$id . " and (" . $this->subgridConfig->filter() . ")
            ORDER BY
               `" . $sortField . "` " . $this->subgridConfig->sortDirection() . "
            LIMIT ?, 1
        ";

        if ($position == 1) {
            $record1 = null;
        } else {
            $preparedSql = str_replace('?', $position - 2, $sql);
            $record1 = ipDb()->fetchAll($preparedSql);
        }
        $preparedSql = str_replace('?', $position - 1, $sql);
        $record2 = ipDb()->fetchAll($preparedSql);

        if (!isset($record1[0][$sortField]) && !isset($record2[0][$sortField])) {
            if ($position == 0) {
                return;
            }

            $orderBy = 'ORDER BY ' . $sortField . ' ' . ($this->subgridConfig->sortDirection() == 'asc' ? 'desc' : 'asc'); //sort in opposite. This way when selecting first item with selectValue, we will get the last item
            $highestPriority = ipDb()->selectValue($this->subgridConfig->rawTableName(), $sortField, array(), $orderBy);
            $newPriority = $highestPriority + 5;
        } else {
            if (isset($record1[0][$sortField])) {
                $priority1 = $record1[0][$sortField];
            } else {
                if (isset($record2[0][$sortField])) {
                    $priority1 = $record2[0][$sortField] - 5;
                }
            }

            if (isset($record2[0][$sortField])) {
                $priority2 = $record2[0][$sortField];
            } else {
                if (isset($record1[0][$sortField])) {
                    $priority2 = $record1[0][$sortField] + 5;
                }
            }

            $newPriority = ($priority1 + $priority2) / 2;
        }



        ipDb()->update(
            $this->subgridConfig->rawTableName(),
            array($sortField => $newPriority),
            array($this->subgridConfig->idField() => $id)
        );
    }

}

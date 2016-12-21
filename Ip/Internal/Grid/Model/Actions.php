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
            " . $db->joinQuery() . "
        WHERE
            " . $this->subgridConfig->tableName() . ".`" . $this->subgridConfig->idField() . "` = :id
        ";

        $params = array(
            'id' => $id
        );

        $callables = $this->subgridConfig->beforeDelete();
        if ($callables) {
            if (is_array($callables) && !is_callable($callables)) {
                foreach($callables as $callable) {
                    call_user_func($callable, $params['id']);
                }
            } else {
                call_user_func($callables, $params['id']);
            }
        }

        ipDb()->execute($sql, $params);

        if ($this->subgridConfig->isMultilingual()) {
            $sql = "
            DELETE

            FROM
                " . $this->subgridConfig->languageTableName() . "
            WHERE
                " . $this->subgridConfig->languageTableName() . ".`" . $this->subgridConfig->languageForeignKeyField() . "` = :id
            ";

            ipDb()->execute($sql, $params);
        }

        $callables = $this->subgridConfig->afterDelete();
        if ($callables) {
            if (is_array($callables) && !is_callable($callables)) {
                foreach($callables as $callable) {
                    call_user_func($callable, $params['id']);
                }
            } else {
                call_user_func($callables, $params['id']);
            }
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
        $dbData = [];
        $languageData = [];
        $languages = ipContent()->getLanguages();
        foreach ($fields as $field) {
            if (empty($field['field']) ||  $field['field'] == $this->subgridConfig->idField() || isset($field['allowUpdate']) && !$field['allowUpdate'] || !empty($field['type']) && $field['type'] == 'Tab' || !empty($field['ignoreDb'])) {
                continue;
            }

            $fieldObject = $this->subgridConfig->fieldObject($field);
            $fieldObject->beforeUpdate($id, $oldData, $data); //the same event for both: multilingual and non multilingual fields. Each field may store it's multilingual state from constructor and act differently on this event if needed. $oldData is not very correct in multilingual context. But that's still the bets way to go.

            if (empty($field['multilingual'])) {
                $fieldData = $fieldObject->updateData($data);
                if (!is_array($fieldData)) {
                    throw new \Ip\Exception("updateData method in class " . esc(
                            get_class($fieldObject)
                        ) . " has to return array.");
                }
                $dbData = array_merge($dbData, $fieldData);
            } else {
                foreach($languages as $language) {
                    $tmpData = $data;
                    if (isset($data[$field['field'] . '_' . $language->getCode()])) {
                        $tmpData[$field['field']] = $data[$field['field'] . '_' . $language->getCode()];
                    }

                    $fieldObject = $this->subgridConfig->fieldObject($field);
                    $fieldData = $fieldObject->updateData($tmpData);
                    if (!is_array($fieldData)) {
                        throw new \Ip\Exception("createData method in class " . esc(
                                get_class($fieldObject)
                            ) . " has to return array.");
                    }

                    if (empty($languageData[$language->getCode()])) {
                        $languageData[$language->getCode()] = [];
                    }
                    $languageData[$language->getCode()] = array_merge($languageData[$language->getCode()], $fieldData);
                }
            }
        }

        if ($this->subgridConfig->updateFilter()) {
            $dbData = call_user_func($this->subgridConfig->updateFilter(), $id, $dbData);
        }

        if ($this->subgridConfig->isMultilingual() && $this->subgridConfig->updateLanguageFilter()) {
            $languageData = call_user_func($this->subgridConfig->updateLanguageFilter(), $id, $languageData);
        }

        $this->updateDb($this->subgridConfig->rawTableName(), $dbData, $id);
        if (!empty($languageData)) {
            foreach($languageData as $languageCode => $rawData) {
                $translationExists = ipDb()->selectRow($this->subgridConfig->rawLanguageTableName(), '*', array($this->subgridConfig->languageCodeField() => $languageCode, $this->subgridConfig->languageForeignKeyField() => $id));
                if (!$translationExists) {
                    $insertData = $rawData;
                    $insertData[$this->subgridConfig->languageCodeField()] = $languageCode;
                    $insertData[$this->subgridConfig->languageForeignKeyField()] = $id;
                    ipDb()->insert($this->subgridConfig->rawLanguageTableName(), $insertData);
                }
                $this->updateDb($this->subgridConfig->rawTableName(), $rawData, $id, $languageCode);
            }
        }

        foreach ($fields as $field) {
            $this->subgridConfig->fieldObject($field)->afterUpdate($id, $oldData, $data);
        }

    }

    protected function updateDb($table, $update, $id, $languageCode = null)
    {
        if (empty($update)) {
            return false;
        }


        $db = new Db($this->subgridConfig, $this->statusVariables);
        $db->setDefaultLanguageCode($languageCode);

        $sql = "UPDATE " . ipTable($table) . " " . $db->joinQuery() . " SET ";
        $params = [];
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


        $result = ipDb()->execute($sql, $params);



        return $result;
    }


    public function create($data)
    {
        $languages = ipContent()->getlanguages();
        $fields = $this->subgridConfig->fields();
        $dbData = [];
        $languageData = [];
        foreach ($fields as $field) {
            if (!empty($field['type']) && $field['type'] == 'Tab' && empty($field['preview']) || !empty($field['ignoreDb'])) {
                continue;
            }

            $fieldObject = $this->subgridConfig->fieldObject($field);
            $fieldObject->beforeCreate(null, $data); //one vent for multilingual and non-multilingual fields.

            if (empty($field['multilingual'])) {
                $fieldData = $fieldObject->createData($data);
                if (!is_array($fieldData)) {
                    throw new \Ip\Exception("createData method in class " . esc(
                            get_class($fieldObject)
                        ) . " has to return array.");
                }
                $dbData = array_merge($dbData, $fieldData);
            } else {
                foreach($languages as $language) {
                    $tmpData = $data;
                    if (isset($data[$field['field'] . '_' . $language->getCode()])) {
                        $tmpData[$field['field']] = $data[$field['field'] . '_' . $language->getCode()];
                    }

                    $fieldObject = $this->subgridConfig->fieldObject($field);
                    $fieldData = $fieldObject->createData($tmpData);
                    if (!is_array($fieldData)) {
                        throw new \Ip\Exception("createData method in class " . esc(
                                get_class($fieldObject)
                            ) . " has to return array.");
                    }

                    if (empty($languageData[$language->getCode()])) {
                        $languageData[$language->getCode()] = [];
                    }
                    $languageData[$language->getCode()] = array_merge($languageData[$language->getCode()], $fieldData);
                }

            }

        }

        $sortField = $this->subgridConfig->sortField();
        if ($sortField) {
            if ($this->subgridConfig->createPosition() == 'top') {
                $orderValue = ipDb()->selectValue($this->subgridConfig->rawTableName(), "MIN(`$sortField`)", []);
                $dbData[$sortField] = is_numeric($orderValue) ? $orderValue - 1 : 1; // 1 if null
            } else {
                $orderValue = ipDb()->selectValue($this->subgridConfig->rawTableName(), "MAX(`$sortField`)", []);
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

        if ($this->subgridConfig->isMultilingual() && $this->subgridConfig->createLanguageFilter()) {
            $languageData = call_user_func($this->subgridConfig->createLanguageFilter(), $languageData);
        }



        $recordId = ipDb()->insert($this->subgridConfig->rawTableName(), $dbData);
        if (!empty($languageData)) {
            foreach($languageData as $languageCode => $rawData) {
                $rawData[$this->subgridConfig->languageCodeField()] = $languageCode;
                $rawData[$this->subgridConfig->languageForeignKeyField()] = $recordId;
                ipDb()->insert($this->subgridConfig->rawLanguageTableName(), $rawData);
            }
        }

        foreach ($fields as $field) {
            $fieldObject = $this->subgridConfig->fieldObject($field);
            $fieldObject->afterCreate($recordId, $data);
        }
        return $recordId;
    }

    public function move($id, $targetId, $beforeOrAfter)
    {
        if($this->subgridConfig->sortDirection() == 'desc') {
            //switch $beforeOrAfter value
            if ($beforeOrAfter == 'before') {
                $beforeOrAfter = 'after';
            } else {
                $beforeOrAfter = 'before';
            }
        }
        $sortField = $this->subgridConfig->sortField();

        $priority = ipDb()->selectValue($this->subgridConfig->rawTableName(), $sortField, array('id' => $targetId));
        if ($priority === false) {
            throw new \Ip\Exception('Target record doesn\'t exist');
        }

        $tableName = $this->subgridConfig->tableName();

        $db = new Db($this->subgridConfig, $this->statusVariables);
        if ($beforeOrAfter == 'before') {
            $sql = "
            SELECT
                `{$sortField}`
            FROM
                {$tableName}
                " . $db->joinQuery() . "
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

        //$directionInverse gives opposite sign number when increasing or decresing values depending on sort direction
        if ($this->subgridConfig->sortDirection() == 'asc') {
            $directionInverse = 1;
        } else {
            $directionInverse = -1;
        }



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
            $highestPriority = ipDb()->selectValue($this->subgridConfig->rawTableName(), $sortField, [], $orderBy);
            $newPriority = $highestPriority + 5 * $directionInverse;
        } else {
            if (isset($record1[0][$sortField])) {
                $priority1 = $record1[0][$sortField];
            } else {
                if (isset($record2[0][$sortField])) {
                    $priority1 = $record2[0][$sortField] - 5 * $directionInverse;
                }
            }

            if (isset($record2[0][$sortField])) {
                $priority2 = $record2[0][$sortField];
            } else {
                if (isset($record1[0][$sortField])) {
                    $priority2 = $record1[0][$sortField] + 5 * $directionInverse;
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

<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Grid\Model\Field;


class RepositoryFile extends \Ip\Internal\Grid\Model\Field
{
    protected $field = '';
    protected $label = '';
    protected $defaultValue = '';
    protected $repositoryBindKey = 'Grid';

    public function __construct($fieldFieldConfig, $wholeConfig)
    {
        parent::__construct($fieldFieldConfig, $wholeConfig);

        if (!empty($fieldFieldConfig['repositoryBindKey'])) {
            $this->repositoryBindKey = $fieldFieldConfig['repositoryBindKey'];
        } else {
            $this->repositoryBindKey = 'Table_' . $wholeConfig['table'] . '_' . $this->field;
        }

        $this->fileLimit = 1;

        if (!empty($this->defaultValue) && !is_array($this->defaultValue)) {
            $this->defaultValue = array($this->defaultValue);
        }

    }

    public function preview($recordData)
    {
        return esc($recordData[$this->field]);
    }

    public function createField()
    {
        $field = new \Ip\Form\Field\RepositoryFile(array(
            'label' => $this->label,
            'name' => $this->field
        ));
        if ($this->fileLimit !== null) {
            $field->setFileLimit($this->fileLimit);
        }
        $field->setValue($this->defaultValue);
        return $field;
    }

    public function createData($postData)
    {
        if (isset($postData[$this->field][0])) {
            return array($this->field => $postData[$this->field][0]);
        }
        return array();
    }

    public function updateField($curData)
    {
        $field = new \Ip\Form\Field\RepositoryFile(array(
            'label' => $this->label,
            'name' => $this->field
        ));
        if ($this->fileLimit !== null) {
            $field->setFileLimit($this->fileLimit);
        }
        $field->setValue(array($curData[$this->field]));
        return $field;
    }

    public function updateData($postData)
    {
        $field = new \Ip\Form\Field\RepositoryFile(array(
            'label' => $this->label,
            'name' => $this->field
        ));
        return array ($this->field => $field->getValueAsString($postData, $this->field));
    }


    public function searchField($searchVariables)
    {
        $field = new \Ip\Form\Field\Text(array(
            'label' => $this->label,
            'name' => $this->field
        ));
        if (!empty($searchVariables[$this->field])) {
            $field->setValue($searchVariables[$this->field]);
        }
        return $field;
    }

    public function searchQuery($searchVariables)
    {
        if (isset($searchVariables[$this->field]) && $searchVariables[$this->field] !== '') {
            return $this->field . ' like \'%'.mysql_real_escape_string($searchVariables[$this->field]) . '%\' ';
        }
    }

    public function afterDelete($recordId, $curData)
    {
        if (!empty($curData[$this->field])) {
            \Ip\Internal\Repository\Model::unbindFile($curData[$this->field], $this->repositoryBindKey, $recordId);
        }
    }

    public function afterCreate($recordId, $curData)
    {
        if (!empty($curData[$this->field])) {
            \Ip\Internal\Repository\Model::bindFile($curData[$this->field][0], $this->repositoryBindKey, $recordId);
        }
    }

    public function afterUpdate($recordId, $oldData, $newData)
    {
        if (!isset($oldData[$this->field])) {
            $oldData[$this->field] = '';
        }
        if (!isset($newData[$this->field])) {
            $newData[$this->field] = '';
        }

        if (!empty($oldData[$this->field]) && $oldData[$this->field] != $newData[$this->field]) {
            \Ip\Internal\Repository\Model::unbindFile($oldData[$this->field], $this->repositoryBindKey, $recordId);
        }

        if (!empty($newData[$this->field]) && $oldData[$this->field] != $newData[$this->field]) {
            \Ip\Internal\Repository\Model::bindFile($newData[$this->field][0], $this->repositoryBindKey, $recordId);
        }
    }

}

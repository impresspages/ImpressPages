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
    protected $path = '';
    protected $repositoryBindKey = 'Grid';
    protected $fileLimit = null;

    public function __construct($fieldFieldConfig, $wholeConfig)
    {
        parent::__construct($fieldFieldConfig, $wholeConfig);

        if (!empty($fieldFieldConfig['repositoryBindKey'])) {
            $this->repositoryBindKey = $fieldFieldConfig['repositoryBindKey'];
        } else {
            $this->repositoryBindKey = 'Table_' . $wholeConfig['table'] . '_' . $this->field;
        }

        if (array_key_exists('fileLimit', $fieldFieldConfig)) {
            $this->fileLimit = $fieldFieldConfig['fileLimit'];
        } else {
            $this->fileLimit = 1;
        }

        if (!empty($this->defaultValue) && !is_array($this->defaultValue)) {
            $this->defaultValue = array($this->defaultValue);
        }

        if (array_key_exists('path', $fieldFieldConfig)) {
            $this->path = $fieldFieldConfig['path'];
        } else {
            $this->path = '';
        }

    }

    public function preview($recordData)
    {
        if ($this->fileLimit == 1) {
            return esc($recordData[$this->field]);
        } else {
            $data = json_decode($recordData[$this->field]);
            if (is_array($data)) {
                $data = implode(', ', $data);
            }
            return esc($data);
        }

    }

    public function createField()
    {
        $field = new \Ip\Form\Field\RepositoryFile(array(
            'label' => $this->label,
            'name' => $this->field,
            'layout' => $this->layout,
            'attributes' => $this->attributes,
            'path' => $this->path
        ));
        if ($this->fileLimit !== null) {
            $field->setFileLimit($this->fileLimit);
        }
        $field->setValue(json_decode($this->defaultValue));
        return $field;
    }

    public function createData($postData)
    {
        if (!isset($postData[$this->field][0])) {
            return [];
        }

        if ($this->fileLimit == 1) {
            $value = $postData[$this->field][0];
        } else {
            $value = json_encode($postData[$this->field]);
        }
        return array($this->field => $value);
    }

    public function updateField($curData)
    {
        $field = new \Ip\Form\Field\RepositoryFile(array(
            'label' => $this->label,
            'name' => $this->field,
            'layout' => $this->layout,
            'attributes' => $this->attributes,
            'path' => $this->path
        ));
        if ($this->fileLimit !== null) {
            $field->setFileLimit($this->fileLimit);
        }
        if (isset($curData[$this->field])){
        if ($this->fileLimit == 1) {
            $field->setValue(array($curData[$this->field]));
        } else {
            $field->setValue(json_decode($curData[$this->field]));
            }
        }
        return $field;
    }

    public function updateData($postData)
    {
        if ($this->fileLimit == 1) {
            if (!isset($postData[$this->field][0])) {
                $value = null;
            } else {
                $value = $postData[$this->field][0];
            }
        } else {
            if (!isset($postData[$this->field][0])) {
                $value = json_encode([]);
            } else {
                $value = json_encode($postData[$this->field]);
            }
        }
        return array($this->field => $value);
    }


    public function searchField($searchVariables)
    {
        $field = new \Ip\Form\Field\Text(array(
            'label' => $this->label,
            'name' => $this->field,
            'layout' => $this->layout,
            'attributes' => $this->attributes
        ));
        if (!empty($searchVariables[$this->field])) {
            $field->setValue($searchVariables[$this->field]);
        }
        return $field;
    }

    public function searchQuery($searchVariables)
    {
        if (isset($searchVariables[$this->field]) && $searchVariables[$this->field] !== '') {
            return '`' . $this->field . '` like ' . ipDb()->getConnection()->quote(
                '%' . $searchVariables[$this->field] . '%'
            ) . ' ';
        }
        return null;
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

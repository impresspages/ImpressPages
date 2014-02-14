<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Grid\Model\Field;


class File extends \Ip\Internal\Grid\Model\Field
{
    protected $field = '';
    protected $label = '';
    protected $defaultValue = '';

    public function __construct($config)
    {
        if (empty($config['field'])) {
            throw new \Ip\Exception('\'field\' option required for text field');
        }
        $this->field = $config['field'];

        if (!empty($config['label'])) {
            $this->label = $config['label'];
        }

        if (isset($config['fileLimit'])) {
            $this->fileLimit = $config['fileLimit'];
        }

        if (!empty($config['defaultValue'])) {
            $this->defaultValue = $config['defaultValue'];
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
        if (isset($postData[$this->field])) {
            return array($this->field => $postData[$this->field]);
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
        $field->setValue($curData[$this->field]);
        return $field;
    }

    public function updateData($postData)
    {
        return array($this->field => $postData[$this->field]);
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
        //do nothing by default
    }

    public function afterCreate($recordId, $curData)
    {
        //do nothing by default
    }

    public function afterUpdate($recordId, $oldData, $newData)
    {
        //do nothing by default
    }

}

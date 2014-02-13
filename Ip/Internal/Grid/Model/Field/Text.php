<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Grid\Model\Field;


class Text extends \Ip\Internal\Grid\Model\Field
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
        $field = new \Ip\Form\Field\Text(array(
            'label' => $this->label,
            'name' => $this->field
        ));
        $field->setValue($this->defaultValue);
        return $field;
    }

    public function createData($postData)
    {
        return array($this->field => $postData[$this->field]);
    }

    public function updateField($curData)
    {
        $field = new \Ip\Form\Field\Text(array(
            'label' => $this->label,
            'name' => $this->field
        ));
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

    public function searchQuery($postData)
    {
        return $this->field . ' like \'%'.mysql_real_escape_string($postData[$this->field]) . '%\' ';
    }
}

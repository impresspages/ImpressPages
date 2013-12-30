<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Grid\Model\Field;


class Select extends \Ip\Internal\Grid\Model\Field
{
    protected $field = '';
    protected $label = '';
    protected $defaultValue = '';
    protected $values = array();

    public function __construct($config)
    {
        if (empty($config['field'])) {
            throw new \Ip\CoreException('\'field\' option required for text field');
        }
        $this->field = $config['field'];

        if (!empty($config['label'])) {
            $this->label = $config['label'];
        }

        if (!empty($config['values'])) {
            $this->values = $config['values'];
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
        $field = new \Ip\Form\Field\Select(array(
            'label' => $this->label,
            'name' => $this->field,
            'values' => $this->values
        ));
        $field->setDefaultValue($this->defaultValue);
        return $field;
    }

    public function createData($postData)
    {
    }

    public function updateField($curData)
    {
        $field = new \Ip\Form\Field\Select(array(
            'label' => $this->label,
            'name' => $this->field,
            'values' => $this->values
        ));
        $field->setDefaultValue($curData[$this->field]);
        return $field;
    }

    public function updateData($postData)
    {
        return array($this->field => $postData[$this->field]);
    }


    public function searchField()
    {
    }

    public function searchQuery($postData)
    {
    }
}
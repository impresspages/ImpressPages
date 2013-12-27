<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Grid\Model\Field;


class Checkbox extends \Ip\Grid\Model\Field
{
    protected $field = '';
    protected $label = '';
    protected $defaultValue = '';

    public function __construct($config)
    {
        if (empty($config['field'])) {
            throw new \Ip\CoreException('\'field\' option required for text field');
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
        $field = new \Ip\Form\Field\Checkbox(array(
            'label' => $this->label,
            'name' => $this->field
        ));
        $field->setDefaultValue($this->defaultValue);
        return $field;
    }

    public function createData($postData)
    {
    }

    public function updateField($curData)
    {
        $field = new \Ip\Form\Field\Checkbox(array(
            'label' => $this->label,
            'name' => $this->field
        ));
        $field->setDefaultValue($curData[$this->field]);
        return $field;
    }

    public function updateData($postData)
    {

    }


    public function searchField()
    {
    }

    public function searchQuery($postData)
    {
    }
}
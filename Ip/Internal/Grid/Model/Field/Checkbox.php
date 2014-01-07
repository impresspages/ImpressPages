<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Grid\Model\Field;


class Checkbox extends \Ip\Internal\Grid\Model\Field
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
        return array($this->field => !empty($postData[$this->field]));
    }


    public function searchField()
    {
    }

    public function searchQuery($postData)
    {
    }
}
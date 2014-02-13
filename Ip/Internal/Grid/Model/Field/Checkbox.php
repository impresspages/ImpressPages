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
        $field->setValue($this->defaultValue);
        return $field;
    }

    public function createData($postData)
    {
        return array($this->field => !empty($postData[$this->field]));
    }

    public function updateField($curData)
    {
        $field = new \Ip\Form\Field\Checkbox(array(
            'label' => $this->label,
            'name' => $this->field
        ));
        $field->setValue($curData[$this->field]);
        return $field;
    }

    public function updateData($postData)
    {
        return array($this->field => !empty($postData[$this->field]));
    }


    public function searchField($searchVariables)
    {
        $values = array(
            array(null, ''),
            array('1', __('Yes', 'ipAdmin', false)),
            array('0', __('No', 'ipAdmin', false))

        );


        $field = new \Ip\Form\Field\Select(array(
            'label' => $this->label,
            'name' => $this->field,
            'values' => $values,
            'value' => null
        ));
        if (isset($searchVariables[$this->field])) {
            $field->setValue($searchVariables[$this->field]);
        }
        return $field;

    }

    public function searchQuery($searchVariables)
    {
        if (isset($searchVariables[$this->field]) && $searchVariables[$this->field] !== '') {
            if ($searchVariables[$this->field]) {
                return $this->field;
            } else {
                return 'not '.$this->field;
            }

        }
    }
}

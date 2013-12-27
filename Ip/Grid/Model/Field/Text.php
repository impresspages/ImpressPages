<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Grid\Model\Field;


class Text extends \Ip\Grid\Model\Field
{
    protected $field = '';
    protected $label = '';

    public function __construct($config)
    {
        if (empty($config['field'])) {
            throw new \Ip\CoreException('\'field\' option required for text field');
        }
        $this->field = $config['field'];

        if (!empty($config['label'])) {
            $this->label = $config['label'];
        }
    }

    public function preview($recordData)
    {
        return esc($recordData[$this->field]);
    }

    public function createField()
    {
    }

    public function createQuery($postData)
    {
    }

    public function updateField($curData)
    {
        $field = new \Ip\Form\Field\Text(array(
            'label' => $this->label,
            'name' => $this->field
        ));
        $field->setDefaultValue($curData[$this->field]);
        return $field;
    }

    public function updateQuery($postData)
    {
    }


    public function searchField()
    {
    }

    public function searchQuery($postData)
    {
    }
}
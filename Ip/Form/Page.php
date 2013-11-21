<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Form;


class Page
{
    /**
     * @var Fieldset[]
     */
    protected $fieldsets;
    protected $label;

    public function __construct()
    {
        $this->fieldsets = array();
    }

    public function addFieldset($fieldset)
    {
        $this->fieldsets[] = $fieldset;
    }

    /**
     *
     * Add field to last fielset. Create fieldset if does not exist.
     * @param Field $field
     */
    public function addField(Field\Field $field)
    {
        if (count($this->fieldsets) == 0) {
            $this->addFieldset(new Fieldset());
        }
        end($this->fieldsets)->addField($field);
    }

    /**
     * Remove field from fieldset
     * @param string $fieldName
     * @return int removed fields count
     */
    public function removeField($fieldName)
    {
        $count = 0;
        foreach ($this->fieldsets as $key => $fieldset) {
            $count += $fieldset->removeField($fieldName);
        }
        return $count;
    }

    /**
     *
     * Return all fieldset
     * @return Fieldset[]
     */
    public function getFieldsets()
    {
        return $this->fieldsets;
    }


    /**
     * Return all fields (from all fieldsets in one level array)
     */
    public function getFields()
    {
        $fieldsets = $this->getFieldsets();
        $fields = array();
        foreach ($fieldsets as $fieldset) {
            $fields = array_merge($fields, $fieldset->getFields());
        }
        return $fields;
    }

    public function getField($name)
    {
        $allFields = $this->getFields();
        foreach ($allFields as $key => $field) {
            if ($field->getName() == $name) {
                return $field;
            }
        }
        return false;
    }


    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

}
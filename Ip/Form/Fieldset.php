<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Form;


class Fieldset
{
    protected $fields;
    protected $label;
    protected $attributes;

    public function __construct($label = null)
    {
        if ($label) {
            $this->setLabel($label);
        }
        $this->fields = array();
        $this->attributes = array();
    }

    /**
     *
     * Add field to last fielset. Create fieldset if does not exist.
     * @param Field $field
     */
    public function addField(\Ip\Form\Field $field)
    {
        $this->fields[] = $field;
    }

    /**
     * Remove field from fieldset
     * @param string $fieldName
     * @return int removed fields count
     */
    public function removeField($fieldName)
    {
        $count = 0;
        foreach ($this->fields as $key => $field) {
            if ($field->getName() == $fieldName) {
                unset($this->fields[$key]);
                $count++;
            }
        }
        return $count;
    }

    /**
     *
     * Return all fields
     */
    public function getFields()
    {
        return $this->fields;
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

    /**
     * Get field attributes as HTML string
     *
     * @param $doctype \Ip\View doctype constant
     * @return string
     */
    public function getAttributesStr($doctype) {
        $answer = '';
        foreach ($this->getAttributes() as $attributeKey => $attributeValue) {
            $answer .= ' '.htmlspecialchars($attributeKey).'="'.htmlspecialchars($attributeValue).'"';
        }
        return $answer;
    }

    /**
     * Get all HTML attributes of the field
     *
     * @return array Field HTML attributes
     */
    public function getAttributes() {
        return $this->attributes;
    }

    /**
     * Add HTML attribute to input field. Alternative way to setAttributes method.
     *
     * @param string $name Attribute name
     * @param string $value Attribute value
     *
     */
    public function addAttribute($name, $value) {
        $this->attributes[$name] = $value;
    }

    /**
     * Remove HTML attribute
     *
     * @param $name
     */
    public function removeAttribute($name) {
        unset($this->attributes[$name]);
    }


}

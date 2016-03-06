<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form;


class Fieldset
{

    /**
     * @var \Ip\Form\Field[]
     */
    protected $fields;
    /**
     * @var string $label
     */
    protected $label;
    protected $attributes;

    /**
     * Constructor
     *
     * @param string $label
     */
    public function __construct($label = null)
    {
        if ($label) {
            $this->setLabel($label);
        }
        $this->fields = array();
        $this->attributes = array();
    }

    /**
     * Add field to last fielset
     *
     * Create fieldset if does not exist.
     * @param Field $field
     */
    public function addField(\Ip\Form\Field $field)
    {
        $this->fields[] = $field;
    }

    /**
     * Remove field from fieldset
     *
     * @param string $fieldName
     * @return int Removed fields count.
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
     * Return all fields
     * @return \Ip\Form\Field[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Get field name
     *
     * @param string $name
     * @return object|bool
     */
    public function getField($name)
    {
        $allFields = $this->getFields();
        foreach ($allFields as $field) {
            if ($field->getName() == $name) {
                return $field;
            }
        }

        return false;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set label
     *
     * @param string $label
     */
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
    public function getAttributesStr($doctype)
    {
        $answer = '';
        foreach ($this->getAttributes() as $attributeKey => $attributeValue) {
            $answer .= ' ' . htmlspecialchars($attributeKey) . '="' . escAttr($attributeValue) . '"';
        }

        return $answer;
    }

    /**
     * Get all HTML attributes of the field
     *
     * @return array Field HTML attributes.
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param $name
     * @return string
     */
    public function getAttribute($name)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        } else {
            return false;
        }
    }

    /**
     * Add HTML attribute to input field.
     *
     * Alternative way to setAttributes method.
     * @param string $name Attribute name.
     * @param string $value Attribute value.
     *
     */
    public function addAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Remove HTML attribute
     *
     * @param string $name
     */
    public function removeAttribute($name)
    {
        unset($this->attributes[$name]);
    }

}

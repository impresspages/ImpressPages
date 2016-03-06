<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form;


/**
 * Web page form field
 *
 * @package Ip\Form
 */
abstract class Field
{

    // Layouts define how field should be treated in the view.
    const LAYOUT_DEFAULT = 'default';
    const LAYOUT_BLANK = 'blank';
    const LAYOUT_NO_LABEL = 'noLabel';

    // Types define how field values should be used in controller. Eg. 'system' fields.
    // Should not be sent by email as form post data. They are just helpers to deliver.
    // Form to the controller (eg. hidden fields, submit button, captcha).
    const TYPE_REGULAR = 'regular';
    const TYPE_SYSTEM = 'system';

    protected $label;
    protected $note;
    protected $hint;
    protected $name;
    protected $dbField; // Where in db this value should be stored by the method writeToDatabase.
    protected $value;
    protected $validators;
    protected $attributes;
    protected $classes; // CSS classes to be added to input field.
    protected $environment;
    protected $layout;

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        $this->validators = array();

        if (!empty($options['validators'])) {
            if (!is_array($options['validators'])) {
                $options['validators'] = array($options['validators']);
            }
            foreach ($options['validators'] as $validator) {
                $this->addValidator($validator);
            }
        }

        if (!empty($options['label'])) {
            $this->setLabel($options['label']);
        }
        if (!empty($options['note'])) {
            $this->setNote($options['note']);
        }
        if (!empty($options['hint'])) {
            $this->setHint($options['hint']);
        }
        if (!empty($options['name'])) {
            $this->setName($options['name']);
        }
        if (isset($options['value'])) {
            $this->setValue($options['value']);
        }
        if (!empty($options['layout'])) {
            $this->setLayout($options['layout']);
        }

        $this->classes = array();
        if (!empty($options['css'])) { //alias of 'class'
            $this->setCssClasses($options['css']);
        }
        if (!empty($options['class'])) {
            $this->setCssClasses($options['class']);
        }

        if (!empty($options['attributes'])) {
            $this->setAttributes($options['attributes']);
        } else {
            $this->setAttributes(array());
        }
        if (!isset($this->attributes['id'])) {
            $this->addAttribute('id', 'field_' . rand(1, PHP_INT_MAX));
        }

    }

    /**
     * Render field's HTML code
     *
     * @param string $doctype \Ip\View doctype constant
     * @param string $environment \Ip\Form::ENVIRONMENT_ADMIN or \Ip\Form::ENVIRONMENT_PUBLIC
     * @return string
     */
    public abstract function render($doctype, $environment);

    /**
     * Get field layout
     *
     * @return string
     */
    public function getLayout()
    {
        if (empty($this->layout) || !in_array($this->layout, array(self::LAYOUT_BLANK, self::LAYOUT_DEFAULT, self::LAYOUT_NO_LABEL))) {
            return self::LAYOUT_DEFAULT;
        }
        return $this->layout;
    }

    /**
     * Get field type
     *
     * @return string
     */
    public function getType()
    {
        return self::TYPE_REGULAR;
    }

    /**
     * Get field attributes as HTML string
     *
     * @param string $doctype \Ip\View doctype constant
     * @return string
     */
    public function getAttributesStr($doctype)
    {
        $answer = '';
        foreach ($this->getAttributes() as $attributeKey => $attributeValue) {
            $answer .= ' ' . htmlspecialchars($attributeKey) . '="' . htmlspecialchars($attributeValue) . '"';
        }

        return $answer;
    }

    /**
     * Get a value from posted form values array
     *
     * @param array $values All posted form values.
     * @param string $valueKey This field name.
     * @return string
     */
    public function getValueAsString($values, $valueKey)
    {
        if (isset($values[$valueKey])) {
            return $values[$valueKey];
        } else {
            return '';
        }
    }

    /**
     * Get validators
     *
     * @return \Ip\Form\Validator[]
     */
    public function getValidators()
    {
        return $this->validators;
    }

    /**
     * Check if the field is required
     *
     * @return bool
     */
    public function isRequired()
    {
        $validators = $this->getValidators();
        foreach ($validators as $validator) {
            if (get_class($validator) == 'Ip\Form\Validator\Required') {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if field passes validation
     *
     * @param string $values
     * @param string $valueKey
     * @param string $environment \Ip\Form::ENVIRONMENT_ADMIN or \Ip\Form::ENVIRONMENT_PUBLIC
     * @return bool
     */
    public function validate($values, $valueKey, $environment)
    {
        $validators = $this->getValidators();
        foreach ($validators as $validator) {
            $error = $validator->getError($values, $valueKey, $environment);
            if ($error) {
                return $error;
            }
        }

        return false;
    }

    /**
     * Add a validator to a field
     *
     * Available validators are located at Ip/Form/Field/Validator folder.
     * E.g., to add required field validator use $field->addValidator('Required') method.
     * @param $validator
     * @throws \Ip\Exception
     */
    public function addValidator($validator)
    {
        if (!is_array($validator)) {
            $validator = array($validator);
        }

        if (empty($validator)) {
            throw new \Ip\Exception('Empty validator');
        }

        if (is_string($validator[0])) {
            if (preg_match('/^[a-z0-9]+$/i', $validator[0])) {
                $validatorClass = '\\Ip\\Form\\Validator\\' . $validator[0];
            } else {
                $validatorClass = $validator[0];
            }
            if (count($validator) >= 3) {
                $validatorObject = new $validatorClass($validator[1], $validator[2]);
            } elseif (count($validator) == 2) {
                $validatorObject = new $validatorClass($validator[1]);
            } elseif (count($validator) == 1) {
                $validatorObject = new $validatorClass();
            } else {
                throw new \Ip\Exception('Incorrect validator');
            }

        } else {
            $validatorObject = $validator[0];
        }

        $this->validators[] = $validatorObject;
    }

    /**
     * Remove field validator
     *
     * @param $validator
     */
    public function removeValidator($validator)
    {
        $validatorClass = 'Modules\\developer\\form\\Validator\\' . $validator;
        $newValidatorsArray = array();
        foreach ($this->validators as $validator) {
            if (get_class($validator) != $validatorClass) {
                $newValidatorsArray[] = $validator;
            }
        }
        $this->validators = $newValidatorsArray;
    }

    /**
     * Add HTML attribute to input field
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
     * @param $name
     */
    public function removeAttribute($name)
    {
        unset($this->attributes[$name]);
    }

    /**
     * Get validator HTML attributes
     *
     * Needed for JavaScript validator.
     * @param string $doctype \Ip\View doctype constant.
     * @return string
     */
    public function getValidationAttributesStr($doctype)
    {
        $attributesStr = '';
        foreach ($this->getValidators() as $validator) {
            $tmpArgs = $validator->validatorAttributes();
            if ($tmpArgs != '') {
                $attributesStr .= ' ' . $tmpArgs;
            }
        }

        return $attributesStr;
    }

    /**
     * CSS class that should be applied to surrounding element of this field. By default equal to the class name of the field.
     * This field is used to identify fields by their type. So each extending class should return its own unique and constant string.
     * @return string
     */
    public function getTypeClass()
    {
        $classParts = explode('\\', get_class($this));
        $last = lcfirst(array_pop($classParts));
        return $last;
    }

    /* GETTERS AND SETTERS  */

    /**
     * Get field label
     *
     * @return string Field label
     */

    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set field label
     *
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Get field input hint text
     *
     * @return string Hint
     */
    public function getHint()
    {
        return $this->hint;
    }

    /**
     * Set field input hint text
     *
     * @param string $hint Hint
     */
    public function setHint($hint)
    {
        $this->hint = $hint;
    }

    /**
     * Get field note text
     *
     * @return string Text note.
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set field note text
     *
     * @param string $note Note text.
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * Get field name attribute
     *
     * @return string Field name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * If your input has many input fields
     *
     * Eg. field[id], field[code], ... Return the name of input that should hold error message.
     * @return string
     */
    public function getValidationInputName()
    {
        return $this->name;
    }

    /**
     * Set field name attribute
     *
     * @param string $name Field name.
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get field value
     *
     * @return mixed Field value.
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set field value
     *
     * @param string $value Field value.
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Set field layout. Use constants \Ip\Form\Field::LAYOUT_DEFAULT, \Ip\Form\Field::LAYOUT_BLANK, \Ip\Form\Field::LAYOUT_NO_LABEL,
     *
     * @param string $layout.
     */
    public function setLayout($layout)
    {
        $this->layout= $layout;
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
     * Get specific HTML attribute of the field
     *
     * @param string $attribute
     * @return string|bool
     */
    public function getAttribute($attribute)
    {
        if (isset($this->attributes[$attribute])) {
            return $this->attributes[$attribute];
        } else {
            return false;
        }
    }

    /**
     * Set extra HTML attributes from associative array
     *
     * Does not affect default class, name, required, type and value attributes.
     * @param array $attributes Associative array with keys as attribute names and values as attribute values.
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Get field "id" HTML attribute
     *
     * @return string HTML "id" attribute value
     */
    public function getId()
    {
        return $this->getAttribute('id');
    }

    /**
     * Add CSS class to form field
     *
     * @param string $cssClass
     */
    public function addClass($cssClass)
    {
        $this->classes[$cssClass] = 1;
    }

    /**
     * Remove CSS class from a form field
     *
     * @param $cssClass
     */
    public function removeClass($cssClass)
    {
        unset($this->classes[$cssClass]);
    }

    /**
     * Get a list of field's HTML classes
     *
     * @return array
     */
    public function getClasses()
    {
        return array_keys($this->classes);
    }

    /**
     * Get class attributes as a string
     *
     * @return string
     */
    public function getClassesStr()
    {
        $answer = '';
        foreach ($this->getClasses() as $class) {
            $answer .= ' ' . $class;
        }

        return 'class="' . $answer . '"';
    }

    /**
     * Set css class
     */
    public function setCssClasses($classes)
    {
        if (!is_array($classes)) {
            $classes = explode(' ', $classes);
        }

        $this->classes = array_flip($classes);
    }

}

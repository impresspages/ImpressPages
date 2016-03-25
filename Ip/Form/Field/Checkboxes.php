<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;

use Ip\Form\Field;


class Checkboxes extends Field
{

    private $values;
    private $stolenId;

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        $this->value = array();
        if (isset($options['values'])) {
            $this->values = $options['values'];
        }
        else {
            $this->values = array();
        }
        parent::__construct($options);
        $this->stolenId = $this->getAttribute('id');
        $this->removeAttribute('id');
    }

    /**
     * Render field
     *
     * @param string $doctype
     * @param $environment
     * @return string
     */
    public function render($doctype, $environment)
    {
        $answer = '';

        foreach ($this->getValues() as $key => $value) {
            if (!is_array($value)) {
                $value = array($value, $value);
            }

            if (in_array($value[0], $this->value)) {
                $checked = ' checked="checked"';
            }
            else {
                $checked = '';
            }

            if ($key == 0) {
                $id = ' id="' . $this->stolenId . '"';
            }
            else {
                $id = '';
            }

            $answer .= '
            <div class="checkbox">
                <label>
                    <input ' . $this->getAttributesStr($doctype) . $id . ' class="' . implode(
                    ' ',
                    $this->getClasses()
                ) . '" name="'.htmlspecialchars(
                    $this->getName().'[]'
                ) . '" type="checkbox" ' . $this->getValidationAttributesStr(
                    $doctype
                ) . $checked . ' value="' . htmlspecialchars($value[0]) . '" />
                    ' . htmlspecialchars($value[1]) . '
                </label>
            </div>
            ';
        }

        return $answer;
    }

    /**
     * Set values
     *
     * @param string $values
     */
    public function setValues($values)
    {
        $this->values = $values;
    }

    /**
     * Get values
     *
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Get class type
     *
     * CSS class that should be applied to surrounding element of this field.
     * By default empty. Extending classes should specify their value.
     * @return string
     */
    public function getTypeClass()
    {
        return 'checkboxes';
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->stolenId;
    }


    /**
     * Set field value
     *
     * @param string $value Field value.
     */
    public function setValue($value)
    {
        if (!is_array($value)) {
            $value = array($value);
        }
        $this->value = $value;
    }

}

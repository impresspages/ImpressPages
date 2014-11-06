<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;


class Select extends \Ip\Form\Field
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
        if (isset($options['values'])) {
            $this->values = $options['values'];
        } else {
            $this->values = array();
        }
        parent::__construct($options);
        $this->stolenId = $this->getAttribute('id');
        $this->removeAttribute(
            'id'
        ); // We need to put id only on the first input. So we will remove it from attributes list. And put it temporary to stolenId.
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
        $options = '';

        foreach ($this->getValues() as $value) {
            if (!is_array($value)) {
                $value = array($value, $value);
            }

            if ($value[0] === $this->value || is_int($value[0]) && (string)$value[0] === $this->value) {
                $selected = ' selected="selected"';
            } else {
                $selected = '';
            }

            $options .= '<option' . $selected . ' value="' . htmlspecialchars($value[0]) . '">' . htmlspecialchars(
                    $value[1]
                ) . '</option>' . "\n";
        }
        $answer =
            '
            <select ' . $this->getAttributesStr($doctype) . ' id="' . $this->stolenId . '" name="' . htmlspecialchars(
                $this->getName()
            ) . '" class="form-control ' . implode(' ', $this->getClasses()) . '" ' . $this->getValidationAttributesStr(
                $doctype
            ) . ' >
' . $options . '
</select>
';
        return $answer;
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
     * Set values
     *
     * @param string $values
     */
    public function setValues($values)
    {
        $this->values = $values;
    }

    /**
     * Get validation attributes
     *
     * HTML5 spec: The first child option element of a select element with a required attribute and without a multiple attribute, and whose size is 1, must have either an empty value attribute, or must have no text content.
     * @see Ip\Form\Field.Field::getValidationAttributesStr()
     * @param string $doctype
     * @return string
     */
    public function getValidationAttributesStr($doctype)
    {
        $attributesStr = '';
        $values = $this->getValues();
        if (!isset($values[0])) {
            return parent::getValidationAttributesStr($doctype);
        }

        $firstValue = $values[0];
        if (is_array($firstValue)) {
            $key = $firstValue[0];
            $value = $firstValue[1];
        } else {
            $key = $firstValue;
            $value = $firstValue;
        }

        $html5Important = ($doctype == \Ip\Response\Layout::DOCTYPE_HTML5 && $this->getAttribute(
                'size'
            ) <= 1 && $this->getAttribute('multiple') === false && ($key != '' && $value != ''));

        if (!$html5Important) {
            return parent::getValidationAttributesStr($doctype);
        }

        foreach ($this->getValidators() as $validator) {
            if (get_class($validator) == 'Ip\Form\Validator\Required') {
                continue;
            }
            $tmpArgs = $validator->validatorAttributes();
            if ($tmpArgs != '') {
                $attributesStr .= ' ' . $tmpArgs;
            }
        }

        return $attributesStr;
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

}

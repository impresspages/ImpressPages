<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;

use Ip\Form\Field;


class Checkbox extends Field
{

    protected $checked = false;
    protected $text = null;
    protected $postValue = null;

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        if (isset($options['checked'])) {
            $this->checked = $options['checked'];
        } else {
            if (isset($options['value']) && ($options['value'] === true || $options['value'] === 1)) {
                $this->checked = true;
            } else {
                $this->checked = false;
            }
        }
        if (isset($options['text']) && $options['text']) {
            $this->setText($options['text']);
        }
        if (isset($options['options']) && isset($options['options']['text']) && $options['options']['text']) {
            $this->setText($options['options']['text']);
        }
        if (isset($options['value']) && $options['value']) {
            $this->setChecked(1);
        }

        if (isset($options['postValue']) && $options['postValue']) {
            $this->setPostValue($options['postValue']);
        }

        parent::__construct($options);
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
        if ($this->getChecked()) {
            $attributes = ' checked="checked"';
        } else {
            $attributes = '';
        }

        if ($this->getPostValue() !== null) {
            $value = ' value="' . htmlspecialchars($this->getPostValue(), ENT_QUOTES) . '"';
        } else {
            $value = '';
        }

        $answer .= '
        <div class="checkbox">
            <label>
                <input ' . $this->getAttributesStr($doctype) . ' class="' . implode(
                ' ',
                $this->getClasses()
            ) . '" name="' . htmlspecialchars(
                $this->getName()
            ) . '" type="checkbox"' . $attributes . ' ' . $this->getValidationAttributesStr($doctype) . $value . ' />
                ' . $this->getText() . '
            </label>
        </div>
        ';

        return $answer;
    }

    /**
     * Set checked option
     *
     * @param int| $checked
     */
    public function setChecked($checked)
    {
        $this->checked = $checked;
    }

    /**
     * Get checked option
     *
     * @return bool
     */
    public function getChecked()
    {
        return $this->checked;
    }

    /**
     * Set post value
     *
     * @param string $postValue
     */
    public function setPostValue($postValue)
    {
        $this->postValue = $postValue;
    }

    /**
     * Get post value
     *
     * @return string
     */
    public function getPostValue()
    {
        return $this->postValue;
    }

    /**
     * Set text
     *
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Get value as string
     *
     * @param string $values
     * @param string $valueKey
     * @return string
     */
    public function getValueAsString($values, $valueKey)
    {
        if ($this->isChecked($values, $valueKey)) {
            return 'Yes';
        } else {
            return 'No';
        }
    }

    /**
     * Is checked?
     *
     * @param string $values
     * @param string $valueKey
     * @return int
     */
    public function isChecked($values, $valueKey)
    {
        if (isset($values[$valueKey]) && $values[$valueKey]) {
            return 1;
        } else {
            return 0;
        }
    }



    /**
     * Set value
     *
     * @param string $value
     */
    public function setValue($value)
    {
        if ($value) {
            $this->setChecked(1);
        }
        parent::setValue($value);
    }

}

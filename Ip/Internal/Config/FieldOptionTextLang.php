<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Config;


use Ip\Form\Field;

class FieldOptionTextLang extends Field
{

    protected $optionName = null;

    public function __construct($options = [])
    {
        if (!empty($options['optionName'])) {
            $this->optionName = $options['optionName'];
        }

        parent::__construct($options);
    }

    public function setOptionName($optionName)
    {
        $this->optionName = $optionName;
    }

    public function getOptionName()
    {
        return $this->optionName;
    }


    public function render($doctype, $environment)
    {
        $languages = ipContent()->getLanguages();
        $answer = '';
        foreach ($languages as $language) {
            $value = ipGetOptionLang($this->getOptionName(), $language->getCode(), $this->getValue());
            $fieldId = $this->getName() . '_' . $language->getId() . '';
            $answer .= '
<div class="input-group">
  <span class="input-group-addon">' . esc($language->getAbbreviation()) . '</span>
  <input
  data-fieldname="' . $this->getName() . '"
  data-fieldid="' . $fieldId . '"
  id="' . $fieldId . '"
  ' . $this->getAttributesStr($doctype) . ' data-languageid="' . $language->getId() . '" class="ips' . $this->getName(
                ) . ' form-control ' . implode(' ', $this->getClasses()) . '" name="' . esc(
                    $fieldId,
                    'attr'
                ) . '" ' . $this->getValidationAttributesStr($doctype) . ' type="optionTextLang" value="' . esc(
                    $value,
                    'attr'
                ) . '" />
</div>
            ';
        }
        return $answer;
    }

    /**
     * CSS class that should be applied to surrounding element of this field. By default empty. Extending classes should specify their value.
     */
    public function getTypeClass()
    {
        return 'multipleText';
    }

}

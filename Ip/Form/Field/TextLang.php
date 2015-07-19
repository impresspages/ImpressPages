<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;



class TextLang extends \Ip\Form\FieldLang
{

    public function __construct($options = array())
    {

        parent::__construct($options);
    }


    public function render($doctype, $environment)
    {
        $languages = ipContent()->getLanguages();
        $answer = '';
        foreach ($languages as $language) {
            $langValue = '';
            $fieldValue = $this->getValue();
            if (is_array($fieldValue)) {
                if (!empty($fieldValue[$language->getCode()])) {
                    $langValue = $fieldValue[$language->getCode()];
                }
            }
            if (!is_string($langValue)) {
                //just in case we have an array or something else incompatible with below code in the database
                $langValue = '';
            }

            $answer .= '
<div class="input-group">
  <span class="input-group-addon">' . esc($language->getAbbreviation()) . '</span>
  <input ' . $this->getAttributesStr($doctype) . ' class="form-control ' . implode(
                    ' ',
                    $this->getClasses()
                ) . '" name="' . escAttr($this->getName() . '[' . $language->getCode() . ']" ') . $this->getValidationAttributesStr(
                    $doctype
                ) . ' type="text" value="' . escAttr($langValue) . '" />
</div>
            ';
        }
        return $answer;
    }



}

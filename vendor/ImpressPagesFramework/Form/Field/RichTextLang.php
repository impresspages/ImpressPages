<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;


class RichTextLang extends \Ip\Form\FieldLang
{

    /**
     * Render field
     *
     * @param string $doctype
     * @param $environment
     * @return string
     */
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

<textarea ' . $this->getAttributesStr($doctype) . ' class="form-control ' . implode(
                    ' ',
                    $this->getClasses()
                ) . '" name="' . escattr($this->getName()) . '[' . $language->getCode() . ']" ' . $this->getValidationAttributesStr(
                    $doctype
                ) . ' >' . escTextarea($langValue) . '</textarea>


</div>
            ';
        }
        return $answer;
        /*return '<textarea ' . $this->getAttributesStr($doctype) . ' class="form-control ' . implode(
            ' ',
            $this->getClasses()
        ) . '" name="' . esc($this->getName(), 'attr') . '" ' . $this->getValidationAttributesStr(
            $doctype
        ) . ' >' . esc($this->getValue(), 'textarea') . '</textarea>';*/
    }

}

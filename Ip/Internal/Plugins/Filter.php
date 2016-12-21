<?php


namespace Ip\Internal\Plugins;


class Filter
{
    /**
     * @param $form
     * @param $info ['pluginName']
     */
    public static function ipPluginPropertiesForm_20($form, $info)
    {
        return Helper::pluginPropertiesFormFields($info['pluginName'], $form);
    }

    /**
     * @param array $data
     * @param $info ['pluginName']
     */
    public static function ipPluginSaveOptions_20($data, $info)
    {
        $plugin = Helper::getPluginData($info['pluginName']);

        if (empty($plugin['options'])) {
            return $data;
        }

        $form = new \Ip\Form();

        /* @var $form \Ip\Form */
        $form = Helper::pluginPropertiesFormFields($info['pluginName'], $form);

        foreach ($plugin['options'] as $option) {
            if (empty($option['type'])) {
                $option['type'] = 'text';
            }

            if (empty($option['name'])) {
                continue;
            }

            $field = $form->getField($option['name']);
            if (!$field) {
                continue;
            }

            $optionName = $info['pluginName'] . '.' . $option['name'];

            if ($field instanceof \Ip\Form\FieldLang) {
                //multilingual field
                $value = '';
                if (!empty($data[$option['name']])) {
                    $value = $data[$option['name']];
                }
                if (!is_array($value)) {
                    $value = [];
                }
                foreach($value as $languageKey => $langValue) {
                    if (!is_string($languageKey)) {
                        continue;
                    }
                    if (!is_string($langValue)) {
                        continue;
                    }
                    ipSetOptionLang($optionName, $langValue, $languageKey);
                }

            } else {
                //standard field
                if (method_exists($field, 'isChecked')) {
                    //checkbox uniqueness
                    $value = $field->isChecked($data, $option['name']);
                } else {
                    $value = $field->getValueAsString($data, $option['name']);
                }

                ipSetOption($optionName, $value);
            }

            unset($data[$option['name']]); // this option is processed
        }

        return $data;
    }

}

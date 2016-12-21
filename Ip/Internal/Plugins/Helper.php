<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Plugins;


use \Ip\Form as Form;

class Helper
{


    /**
     * Clean comments of json content and decode it with json_decode().
     * Work like the original php json_decode() function with the same params
     *
     * @param   string $json The json string being decoded
     * @param   bool $assoc When TRUE, returned objects will be converted into associative arrays.
     * @param   integer $depth User specified recursion depth. (>=5.3)
     * @param   integer $options Bitmask of JSON decode options. (>=5.4)
     * @return  string
     */
    public static function jsonCleanDecode($json, $assoc = false, $depth = 512, $options = 0)
    {

        // search and remove comments like /* */ and //
        $json = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t](//).*)#", '', $json);

        $json = json_decode($json, $assoc, $depth, $options);

        return $json;
    }

    public static function removeDir($dir, $depth = 0)
    {

        if (!file_exists($dir)) {
            //already removed
            return true;
        }

        $dir = self::removeTrailingSlash($dir);

        if (!is_writable($dir)) {
            return false;
        }

        if (is_dir($dir)) {
            if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file == ".." || $file == ".") {
                        continue;
                    }

                    $result = self::removeDir($dir . '/' . $file, $depth + 1);
                    if (!$result) {
                        return false;
                    }
                }
                closedir($handle);
            }

            $result = rmdir($dir);
            return $result;
        } else {
            $result = unlink($dir);
            return $result;
        }
    }

    private static function removeTrailingSlash($path)
    {
        return preg_replace('{/$}', '', $path);
    }


    public static function pluginPropertiesForm($pluginName)
    {

        $form = new \Ip\Form();
        $form->setEnvironment(\Ip\Form::ENVIRONMENT_ADMIN);

        $field = new \Ip\Form\Field\Hidden(
            array(
                'name' => 'aa',
                'value' => 'Plugins.updatePlugin'
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Hidden(
            array(
                'name' => 'pluginName',
                'value' => $pluginName
            ));
        $form->addField($field);

        $initialFieldCount = count($form->getFields());

        $form = ipFilter('ipPluginPropertiesForm', $form, array('pluginName' => $pluginName));

        if (count($form->getFields()) == $initialFieldCount) {
            return null;
        }

        $field = new \Ip\Form\Field\Submit(array(
            'value' => __('Save', 'Ip-admin'),
        ));

        $field->addClass('ipsSave');
        $form->addField($field);

        return $form;
    }

    /**
     * @param string $pluginName
     * @param \Ip\Form $form
     * @return \Ip\Form $form
     */
    public static function pluginPropertiesFormFields($pluginName, $form)
    {
        $plugin = self::getPluginData($pluginName);

        if (!empty($plugin['options'])) {
            static::getOptionsForm($pluginName, $form, $plugin['options']);
        }

        return $form;
    }

    public static function savePluginOptions($pluginName, $data)
    {
        $form = self::pluginPropertiesForm($pluginName);

        $errors = $form->validate($data);

        if ($errors) {
            return $errors;
        }

        ipFilter('ipPluginSaveOptions', $data, array('pluginName' => $pluginName)); //for internal use only. Don't use in your plugins as it is going to change

        return true;
    }

    /**
     * @param string $pluginName
     * @param \Ip\Form $form
     * @param array $options
     */
    public static function getOptionsForm($pluginName, $form, $options)
    {
        foreach ($options as $option) {
            if (empty($option['type'])) {
                $option['type'] = 'text';
            }


            if (in_array($option['type'], array('select', 'text', 'textarea', 'richText', 'color', 'range', 'checkbox', 'password'))) {
                $option['type'] = ucfirst($option['type']);
            }

            $className = $option['type'];
            if (class_exists($className)) {
                $newField = new $className($option);
            } else {
                $className = 'Ip\\Form\\Field\\' . $option['type'];
                if (class_exists($className)) {
                    $newField = new $className($option);
                }
            }

            if (!isset($newField)) {
                //field type is not recognised
                continue;
            }

            $default = isset($option['default']) ? $option['default'] : null;

            if (!empty($option['name'])) {
                $newField->setName($option['name']);
                $optionKey = "{$pluginName}.{$option['name']}";
                if ($newField instanceof \Ip\Form\FieldLang) {
                    $value = [];
                    foreach(ipContent()->getLanguages() as $language) {
                        $value[$language->getCode()] = ipGetOptionLang($optionKey, $language->getCode(), $default);
                    }
                    $newField->setValue($value);
                } else {
                    $newField->setValue(ipGetOption($optionKey, $default));
                }

            }
            $newField->setLabel(empty($option['label']) ? '' : $option['label']);
            if (!empty($option['note'])) {
                $newField->setNote($option['note']);
            }

            if (!empty($option['validators'])) {
                $newField->addValidator($option['validators']);
            }


            $form->addfield($newField);
        }
    }

    public static function getPluginData($pluginName)
    {
        $activePlugins = Service::getActivePluginNames();
        $config = Model::getPluginConfig($pluginName);
        $pluginRecord = array(
            'description' => '',
            'title' => $pluginName,
            'name' => $pluginName,
            'version' => '',
            'author' => '',
            'labelType' => 'default', // Bootstrap class
            'label' => __('Inactive', 'Ip-admin'),
            'active' => false
        );
        if (in_array($pluginName, $activePlugins)) {
            $pluginRecord['active'] = true;
            $pluginRecord['labelType'] = 'success'; // Bootstrap class
            $pluginRecord['label'] = __('Active', 'Ip-admin');
        }
        if (isset($config['description'])) {
            $pluginRecord['description'] = $config['description'];
        }
        if (isset($config['version'])) {
            $pluginRecord['version'] = $config['version'];
        }
        if (isset($config['title'])) {
            $pluginRecord['title'] = $config['title'];
        }
        if (isset($config['author'])) {
            $pluginRecord['author'] = $config['author'];
        }
        if (isset($config['name'])) {
            $pluginRecord['name'] = $config['name'];
        }

        if (isset($config['options'])) {
            $pluginRecord['options'] = $config['options'];
        }

        return $pluginRecord;
    }

}

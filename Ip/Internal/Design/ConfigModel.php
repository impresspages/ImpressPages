<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Design;


use \Ip\Form as Form;

class ConfigModel
{

    protected $isInPreviewState;

    protected function __construct()
    {
        $this->isInPreviewState = defined('IP_ALLOW_PUBLIC_THEME_CONFIG') || ipRequest()->getRequest(
                'ipDesignPreview'
            ) && $this->hasPermission();
    }

    /**
     * @return ConfigModel
     */
    public static function instance()
    {
        return new ConfigModel();
    }

    public function isInPreviewState()
    {
        return $this->isInPreviewState;
    }

    /**
     * @todo optimize
     * @param string $themeName
     * @param string $name option name
     * @param null $default you can override theme.json default value here
     * @return mixed
     */
    public function getConfigValue($themeName, $name, $default = null)
    {
        $data = ipRequest()->getRequest();


        if (isset($data['restoreDefault'])) {
            //overwrite current config with default theme values
            $model = Model::instance();
            $theme = $model->getTheme(ipConfig()->theme());
            $options = $theme->getOptionsAsArray();
            foreach ($options as $option) {
                if (isset($option['name']) && $option['name'] == $name && isset($option['default'])) {
                    return $option['default'];
                }
            }
            return '';
        } else {
            $config = $this->getLiveConfig();
            if (isset($config[$name])) {
                return $config[$name];
            }
            if (isset($data['refreshPreview'])) {
                return '';
            }

        }

        $result = ipThemeStorage($themeName)->get($name);

        if ($result !== null) {
            return $result;
        }

        if ($default === null) {
            $model = Model::instance();
            $theme = $model->getTheme($themeName);
            $options = $theme->getOptionsAsArray();
            foreach ($options as $option) {
                if (!empty($option['name']) && $option['name'] == $name && isset($option['name']) && isset($option['default'])) {
                    return $option['default'];
                }
            }
        }

        return $default;
    }

    public function getAllConfigValues($theme)
    {
        $data = ipRequest()->getRequest();

        if (isset($data['restoreDefault'])) {
            $config = [];
            //overwrite current config with default theme values
            $model = Model::instance();
            $theme = $model->getTheme(ipConfig()->theme());
            $options = $theme->getOptionsAsArray();
            foreach ($options as $option) {
                if (isset($option['name']) && isset($option['default'])) {
                    $config[$option['name']] = $option['default'];
                }
            }
            return $config;
        } else {
            $config = $this->getLiveConfig();
            if (!empty($config)) {
                return $config;
            }

        }

        return ipThemeStorage($theme)->getAll();
    }

    public function setConfigValue($theme, $name, $value)
    {
        ipThemeStorage($theme)->set($name, $value);
    }

    /**
     * @param string $name
     * @return \Ip\Form
     * @throws \Ip\Exception
     */
    public function getThemeConfigForm($name)
    {
        $model = Model::instance();
        $theme = $model->getTheme($name);
        if (!$theme) {
            throw new \Ip\Exception("Theme doesn't exist");
        }


        $form = new \Ip\Form();
        $form->setEnvironment(\Ip\Form::ENVIRONMENT_ADMIN);
        $form->addClass('ipsForm');


        $options = $theme->getOptions();

        $generalFieldset = $this->getFieldset($name, $options);
        $generalFieldset->setLabel(__('General options', 'Ip-admin'));
        if (count($generalFieldset->getFields())) {
            $form->addFieldset($generalFieldset);
        }


        foreach ($options as $option) {
            if (empty($option['type']) || empty($option['options'])) {
                continue;
            }
            if ($option['type'] != 'group') {
                continue;
            }

            $fieldset = $this->getFieldset($name, $option['options']);
            if (!empty($option['label'])) {
                $fieldset->setLabel($option['label']);
            }
            $form->addFieldset($fieldset);
        }

        $form->addFieldset(new \Ip\Form\Fieldset());
        $field = new Form\Field\Hidden();
        $field->setName('aa');
        $field->setValue('Design.updateConfig');
        $form->addField($field);

        return $form;
    }


    /**
     * @param $options
     * @return Form\Fieldset
     */
    protected function getFieldset($themeName, $options)
    {
        $fieldset = new \Ip\Form\Fieldset();

        foreach ($options as $option) {
            if (empty($option['type']) || empty($option['name'])) {
                continue;
            }
            switch ($option['type']) {
                case 'select':
                case 'Select':
                    $newField = new Form\Field\Select();
                    $values = [];
                    if (!empty($option['values']) && is_array($option['values'])) {
                        foreach ($option['values'] as $value) {
                            $values[] = array($value, $value);
                        }
                    }
                    $newField->setValues($values);
                    break;
                case 'text':
                case 'Text':
                    $newField = new Form\Field\Text();
                    break;
                case 'textarea':
                case 'Textarea':
                    $newField = new Form\Field\Textarea();
                    break;
                case 'color':
                case 'Color':
                    $newField = new Form\Field\Color();
                    break;
                case 'range':
                case 'Range':
                    $newField = new Form\Field\Range();
                    break;
                case 'checkbox':
                case 'Checkbox':
                    $newField = new Form\Field\Checkbox();
                    break;
                default:
                    $class = 'Ip\\Form\\Field\\' . $option['type'];
                    if (!class_exists($class)) {
                        $class = $option['type'];
                    }

                    if (class_exists($class)) {
                        $newField = new $class();
                        if ($option['type'] == 'RepositoryFile') {
                            $newField->setFileLimit(1);
                        }
                        if (method_exists($newField, 'setValues') && isset($option['values'])) {
                            $newField->setValues($option['values']);
                        }
                    } else {
                        $newField = new Form\Field\Text();
                    }
            }
            if (!isset($newField)) {
                //field type is not recognised
                continue;
            }

            if (!empty($option['note'])) {
                $newField->setNote($option['note']);
            }
            $newField->setName($option['name']);
            $newField->setLabel(empty($option['label']) ? '' : $option['label']);
            $default = isset($option['default']) ? $option['default'] : null;
            $newField->setValue($this->getConfigValue($themeName, $option['name'], $default));

            $fieldset->addfield($newField);
        }
        return $fieldset;
    }

    protected function getLiveConfig()
    {
        $data = ipRequest()->getRequest();
        if ($this->isInPreviewState() && isset($data['ipDesign']['pCfg'])) {
            return $data['ipDesign']['pCfg'];
        }

        if (isset($data['aa']) && $data['aa'] == 'Design.updateConfig') {
            unset($data['aa']);

            foreach($data as &$item) {
                if (is_array($item)) {
                    if (isset($item[0])) { //to support RepositoryFile
                        $item = $item[0];
                    } else {
                        $item = '';
                    }
                }
            }

            return $data;
        }

    }


    protected function hasPermission()
    {

        if (!\Ip\Internal\Admin\Backend::loggedIn()) {
            return false;
        }


        return true;
    }


}

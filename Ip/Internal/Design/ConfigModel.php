<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Design;


use \Ip\Form as Form;

class ConfigModel{

    protected $isInPreviewState;

    protected function __construct()
    {
        $this->isInPreviewState = defined('IP_ALLOW_PUBLIC_THEME_CONFIG') || isset($_REQUEST['ipDesignPreview']) && $this->hasPermission();
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
        $config = $this->getLiveConfig();
        if (isset($config[$name])) {

            if (isset($data['restoreDefault'])) {
                //overwrite current config with default theme values
                $model = Model::instance();
                $theme = $model->getTheme(ipConfig()->theme());
                $options = $theme->getOptionsAsArray();
                foreach($options as $option) {
                    if (isset($option['name']) && $option['name'] == $name && isset($option['default'])) {
                        return $option['default'];
                    }
                }
            }

            return $config[$name];
        }

        $dbh = ipDb()->getConnection();
        $sql = '
            SELECT
                value
            FROM
                ' . ipTable('design') . '
            WHERE
                `theme` = :theme AND
                `name` = :name
        ';

        $params = array (
            ':theme' => $themeName,
            ':name' => $name
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);
        $result = $q->fetch(\PDO::FETCH_ASSOC);
        if ($result) {
            return $result['value'];
        }

        if ($default === null) {
            $model = Model::instance();
            $theme = $model->getTheme($themeName);
            $options = $theme->getOptionsAsArray();
            foreach($options as $option) {
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
        $config = $this->getLiveConfig();
        if (!empty($config)) {
            if (isset($data['restoreDefault'])) {
                //overwrite current config with default theme values
                $model = Model::instance();
                $theme = $model->getTheme(ipConfig()->theme());
                $options = $theme->getOptionsAsArray();
                foreach($options as $option) {
                    if (isset($option['name']) && isset($option['default'])) {
                        $config[$option['name']] = $option['default'];
                    }
                }
            }
            return $config;
        }

        $dbh = ipDb()->getConnection();
        $sql = '
            SELECT
                `name`, `value`
            FROM
                ' . ipTable('design') . '
            WHERE
                `theme` = :theme
        ';

        $params = array (
            ':theme' => $theme,
        );

        $q = $dbh->prepare($sql);
        $q->execute($params);
        $rs = $q->fetchAll(\PDO::FETCH_ASSOC);

        $config = array();
        foreach ($rs as $row) {
            $config[$row['name']] = $row['value'];
        }


        return $config;
    }

    public function setConfigValue($theme, $name, $value)
    {
        $dbh = ipDb()->getConnection();
        $sql = '
            INSERT INTO
                ' . ipTable('design') . '
            SET
                `theme` = :theme,
                `name` = :name,
                `value` = :value
            ON DUPLICATE KEY UPDATE
                `value` = :value
        ';

        $params = array (
            ':theme' => $theme,
            ':name' => $name,
            ':value' => $value
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);
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
        $form->addClass('ipsForm');



        $options = $theme->getOptions();

        $generalFieldset = $this->getFieldset($name, $options);
        $generalFieldset->setLabel(__('General options', 'ipAdmin'));
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

        foreach($options as $option) {
            if (empty($option['type']) || empty($option['name'])) {
                continue;
            }
            switch ($option['type']) {
                case 'select':
                    $newField = new Form\Field\Select();
                    $values = array();
                    if (!empty($option['values']) && is_array($option['values'])) {
                        foreach($option['values'] as $value) {
                            $values[] = array($value, $value);
                        }
                    }
                    $newField->setValues($values);
                    break;
                case 'text':
                    $newField = new Form\Field\Text();
                    break;
                case 'textarea':
                    $newField = new Form\Field\Textarea();
                    break;
                case 'color':
                    $newField = new Form\Field\Color();
                    break;
                case 'range':
                    $newField = new Form\Field\Range();
                    break;
                case 'check':
                    $newField = new Form\Field\Checkbox();
                    break;
                default:
                    //do nothing
            }
            if (!isset($newField)) {
                //field type is not recognised
                continue;
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
        if ($this->isInPreviewState() && isset($data['ipDesign']['pCfg'])){
            return $data['ipDesign']['pCfg'];
        }

        if (isset($data['aa']) && $data['aa'] == 'Design.updateConfig') {
            unset($data['aa']);
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
<?php
/**
 * @package ImpressPages
 *
 */

namespace Modules\standard\design;


use \Modules\developer\form as Form;

class ConfigModel{


    protected function __construct()
    {

    }

    /**
     * @return Model
     */
    public static function instance()
    {
        return new ConfigModel();
    }

    public function isInPreviewState()
    {
        return isset($_GET['ipDesignPreview']) && $this->hasPermission();
    }


    /**
     * @param string $name
     * @return \Modules\developer\form\Form
     * @throws \Ip\CoreException
     */
    public function getThemeConfigForm($name)
    {
        $model = Model::instance();
        $theme = $model->getTheme($name);
        if (!$theme) {
            throw new \Ip\CoreException("Theme doesn't exist");
        }


        $form = new \Modules\developer\form\Form();

        $options = $theme->getOptions();

        foreach($options as $option) {
            if (empty($option['type']) || empty($option['name'])) {
                continue;
            }
            switch ($option['type']) {

                case 'select':
                    $field = new Form\Field\Select();
                    $values = array();
                    if (!empty($option['values']) && is_array($option['values'])) {
                        foreach($option['values'] as $value) {
                            $values[] = array($value, $value);
                        }
                    }
                    $field->setValues($values);

                    break;
                case 'text':
                    $field = new Form\Field\Text();
                    break;
                case 'file':
                    $field = new Form\Field\File();
                    break;
                default:
                    //do nothing
            }
            if (!isset($field)) {
                //field type is not recognised
                continue;
            }

            $field->setName($option['name']);
            $field->setLabel(empty($option['label']) ? '' : $option['label']);

            $form->addfield($field);
        }

        return $form;
    }


    protected function hasPermission()
    {
        if (!\Ip\Backend::loggedIn()) {
            return false;
        }

        if (!\Ip\Backend::userHasPermission(\Ip\Backend::userId(), 'standard', 'design')) {
            return false;
        }

        return true;
    }


}
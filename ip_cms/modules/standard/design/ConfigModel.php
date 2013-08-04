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
     * @return ConfigModel
     */
    public static function instance()
    {
        return new ConfigModel();
    }

    public function isInPreviewState()
    {
        return isset($_GET['ipDesignPreview']) && $this->hasPermission();
    }

    public function getConfigValue($theme, $name, $default = null)
    {
        $dbh = \Ip\Db::getConnection();
        $sql = '
            SELECT FROM
                `'.DB_PREF.'m_design`
            WHERE
                `theme` = :theme,
                `name` = :name
        ';

        $params = array (
            ':theme' => $theme,
            ':name' => $name
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);
        $result = $q->fetch(\PDO::FETCH_ASSOC);
        if ($result) {
            return $result['value'];
        }
        return $default;
    }

    public function setConfigValue($theme, $name, $value)
    {
        $dbh = \Ip\Db::getConnection();
        $sql = '
            INSERT INTO
                `'.DB_PREF.'m_design`
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
        $form->addClass('ipsForm');

        $field = new Form\Field\Hidden();
        $field->setName('g');
        $field->setDefaultValue('standard');
        $form->addField($field);
        $field = new Form\Field\Hidden();
        $field->setName('m');
        $field->setDefaultValue('design');
        $form->addField($field);
        $field = new Form\Field\Hidden();
        $field->setName('ba');
        $field->setDefaultValue('updateConfig');
        $form->addField($field);


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

        $submit = new Form\Field\Submit();
        $submit->setDefaultValue('{{Save}}');

        $form->addField($submit);

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
<?php
/**
 * @package ImpressPages

 *
 */
namespace Modules\standard\design;


class System{


    public function init()
    {

        $configModel = ConfigModel::instance();
        if ($configModel->isInPreviewState()) {
            $this->initConfig();
        }


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

    protected function initConfig()
    {
        $site = \Ip\ServiceLocator::getSite();
        $site->addCss(BASE_URL.LIBRARY_DIR.'css/bootstrap/bootstrap.css');
        $site->addJavascript(BASE_URL.LIBRARY_DIR.'css/bootstrap/bootstrap.js');
        $site->addCss(BASE_URL.LIBRARY_DIR.'fonts/font-awesome/font-awesome.css');
        $site->addJavascript(BASE_URL.MODULE_DIR.'standard/design/public/optionBox.js');
        $site->addJavascriptVariable('ipModuleDesignConfiguration', $this->getConfigurationBoxHtml());
        $site->addCss(BASE_URL.MODULE_DIR.'standard/design/public/optionBox.css');
        if (file_exists(BASE_DIR.THEME_DIR.THEME.'/'.Model::INSTALL_DIR.'Options.js')) {
            $site->addJavascript(BASE_URL.THEME_DIR.THEME.'/'.Model::INSTALL_DIR.'Options.js');
        }

        $model = Model::instance();
        $theme = $model->getTheme(THEME);
        if (!$theme) {
            throw new \Ip\CoreException("Theme doesn't exist");
        }

        $options = $theme->getOptions();

        $fieldNames = array();
        foreach($options as $option) {
            if (empty($option['name'])) {
                continue;
            }
            $fieldNames[] = $option['name'];
        }
        $site->addJavascriptVariable('ipModuleDesignOptionNames', $fieldNames);
    }

    protected function getConfigurationBoxHtml()
    {
        $configModel = ConfigModel::instance();

        $form = $configModel->getThemeConfigForm(THEME);
        $form->removeClass('ipModuleForm');
        $variables = array(
            'form' => $form
        );
        $configBox = \Ip\View::create('view/configBox.php', $variables);
        return $configBox->render();
    }

}



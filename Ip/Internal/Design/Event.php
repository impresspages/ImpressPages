<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Internal\Design;


class Event
{
    public static function ipInit()
    {
        $configModel = ConfigModel::instance();
        if ($configModel->isInPreviewState()) {
            static::initConfig();
        }

        $lessCompiler = LessCompiler::instance();
        if (ipConfig()->isDevelopmentEnvironment()) {
            if ($lessCompiler->shouldRebuild(ipConfig()->theme())) {
                $lessCompiler->rebuild(ipConfig()->theme());
            }
        }
    }

    /*
     * Loading in Preview mode
     */
    protected static function initConfig()
    {
        ipAddCss('Ip/Internal/Core/assets/admin/admin.css');
        ipAddJs('Ip/Internal/Core/assets/admin/bootstrap.js');
        ipAddJs('Ip/Internal/Core/assets/js/jquery-ui/jquery-ui.js');
        ipAddJs('Ip/Internal/Design/assets/optionsBox.js');
        ipAddJsVariable('ipModuleDesignConfiguration', static::getConfigurationBoxHtml());

        if (file_exists(ipThemeFile(Model::INSTALL_DIR.'Options.js'))) {
            ipAddJs(ipThemeUrl(Model::INSTALL_DIR . 'Options.js'));
        } elseif (file_exists(ipThemeFile(Model::INSTALL_DIR.'options.js'))) {
            ipAddJs(ipThemeUrl(Model::INSTALL_DIR . 'options.js'));
        }

        $model = Model::instance();
        $theme = $model->getTheme(ipConfig()->theme());
        if (!$theme) {
            throw new \Ip\Exception("Theme doesn't exist");
        }

        $options = $theme->getOptionsAsArray();

        $fieldNames = array();
        foreach($options as $option) {
            if (empty($option['name'])) {
                continue;
            }
            $fieldNames[] = $option['name'];
        }
        ipAddJsVariable('ipModuleDesignOptionNames', $fieldNames);
    }

    protected static function getConfigurationBoxHtml()
    {
        $configModel = ConfigModel::instance();

        $form = $configModel->getThemeConfigForm(ipConfig()->theme());
        $form->removeClass('ipModuleForm');
        $variables = array(
            'form' => $form
        );
        $optionsBox = ipView('view/optionsBox.php', $variables);
        return $optionsBox->render();
    }

    public static function ipCacheClear($info)
    {
        $lessCompiler = LessCompiler::instance();
        $lessCompiler->rebuild(ipConfig()->theme());
    }

}

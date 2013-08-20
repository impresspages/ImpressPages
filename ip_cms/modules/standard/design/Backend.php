<?php
/**
 * @package ImpressPages
 *
 */

namespace Modules\standard\design;




use Ip\ServiceLocator;

class Backend extends \Ip\Controller
{


    public function index()
    {
        $site = \Ip\ServiceLocator::getSite();

        $site->addCss(BASE_URL.LIBRARY_DIR.'css/bootstrap/bootstrap.css');
        $site->addJavascript(BASE_URL.LIBRARY_DIR.'css/bootstrap/bootstrap.js');
        $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery-ui/jquery-ui.js');
        $site->addCss(BASE_URL.LIBRARY_DIR.'js/jquery-ui/jquery-ui.css');
        $site->addCss(BASE_URL.LIBRARY_DIR.'fonts/font-awesome/font-awesome.css');
        $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/easyXDM/easyXDM.min.js');
        $site->addJavascript(BASE_URL.MODULE_DIR.'standard/design/public/options.js');
        $site->addJavascript(BASE_URL.MODULE_DIR.'standard/design/public/market.js');
        $site->addJavascript(BASE_URL.MODULE_DIR.'standard/design/public/design.js');
        $site->addCss(BASE_URL.MODULE_DIR.'standard/design/public/design.css');
        $site->addJavascript(BASE_URL.MODULE_DIR.'administrator/system/public/market.js');

        $model = Model::instance();

        $themes = $model->getAvailableThemes();

        $model = Model::instance();
        $theme = $model->getTheme(THEME);
        $options = $theme->getOptions();



        $data = array(
            'theme' => $model->getTheme(THEME),
            'availableThemes' => $themes,
            'marketUrl' => $model->getMarketUrl(),
            'showConfiguration' => !empty($options)
        );

        $contentView = \Ip\View::create('view/index.php', $data);
        $layout = $this->createAdminView($contentView);
        $site->setOutput($layout->render());
    }

    public function downloadThemes()
    {
        $parametersMod = \Ip\ServiceLocator::getParametersMod();
        $site = ServiceLocator::getSite();

        $request = ServiceLocator::getRequest();
        $request->mustBePost();

        $themes = $request->getPost('themes');

        if(!is_writable(BASE_DIR.THEME_DIR)){
            header('HTTP/1.1 500 '.BASE_DIR . THEME_DIR.' '. $parametersMod->getValue('standard', 'design', 'admin_translations', 'theme_write_error'));
            $site->setOutput('');
            return;
        }


        try {

            if (!is_array($themes)) {
                throw new \Ip\CoreException('Download failed: invalid parameters.');
            }

            if (function_exists('set_time_limit')) {
                set_time_limit(count($themes) * 180 + 30);
            }

            $themeDownloader = new ThemeDownloader();

            foreach ($themes as $theme) {
                if (!empty($theme['url']) && !empty($theme['name']) && !empty($theme['signature'])) {
                    $themeDownloader->downloadTheme($theme['name'], $theme['url'], $theme['signature']);
                }
            }

            $this->returnJson(true);

        } catch (\Ip\CoreException $e) {
            header('HTTP/1.1 500 ' . $e->getMessage());
            $site->setOutput('');
        } catch (\Exception $e) {
            header('HTTP/1.1 500 ' . $e->getMessage());
            $site->setOutput('');
        }
    }

    /**
     * @throws \Ip\CoreException
     */
    public function installTheme()
    {
        $request = ServiceLocator::getRequest();
        $request->mustBePost();

        $themeName = $request->getPost('themeName');
        if (empty($themeName)) {
            throw new \Ip\CoreException('Invalid arguments.');
        }

        $model = Model::instance();
        if (!$model->getTheme($themeName)) {
            throw new \Ip\CoreException("Theme '{$themeName}' does not exist.");
        }

        try {
            $model->installTheme($themeName);
        } catch (\Ip\CoreException $e) {
            $this->returnJson(array('status' => 'error', 'error' => $e->getMessage()));
            return;
        }

        $this->returnJson(array('status' => 'success'));
    }

    public function updateConfig()
    {
        $request = \Ip\ServiceLocator::getRequest();
        $request->mustBePost();

        $configModel = ConfigModel::instance();

        $form = $configModel->getThemeConfigForm(THEME);


        $errors = $form->validate($request->getPost());

        if ($errors) {
            $data = array(
                'status' => 'error',
                'errors' => $errors
            );
        } else {
            $configModel = ConfigModel::instance();
            $model = Model::instance();
            $theme = $model->getTheme(THEME);
            if (!$theme) {
                throw new \Ip\CoreException("Theme doesn't exist");
            }

            $options = $theme->getOptions();

            foreach($options as $option) {
                if (empty($option['name'])) {
                    continue;
                }

                $field = $form->getField($option['name']);
                if (!$field) {
                    continue;
                }

                $value = $field->getValueAsString($request->getPost(), $option['name']);
                $configModel->setConfigValue(THEME, $option['name'], $value);
            }

            $lessCompiler = LessCompiler::instance();
            $lessCompiler->clearCache(THEME);

            $data = array(
                'status' => 'success'
            );

        }





        $this->returnJson($data);
    }




}
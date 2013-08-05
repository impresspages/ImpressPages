<?php
/**
 * @package ImpressPages
 *
 */

namespace Modules\standard\design;




class Backend extends \Ip\Controller
{


    public function index()
    {
        $site = \Ip\ServiceLocator::getSite();


        $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/easyXDM/easyXDM.min.js');
        $site->addJavascript(BASE_URL.MODULE_DIR.'standard/design/public/options.js');
        $site->addJavascript(BASE_URL.MODULE_DIR.'standard/design/public/market.js');
        $site->addJavascript(BASE_URL.MODULE_DIR.'standard/design/public/themes.js');
        $site->addJavascript(BASE_URL.MODULE_DIR.'administrator/system/public/market.js');


        $model = Model::instance();

        $themes = $model->getAvailableThemes();

        $curTheme = null;
        foreach($themes as $theme) {
            if ($theme->getName() == THEME) {
                $curTheme = $theme;
            }
        }



        $data = array(
            'previewUrl' => BASE_URL,
            'themeTitle' => $curTheme ? $curTheme->getTitle() : '',
            'themeName' => $curTheme ? $curTheme->getName() : '',
            'themeVersion' => $curTheme ? $curTheme->getVersion() : '',
            'themeThumbnail' => $curTheme ? $curTheme->getThumbnail() : '',
            'marketUrl' => $model->getMarketUrl()
        );


        $contentView = \Ip\View::create('view/designdashboard.php', $data);
        $layout = $this->createAdminView($contentView);
        $site->setOutput($layout->render());
    }



    public function downloadTheme()
    {
        if (!\Ip\ServiceLocator::getRequest()->isPost()) {
            throw new \Ip\CoreException('Post required');
        }

        // TODOX allow only commands from market, maybe use nonce?
        $themeUrl = $_GET['url']; // TODOX remove this parameter, costruct url from theme name, do not allow arbitrary urls
        $themeName = $_GET['name']; // TODOX use $_POST

        $model = Model::instance();

        try {

            if ($model->isThemeAvailable($themeName)) {
                throw new \Ip\CoreException("Theme {$themeName} is already installed. THEME_DIR/{$themeName} exists.");
            }

            $themeDownloader = new ThemeDownloader();
            $themeDownloader->downloadTheme($themeName, $themeUrl);
        } catch (\Ip\CoreException $e) {
            $this->returnJson(
                array(
                    'success' => false,
                    'error' => $e->getMessage()
                )
            );
            return;
        }

        $this->returnJson(array('success' => true));
    }

    public function downloadThemes()
    {
        if (!isset($_POST['themes']) || !is_array($_POST['themes'])) {
            throw new \Ip\CoreException('Invalid parameters.');
        }

        $themes = $_POST['themes'];

        if (function_exists('set_time_limit')) {
            set_time_limit(count($themes) * 60 + 30);
        }

        $themeDownloader = new ThemeDownloader();
        $model = Model::instance();

        $result = array();
        foreach ($themes as $theme) {
            if (!empty($theme['url']) && !empty($theme['name']) && !empty($theme['signature'])) {
                if ($model->isThemeAvailable($theme['name'])) {
                    // TODOX make it work with JS
                    throw new \Ip\CoreException('Theme already installed.');
                }

                $themeDownloader->downloadTheme($theme['name'], $theme['url'], $theme['signature']);
                $result[] = true;
            }
        }

        $this->returnJson($result);
    }

    public function updateConfig()
    {
        $request = \Ip\ServiceLocator::getRequest();
        if (!$request->isPost()) {
            throw new \Ip\CoreException('Post required');
        }

        $configModel = ConfigModel::instance();

        $form = $configModel->getThemeConfigForm(THEME);


        $errors = $form->validate($request->getPost());

        if ($errors) {
            $data = array(
                'status' => 'error',
                'errors' => $errors
            );
        } else {
            $data = array(
                'status' => 'success'
            );

        }

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



        $this->returnJson($data);
    }


}
<?php
/**
 * @package ImpressPages
 *
 */

namespace Modules\standard\design;




class Controller extends \Ip\Controller
{


    public function index()
    {


        $this->backendOnly();
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
        $this->backendOnly();
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


    protected function backendOnly()
    {
        if (!\Ip\Backend::loggedIn()) {
            throw new \Exception('This controller can be accessed only by administrator');
        }
    }
}
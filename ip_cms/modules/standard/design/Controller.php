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
        $site->addJavascript(BASE_URL.MODULE_DIR.'standard/design/public/themes.js');


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
            'themeName' => $curTheme ? $curTheme->getName() : '',
            'themeVersion' => $curTheme ? $curTheme->getVersion() : '',
            'themePreviewImage' => $curTheme ? $curTheme->getPreviewImage() : '',
            'marketUrl' => $model->getMarketUrl()
        );


        $contentView = \Ip\View::create('view/designdashboard.php', $data);
        $layout = $this->createAdminView($contentView);
        $site->setOutput($layout->render());
    }


//    protected function getConfigurationForm()
//    {
//        $form = new \Modules\developer\form\Form();
//
//        //add text field to form object
//        $field = new \Modules\developer\form\Field\Text(
//            array(
//                'name' => 'firstField', //html "name" attribute
//                'label' => 'First field', //field label that will be displayed next to input field
//            ));
//        $form->addField($field);
//
//        return $form;
//    }

    public function downloadTheme()
    {
        $themeUrl = $_GET['url'];
        $themeName = $_GET['name'];

        $model = Model::instance();

        if ($model->isThemeAvailable($themeName)) {
            throw new \Ip\CoreException("Theme {$themeName} is already installed. THEME_DIR/{$themeName} exists.");
        }

        $themeDownloader = new ThemeDownloader();
        $themeDownloader->downloadTheme($themeName, $themeUrl);

        $this->returnJson(array("result" => true));
    }


    protected function backendOnly()
    {
        if (!\Ip\Backend::loggedIn()) {
            throw new \Exception('This controller can be accessed only by administrator');
        }
    }
}
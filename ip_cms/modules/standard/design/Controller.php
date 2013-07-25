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
        $view = \Ip\View::create('view/layout.php', $data);
        $site->setOutput($view->render());
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



    protected function backendOnly()
    {
        if (!\Ip\Backend::loggedIn()) {
            throw new \Exception('This controller can be accessed only by administrator');
        }
    }
}
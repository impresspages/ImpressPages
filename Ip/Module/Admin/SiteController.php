<?php
namespace Ip\Module\Admin;

class SiteController extends \Ip\Controller{

    public function loginAjax()
    {
        $parametersMod = \Ip\ServiceLocator::getParametersMod();

        \Ip\Request::mustBePost();

        $validateForm = $this->getLoginForm();
        $errors = $validateForm->validate(\Ip\Request::getPost());

        if (empty($errors)) {
            if (\Ip\Backend\Db::incorrectLoginCount(\Ip\Request::getPost('login').'('.$_SERVER['REMOTE_ADDR'].')') > 10) {
                $errors['password'] = $parametersMod->getValue('standard', 'configuration', 'system_translations', 'login_suspended');
                \Ip\Backend\Db::log('system', 'backend login suspended', \Ip\Request::getPost('login').'('.$_SERVER['REMOTE_ADDR'].')', 2);
            }

        }

        if (empty($errors)) {
            if (Model::instance()->login(\Ip\Request::getPost('login'), \Ip\Request::getPost('password'))) {
                \Ip\Backend\Db::log('system', 'backend login', \Ip\Request::getPost('login').' ('.$_SERVER['REMOTE_ADDR'].')', 0);
            } else {
                \Ip\Backend\Db::log('system', 'backend login incorrect', \Ip\Request::getPost('login').'('.$_SERVER['REMOTE_ADDR'].')', 1);
                $errors['password'] =  $parametersMod->getValue('standard', 'configuration', 'system_translations', 'login_incorrect');
            }
        }




        if (empty($errors)) {
            $answer = array(
                'status' => 'success',
                'redirectUrl' => BASE_URL . '?cms_action=manage'
            );
        } else {
            $answer = array(
                'status' => 'error',
                'errors' => $errors
            );
        }

        $this->returnJson($answer);
    }

    public function logout()
    {
        Model::instance()->logout();
        $this->redirect(BASE_URL.'admin/');
    }

    public function sessionRefresh()
    {
        $this->returnJson(array());
    }

    public function login()
    {
        if (\Ip\Backend::userId()) {
            //user has already been logged in
            $this->redirect(BASE_URL . '?cms_action=manage');
            return;
        }



        $site = \Ip\ServiceLocator::getSite();

        global $cms;
        $cms = new OldCmsInterface();

        $variables = array(
            'loginForm' => $this->getLoginForm()
        );

        $site->addJavascript(BASE_URL . LIBRARY_DIR . 'js/jquery/jquery.js');
        $site->addJavascript(BASE_URL . 'Ip/Module/Admin/Public/login.js');

        $config = \Ip\ServiceLocator::getConfig();
        $site->removeJavascript($config->getCoreModuleUrl().'Admin/Public/admin.js');
        $view = \Ip\View::create('View/login.php', $variables);
        $site->setOutput($view);
    }

    protected function getLoginForm()
    {
        $parametersMod = \Ip\ServiceLocator::getParametersMod();
        //create form object
        $form = new \Modules\developer\form\Form();

        //add text field to form object
        $field = new \Modules\developer\form\Field\Hidden(
            array(
                'name' => 'sa',
                'defaultValue' => 'Admin.loginAjax', //html "name" attribute
            ));
        $form->addfield($field);


        //add text field to form object
        $field = new \Modules\developer\form\Field\Blank(
            array(
                'name' => 'global_error',
            ));
        $form->addfield($field);

        //add text field to form object
        $field = new \Modules\developer\form\Field\Text(
            array(
                'name' => 'login', //html "name" attribute
                'label' => $parametersMod->getValue('standard','configuration','system_translations','login_name')
            ));
        $field->addValidator('Required');
        $form->addField($field);

        //add text field to form object
        $field = new \Modules\developer\form\Field\Password(
            array(
                'name' => 'password', //html "name" attribute
                'label' => $parametersMod->getValue('standard','configuration','system_translations','login_password')
            ));
        $field->addValidator('Required');
        $form->addField($field);


        //add text field to form object
        $field = new \Modules\developer\form\Field\Submit(
            array(
                'defaultValue' => $parametersMod->getValue('standard','configuration','system_translations','login_login')
            ));
        $field->addClass('ipsLoginButton');
        $form->addField($field);



        return $form;
    }
}
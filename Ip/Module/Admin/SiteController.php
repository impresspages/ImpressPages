<?php
namespace Ip\Module\Admin;

class SiteController extends \Ip\Controller{

    public function loginAjax()
    {

        \Ip\Request::mustBePost();

        $validateForm = $this->getLoginForm();
        $errors = $validateForm->validate(\Ip\Request::getPost());

        if (empty($errors)) {
            if (\Ip\Internal\Db::incorrectLoginCount(\Ip\Request::getPost('login').'('.$_SERVER['REMOTE_ADDR'].')') > 10) {
                $errors['password'] = __('Your login suspended for one hour.', 'ipAdmin');
                \Ip\Internal\Db::log('system', 'backend login suspended', \Ip\Request::getPost('login').'('.$_SERVER['REMOTE_ADDR'].')', 2);
            }

        }

        if (empty($errors)) {
            if (Model::instance()->login(\Ip\Request::getPost('login'), \Ip\Request::getPost('password'))) {
                \Ip\Internal\Db::log('system', 'backend login', \Ip\Request::getPost('login').' ('.$_SERVER['REMOTE_ADDR'].')', 0);
            } else {
                \Ip\Internal\Db::log('system', 'backend login incorrect', \Ip\Request::getPost('login').'('.$_SERVER['REMOTE_ADDR'].')', 1);
                $errors['password'] =  __('Incorrect name or password', 'ipAdmin');
            }
        }




        $redirectUrl = \Ip\Config::baseUrl('', array('cms_action' => 'manage'));
        if (empty($errors)) {
            $answer = array(
                'status' => 'success',
                'redirectUrl' => $redirectUrl
            );
        } else {
            $answer = array(
                'status' => 'error',
                'errors' => $errors
            );
        }
        if (\Ip\Request::getPost('ajax', 1)) {
            return new \Ip\Response\Json($answer);
        } else {
            //MultiSite autologin
            return new \Ip\Response\Redirect($redirectUrl);
        }
    }

    public function logout()
    {
        Model::instance()->logout();
        return new \Ip\Response\Redirect(\Ip\Config::baseUrl('admin/'));
    }

    public function sessionRefresh()
    {
        return new \Ip\Response\Json(array());
    }

    public function login()
    {
        if (\Ip\Module\Admin\Backend::userId()) {
            //user has already been logged in
            return new \Ip\Response\Redirect(\Ip\Config::baseUrl('', array('cms_action' => 'manage')));
        }


        global $cms;
        $cms = new OldCmsInterface();


        ipAddJavascript(\Ip\Config::coreModuleUrl('Assets/assets/js/jquery.js'));
        ipAddJavascript(\Ip\Config::coreModuleUrl('Admin/Public/login.js'));



        $response = new \Ip\Response\Layout();
        $response->setLayout(\Ip\Config::coreMOduleFile('Admin/View/login.php'));
        $response->setLayoutVariable('loginForm', $this->getLoginForm());
        return $response;
        $view = \Ip\View::create('View/login.php', $variables);
        return $view;
    }

    protected function getLoginForm()
    {
        //create form object
        $form = new \Ip\Form();

        //add text field to form object
        $field = new \Ip\Form\Field\Hidden(
            array(
                'name' => 'sa',
                'defaultValue' => 'Admin.loginAjax', //html "name" attribute
            ));
        $form->addfield($field);


        //add text field to form object
        $field = new \Ip\Form\Field\Blank(
            array(
                'name' => 'global_error',
            ));
        $form->addfield($field);

        //add text field to form object
        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'login', //html "name" attribute
                'label' => __('Name', 'ipAdmin')
            ));
        $field->addValidator('Required');
        $form->addField($field);

        //add text field to form object
        $field = new \Ip\Form\Field\Password(
            array(
                'name' => 'password', //html "name" attribute
                'label' => __('Password', 'ipAdmin')
            ));
        $field->addValidator('Required');
        $form->addField($field);


        //add text field to form object
        $field = new \Ip\Form\Field\Submit(
            array(
                'defaultValue' => __('Login', 'ipAdmin')
            ));
        $field->addClass('ipsLoginButton');
        $form->addField($field);



        return $form;
    }
}
<?php
namespace Ip\Module\Admin;

class SiteController extends \Ip\Controller{

    public function loginAjax()
    {

        ipRequest()->mustBePost();

        $validateForm = $this->getLoginForm();
        $errors = $validateForm->validate(ipRequest()->getPost());

        if (empty($errors)) {
            // TODOX do it through filter and don't use log
//            if (\Ip\Internal\Db::incorrectLoginCount(ipRequest()->getPost('login').'('.$_SERVER['REMOTE_ADDR'].')') > 10) {
//                $errors['password'] = __('Your login suspended for one hour.', 'ipAdmin');
//                ipLog()->notice('Admin login `{username}` suspended. IP: {ip}', array('username' => ipRequest()->getPost('login'), 'ip' => ipRequest()->getServer('REMOTE_ADDR')));
//            }

        }

        $username = ipRequest()->getPost('login');

        if (empty($errors)) {
            $ip = ipRequest()->getServer('REMOTE_ADDR');
            if (Model::instance()->login($username, ipRequest()->getPost('password'))) {
                ipLog()->info('Admin.loggedIn: {username} from {ip}', array('username' => $username, 'ip' => $ip));
            } else {
                ipLog()->info('Admin.loginIncorrect: {username} from {ip}', array('username' => $username, 'ip' => $ip));
                ipDispatcher()->notify('Admin.loginIncorrect', array('username' => $username, 'ip' => $ip));
                $errors['password'] =  __('Incorrect username or password', 'ipAdmin');
            }
        }



        //TODOX replace with url to first module;
        $redirectUrl = ipHomeUrl();
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

        if (ipRequest()->getPost('ajax', 1)) {
            $response =  new \Ip\Response\Json($answer);
            return $response;
        } else {
            //MultiSite autologin
            return new \Ip\Response\Redirect($redirectUrl);
        }
    }

    public function logout()
    {
        Model::instance()->logout();
        return new \Ip\Response\Redirect(ipFileUrl('admin/'));
    }

    public function sessionRefresh()
    {
        return new \Ip\Response\Json(array());
    }

    public function login()
    {
        if (\Ip\Module\Admin\Backend::userId()) {
            //user has already been logged in
            \Ip\Module\Content\Service::setManagementMode(1);
            return new \Ip\Response\Redirect(ipHomeUrl());
        }


        $response = ipResponse();//new \Ip\Response\Layout();
        $response->setLayout(ipFile('Ip/Module/Admin/view/login.php'));
        $response->setLayoutVariable('loginForm', $this->getLoginForm());
        $response->addJavascript(ipFileUrl('Ip/Module/Assets/assets/js/jquery.js'));
        $response->addJavascript(ipFileUrl('Ip/Module/Admin/assets/login.js'));
        return $response;
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
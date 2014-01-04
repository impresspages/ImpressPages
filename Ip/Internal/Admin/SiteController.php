<?php
namespace Ip\Internal\Admin;

class SiteController extends \Ip\Controller{

    public function loginAjax()
    {

        ipRequest()->mustBePost();

        $validateForm = $this->getLoginForm();
        $errors = $validateForm->validate(ipRequest()->getPost());

        $username = ipRequest()->getPost('login');

        if (empty($errors)) {
            $model = Model::instance();
            if (!$model->login($username, ipRequest()->getPost('password'))) {
                $errors['password'] = $model->getLastError();
            }
        }

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
        if (\Ip\Internal\Admin\Backend::userId()) {
            //user has already been logged in
            \Ip\Internal\Content\Service::setManagementMode(1);
            return new \Ip\Response\Redirect(ipHomeUrl());
        }


        $response = ipResponse();//new \Ip\Response\Layout();
        $response->setLayout(ipFile('Ip/Internal/Admin/view/login.php'));
        $response->setLayoutVariable('loginForm', $this->getLoginForm());
        $response->addJavascript(ipFileUrl('Ip/Internal/Admin/assets/login.js'));
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
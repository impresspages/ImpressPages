<?php
namespace Ip\Internal\Admin;

class SiteController extends \Ip\Controller{

    public function loginAjax()
    {

        ipRequest()->mustBePost();

        $validateForm = FormHelper::getLoginForm();
        $errors = $validateForm->validate(ipRequest()->getPost());

        $username = ipRequest()->getPost('login');

        if (empty($errors)) {
            $model = Model::instance();
            if (!$model->login($username, ipRequest()->getPost('password'))) {
                $errors = $model->getErrors();
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

    public function passwordResetAjax()
    {

        ipRequest()->mustBePost();

        $validateForm = FormHelper::getPasswordResetForm()Form();
        $errors = $validateForm->validate(ipRequest()->getPost());

        $username = ipRequest()->getPost('login');

        if (empty($errors)) {
            $user = \Ip\Internal\Administrators\Service::getByEmail($username);
            if (!$user) {
                $user = \Ip\Internal\Administrators\Service::getByUsername($username);
            }

            if ($user) {
                \Ip\Internal\Administrators\Service::resetPassword($user['id']);
            } else {
                $errors['username'] = __('Following administrator doesn\'t exist', 'ipAdmin', FALSE);
            }

        }

        if (empty($errors)) {
            $answer = array(
                'status' => 'success',
            );
        } else {
            $answer = array(
                'status' => 'error',
            );
        }

        $response =  new \Ip\Response\Json($answer);
        return $response;
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


        if (ipRequest()->getQuery('resetPassword', 0)) {
            $content = ipView('view/resetPassword.php', array('resetPasswordForm' => FormHelper::getPasswordResetForm()));
        } else {
            $content = ipView('view/login.php', array('loginForm' => FormHelper::getLoginForm()));
        }
        $response = ipResponse();
        $response->setLayout(ipFile('Ip/Internal/Admin/view/loginLayout.php'));
        $response->setLayoutVariable('content', $content);
        ipAddJs(ipFileUrl('Ip/Internal/Admin/assets/login.js'));
        return $response;
    }


}

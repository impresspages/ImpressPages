<?php
namespace Ip\Internal\Admin;

class SiteController extends \Ip\Controller{

    public function login()
    {
        if (\Ip\Internal\Admin\Backend::userId()) {
            //user has already been logged in
            \Ip\Internal\Content\Service::setManagementMode(1);
            return new \Ip\Response\Redirect(ipHomeUrl());
        }


        $content = ipView('view/login.php', array('loginForm' => FormHelper::getLoginForm()));
        ipAddJs('Ip/Internal/Admin/assets/login.js');
        $response = ipResponse();
        $response->setLayout('Ip/Internal/Admin/view/loginLayout.php');
        $response->setLayoutVariable('content', $content);

        return $response;
    }

    public function passwordResetForm()
    {
        if (\Ip\Internal\Admin\Backend::userId()) {
            //user has already been logged in
            \Ip\Internal\Content\Service::setManagementMode(1);
            return new \Ip\Response\Redirect(ipHomeUrl());
        }


        $content = ipView('view/passwordReset.php', array('passwordResetForm' => FormHelper::getPasswordResetForm1()));
        ipAddJs('Ip/Internal/Admin/assets/passwordReset1.js');

        $response = ipResponse();
        $response->setLayout('Ip/Internal/Admin/view/loginLayout.php');
        $response->setLayoutVariable('content', $content);

        return $response;

    }

    public function passwordResetInfo()
    {
        if (\Ip\Internal\Admin\Backend::userId()) {
            //user has already been logged in
            \Ip\Internal\Content\Service::setManagementMode(1);
            return new \Ip\Response\Redirect(ipHomeUrl());
        }


        $content = ipView('view/passwordResetInfo.php');

        $response = ipResponse();
        $response->setLayout('Ip/Internal/Admin/view/loginLayout.php');
        $response->setLayoutVariable('content', $content);

        return $response;

    }

    public function passwordResetSuccess()
    {
        if (\Ip\Internal\Admin\Backend::userId()) {
            //user has already been logged in
            \Ip\Internal\Content\Service::setManagementMode(1);
            return new \Ip\Response\Redirect(ipHomeUrl());
        }


        $content = ipView('view/passwordResetSuccess.php');

        $response = ipResponse();
        $response->setLayout('Ip/Internal/Admin/view/loginLayout.php');
        $response->setLayoutVariable('content', $content);

        return $response;

    }

    public function passwordReset()
    {
        if (\Ip\Internal\Admin\Backend::userId()) {
            //user has already been logged in
            \Ip\Internal\Content\Service::setManagementMode(1);
            return new \Ip\Response\Redirect(ipHomeUrl());
        }


        $content = ipView('view/passwordReset2.php', array('passwordResetForm' => FormHelper::getPasswordResetForm2()));
        ipAddJs('Ip/Internal/Admin/assets/passwordReset2.js');

        $response = ipResponse();
        $response->setLayout('Ip/Internal/Admin/view/loginLayout.php');
        $response->setLayoutVariable('content', $content);

        return $response;
    }


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

        $model = Model::instance();
        $adminMenuItems = $model->getAdminMenuItems(null);
        if (!empty($adminMenuItems)) {
            //redirect user to the first module
            $firstMenuItem = $adminMenuItems[0];
            $redirectUrl = $firstMenuItem->getUrl();
        }


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

        $validateForm = FormHelper::getPasswordResetForm1();
        $errors = $validateForm->validate(ipRequest()->getPost());

        $username = ipRequest()->getPost('username');

        if (empty($errors)) {
            $user = \Ip\Internal\Administrators\Service::getByEmail($username);
            if (!$user) {
                $user = \Ip\Internal\Administrators\Service::getByUsername($username);
            }

            if ($user) {
                \Ip\Internal\Administrators\Service::sendResetPasswordLink($user['id']);
            } else {
                $errors['username'] = __('Following administrator doesn\'t exist', 'ipAdmin', FALSE);
            }

        }

        if (empty($errors)) {
            $answer = array(
                'status' => 'success',
                'redirectUrl' => ipActionUrl(array('sa' => 'Admin.passwordResetInfo'))
            );
        } else {
            $answer = array(
                'status' => 'error',
                'errors' => $errors
            );
        }

        $response =  new \Ip\Response\Json($answer);
        return $response;
    }

    public function passwordResetAjax2()
    {

        ipRequest()->mustBePost();

        $validateForm = FormHelper::getPasswordResetForm2();
        $errors = $validateForm->validate(ipRequest()->getPost());

        $userId = ipRequest()->getPost('userId');
        $secret = ipRequest()->getPost('secret');
        $password = ipRequest()->getPost('password');


        try {
            \Ip\Internal\Administrators\Service::resetPassword($userId, $secret, $password);
        } catch (\Ip\Exception $e) {
            $user['global_error'] = $e->getMessage();
        }


        if (empty($errors)) {
            $answer = array(
                'status' => 'success',
                'redirectUrl' => ipActionUrl(array('sa' => 'Admin.passwordResetSuccess'))
            );
        } else {
            $answer = array(
                'status' => 'error',
                'errors' => $errors
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




}

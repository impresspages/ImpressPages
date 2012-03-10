<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

namespace Modules\community\user;

if (!defined('CMS')) exit;

require_once __DIR__.'/db.php';


class Controller  extends \Ip\Controller{


    public function login() {

        $loginForm = Config::getLoginForm();

        $errors = $loginForm->validate($_POST);

        if ($errors) {
            $data = array(
                'status' => 'error',
                'errors' => $errors
            );
        } else {
            //$this->sendEmail($form, $postData, $data);

            $data = array(
            'status' => 'success'
            );
        }

        $this->returnJson($data);
return;
        if($parametersMod->getValue('community','user','options','login_type') == 'login')
        $tmpUser = Db::userByLogin($_POST['login']);
        else
        $tmpUser = Db::userByEmail($_POST['email']);

        if($parametersMod->getValue('community', 'user', 'options', 'encrypt_passwords')) {
            $tmp_password = md5($_POST['password'].\Modules\community\user\Config::$hashSalt);
        } else {
            $tmp_password = $_POST['password'];
        }

        if($tmpUser && isset($_POST['password']) && $tmp_password == $tmpUser['password']) {
            $this->login($tmpUser);
            if($parametersMod->getValue('community','user','options','enable_autologin') && isset($_POST['autologin']) && $_POST['autologin'] ) {
                setCookie(
                Config::$autologinCookieName,
                json_encode(array('id' => $tmpUser['id'], 'pass' => md5($tmpUser['password'].$tmpUser['created_on']))),
                time() + $parametersMod->getValue('community','user','options','autologin_time') * 60 * 60 * 24,
                Config::$autologinCookiePath,
                Config::getCookieDomain()
                );
            }

            $html = $this->redirectAfterLogin();


        } else {
            $standardForm = new \Library\Php\Form\Standard(\Modules\community\user\Config::getRegistrationFields());
            $errors = array();
            $globalError = null;
            $site->dispatchEvent('community', 'user', 'incorrect_login', array('post'=>$_POST));

            if($parametersMod->getValue('community','user','options','login_type') == 'login') {
                $globalError = $parametersMod->getValue('community', 'user', 'errors', 'incorrect_login_data');
                $errors['login'] = '';
            }else {
                $globalError = $parametersMod->getValue('community', 'user', 'errors', 'incorrect_email_data');
                $errors['email'] = '';
            }
            $errors['password'] = '';
            $log->log('community/user', 'incorrect frontend login', $_SERVER['REMOTE_ADDR']);
            $html = $standardForm->generateErrorAnswer($errors, $globalError);
        }

        echo $html;
        \Db::disconnect();
        exit;
        break;
    }



}
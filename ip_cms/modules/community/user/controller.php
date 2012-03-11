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

    public function registration() {
        global $site;
        global $parametersMod;
        
        $userZone = $site->getZoneByModule('community', 'user');
        if(!$userZone) {
            throw new \Exception("There is no user zone on ImpressPages system");
        }
        
        
        $html = '';

        if(!$parametersMod->getValue('community','user','options','enable_registration')) {
            $site->setOutput('');
            return;
        }
        
        $postData = $_POST;
        
        $registrationForm = Config::getRegistrationForm();

        $errors = $registrationForm->validate($postData);

        $sameEmailUser = Db::userByEmail($postData['email']);

        if($postData['email'] && $sameEmailUser) {
            $errors['email'] = $parametersMod->getValue('community', 'user', 'errors', 'already_registered');
        }

        if($parametersMod->getValue('community','user','options','login_type') == 'login') {
            $sameLoginUser = Db::userByLogin($postData['login']);
            if($sameLoginUser) {
                $errors['login'] = $parametersMod->getValue('community', 'user', 'errors', 'already_registered');
            }
        }

        if($parametersMod->getValue('community','user','options','type_password_twice') && $postData['password'] != $postData['confirm_password']) {
            $errors['password'] = $parametersMod->getValue('community', 'user', 'errors', 'passwords_dont_match');
            $errors['confirm_password'] = $parametersMod->getValue('community', 'user', 'errors', 'passwords_dont_match');
        }

        if (sizeof($errors) > 0) {
            $data = array(
                'status' => 'error',
                'errors' => $errors
            );
            $this->returnJson($data);
        } else {
            $tmp_code = md5(uniqid(rand(), true));
            if($parametersMod->getValue('community', 'user', 'options', 'encrypt_passwords')) {
                $password = md5($postData['password'].\Modules\community\user\Config::$hashSalt);
            } else {
                $password = $postData['password'];
            }

            if ($parametersMod->getValue('community', 'user', 'options', 'require_email_confirmation')) {
                $verified = '0';
            } else {
                $verified = '1';
            }
            
            $additionalData = array(
                'verified' => $verified,
                'verification_code' => $tmp_code,
                'password' => $password,
                'last_login'=>date("Y-m-d"),
                'language_id'=>$site->currentLanguage['id']
            );
            
            $insertId = $registrationForm->writeToDatabase(DB_PREF.'m_community_user', $postData, $additionalData);
            if($insertId === false) {
                $errors['globalError'] = 'Cannot register new user. Please contact administrator.';
                $data = array(
                    'status' => 'error',
                    'errors' => $errors
                );
                $this->returnJson($data);
                return;
            }
            
            $site->dispatchEvent('community', 'user', 'register', array('user_id'=>$insertId));
            if ($parametersMod->getValue('community', 'user', 'options', 'require_email_confirmation')) {
                $this->sendVerificationLink($postData['email'], $tmp_code, $insertId);
                $data = array (
                    'status' => 'success',
                    'redirectUrl' => $site->generateUrl(null, $userZone->getName(), array(Config::$urlRegistrationVerificationRequired))
                );
                $this->returnJson($data);
                return;
            } else {
                if ($parametersMod->getValue('community', 'user', 'options', 'autologin_after_registration')) {
                    $tmpUser = Db::userById($insertId);
                    if ($tmpUser) {
                        $this->login($tmpUser);
                        $redirectUrl = $this->redirectAfterLoginUrl();
                        $data = array (
                            'status' => 'success',
                            'redirectUrl' => $redirectUrl
                        );
                        $this->returnJson($data);
                    }
                } else {
                        $data = array (
                            'status' => 'success',
                            'redirectUrl' => $site->generateUrl(null, $userZone->getName(), array(Config::$urlRegistrationVerified))
                        );
                        $this->returnJson($data);
                        return;
                }
            }
        }
    }
    
    
    function redirectAfterLoginUrl () {
        global $parametersMod;
        global $site;
        $html = '';
        if(isset($_SESSION['modules']['community']['user']['page_after_login'])) {
            $url = $_SESSION['modules']['community']['user']['page_after_login'];
            unset($_SESSION['modules']['community']['user']['page_after_login']);
        } else {
            if($parametersMod->getValue('community', 'user', 'options', 'zone_after_login')) {
                $url = $site->generateUrl(null, $parametersMod->getValue('community', 'user', 'options', 'zone_after_login'));
            } else {
                $url = '';
            }
        }
        return $url;
    }    
    
    private function sendVerificationLink($to, $code, $userId) {
        global $parametersMod;
        global $site;

        
        $content = $parametersMod->getValue('community', 'user', 'email_messages', 'text_verify_registration');
        $link = $site->generateUrl(null, null, array(), array("g" => "community", "m" => "user", "a" => "registration_verification", "id" => $userId, "code" => $code));
        $content = str_replace('[[link]]', '<a href="'.$link.'">'.$link.'</a>', $content);
        
        $websiteName = $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'name');
        $websiteEmail = $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'email'); 
        
        
        $emailData = array(
            'content' => $content,
            'name' => $websiteName,
            'email' => $websiteEmail
        );
        
        $email = \Ip\View::create('view/email.php', $emailData)->render();
        $to = $from = $websiteEmail;
        
        $subject = $parametersMod->getValue('community', 'user', 'email_messages', 'subject_verify_registration');
        
        $files = array();    
        $emailQueue = new \Modules\administrator\email_queue\Module();
        $emailQueue->addEmail($from, '', $to, '',  $subject, $email, false, true, $files);

        $emailQueue->send();
    }    



}
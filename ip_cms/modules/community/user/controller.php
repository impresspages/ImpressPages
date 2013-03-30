<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Modules\community\user;

if (!defined('CMS')) exit;

require_once __DIR__.'/db.php';


class Controller  extends \Ip\Controller{

    private $userZone;

    public function init() {
        global $site;
        $userZone = $site->getZoneByModule('community', 'user');
        if(!$userZone) {
            throw new \Exception("There is no user zone on ImpressPages system");
        }
        $this->userZone = $userZone;
    }

    public function login() {
        global $parametersMod;
        global $site;
        global $log;
        global $dispatcher;
        $loginForm = Config::getLoginForm();
        $errors = $loginForm->validate($_POST);

        if ($errors) {
            $data = array(
                'status' => 'error',
                'errors' => $errors
            );
            $this->returnJson($data);
            return;
        }

        if($parametersMod->getValue('community','user','options','login_type') == 'login') {
            $tmpUser = Db::userByLogin($_POST['login']);
        } else {
            $tmpUser = Db::userByEmail($_POST['email']);
        }

        if($parametersMod->getValue('community', 'user', 'options', 'encrypt_passwords')) {
            $tmp_password = md5($_POST['password'].\Modules\community\user\Config::$hashSalt);
        } else {
            $tmp_password = $_POST['password'];
        }


        if($tmpUser && isset($_POST['password']) && $tmp_password == $tmpUser['password']) {
            $this->loginUser($tmpUser);

            $data = array('userId' => $tmpUser['id']);
            $dispatcher->notify(new Event($this, Event::LOGIN, $data));

            if($parametersMod->getValue('community','user','options','enable_autologin') && isset($_POST['autologin']) && $_POST['autologin'] ) {
                setCookie(
                Config::$autologinCookieName,
                json_encode(array('id' => $tmpUser['id'], 'pass' => md5($tmpUser['password'].$tmpUser['created_on']))),
                time() + $parametersMod->getValue('community','user','options','autologin_time') * 60 * 60 * 24,
                Config::$autologinCookiePath,
                Config::getCookieDomain()
                );
            }

            $answer = array(
                'status' => 'success',
                'redirectUrl' => $this->redirectAfterLoginUrl() 
            );
            $this->returnJson($answer);
            return;
        } else {
            $errors = array();
            $site->dispatchEvent('community', 'user', 'incorrect_login', array('post'=>$_POST));

            if($parametersMod->getValue('community','user','options','login_type') == 'login') {
                $errors['globalError'] = $parametersMod->getValue('community', 'user', 'errors', 'incorrect_login_data');
            }else {
                $errors['globalError'] = $parametersMod->getValue('community', 'user', 'errors', 'incorrect_email_data');
                $errors['email'] = '';
            }
            $log->log('community/user', 'incorrect frontend login', $_SERVER['REMOTE_ADDR']);
            $answer = array(
                'status' => 'error',
                'errors' => $errors 
            );
            $this->returnJson($answer);
            return;
        }

    }

    public function logout() {
        global $session;
        global $parametersMod;
        global $dispatcher;

        $userId = $session->userId();

        $session->logout();

        if ($userId) {
            $data = array('userId' => $userId);
            $dispatcher->notify(new Event($this, Event::LOGOUT, $data));
        }

        if($parametersMod->getValue('community','user','options','enable_autologin')) {
            setCookie(
            Config::$autologinCookieName,
            '',
            time()-60,
            Config::$autologinCookiePath,
            Config::getCookieDomain()
            );
        }
        $this->redirect(BASE_URL);
    }

    public function registration() {
        global $site;
        global $parametersMod;
        global $dispatcher;

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
            return;
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

            //deprecated event
            $site->dispatchEvent('community', 'user', 'register', array('user_id'=>$insertId));

            //new event
            $data = array('userId' => $insertId);
            $dispatcher->notify(new Event($this, Event::REGISTRATION, $data));


            if ($parametersMod->getValue('community', 'user', 'options', 'require_email_confirmation')) {
                $this->sendVerificationLink($postData['email'], $tmp_code, $insertId);
                $data = array (
                    'status' => 'success',
                    'redirectUrl' => $site->generateUrl(null, $this->userZone->getName(), array(Config::$urlRegistrationVerificationRequired))
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
                        return;
                    }
                } else {
                    $data = array (
                            'status' => 'success',
                            'redirectUrl' => $site->generateUrl(null, $this->userZone->getName(), array(Config::$urlRegistrationVerified))
                    );
                    $this->returnJson($data);
                    return;
                }
            }
        }
    }


    public function updateProfile() {
        global $session;
        global $site;
        global $parametersMod;
        global $dispatcher;
        if(!$session->loggedIn()) {
            $site->setOutput('');
            return;
        }
            
        $postData = $_POST;
        $registrationForm = Config::getProfileForm();
        $errors = $registrationForm->validate($postData);
        $tmpUser = Db::userById($session->userId());

        if(isset($_POST['email']) && $_POST['email'] != $tmpUser['email']) {
            $user_by_new_email = Db::userByEmail($_POST['email']);
            if($user_by_new_email && $user_by_new_email['verified']) {
                $errors['email'] = $parametersMod->getValue('community', 'user', 'errors', 'already_registered');
            }

        }


        if($parametersMod->getValue('community','user','options','type_password_twice') && $_POST['password'] != $_POST['confirm_password']) {
            $errors['password'] = $parametersMod->getValue('community', 'user', 'errors', 'passwords_dont_match');
            $errors['confirm_password'] = $parametersMod->getValue('community', 'user', 'errors', 'passwords_dont_match');
        }



        if(count($errors) > 0) {
            $data = array(
                'status' => 'error',
                'errors' => $errors
            );
            $this->returnJson($data);
            return;
        } else {
            if(!$tmpUser) {
                throw new \Exception("User does not exist. ".$session->userId()." ".$_POST['email']);
            }
            $additionalFields = array();

            if(isset($_POST['email']) && $_POST['email'] != $tmpUser['email']) {
                $tmp_code = md5(uniqid(rand(), true));
                $additionalFields['new_email'] = $_POST['email'];
                $additionalFields['verification_code'] = $tmp_code;
            }

            if(isset($_POST['password']) && $_POST['password'] != '') {
                if($parametersMod->getValue('community', 'user', 'options', 'encrypt_passwords')) {
                    $additionalFields['password'] =  md5($_POST['password'].\Modules\community\user\Config::$hashSalt);
                } else {
                    $additionalFields['password'] =  $_POST['password'];
                }
            }


            //$data - all data that needs to be stored except standard fields which are handled separately.
            $data = $registrationForm->filterValues($_POST);
            $data['password'] = null;
            $data['email'] = null;
            $data['confirm_password'] = null;
            $data['submit'] = null;
            
            $insertId = $registrationForm->updateDatabase(DB_PREF.'m_community_user', 'id', $tmpUser['id'], $data, $additionalFields);

            //deprecated event
            $site->dispatchEvent('community', 'user', 'update_profile', array('user_id'=>$tmpUser['id']));

            //new event
            $data = array('userId' => $insertId, 'profileData' => array_merge($data, $additionalFields));
            $dispatcher->notify(new Event($this, Event::PROFILE_UPDATE, $data));


            if(isset($_POST['email']) && $_POST['email'] != $tmpUser['email']) {
                $this->sendUpdateVerificationLink($_POST['email'], $tmp_code, $tmpUser['id']);
                $redirectUrl = $site->generateUrl(null, $this->userZone->getName(), array(Config::$urlEmailVerificationRequired));
            }else {
                $redirectUrl = $site->generateUrl(null, $this->userZone->getName(), array(Config::$urlProfile), array("message"=>"updated"));
            }
            
            $answer = array(
                'status' => 'success',
                'redirectUrl' => $redirectUrl
            );
            $this->returnJson($answer);
            return;
        }
    }

    /**
     *
     * Registration verification
     */
    public function verification() {
        global $site;
        global $parametersMod;
        global $dispatcher;
        if (!isset($_REQUEST['id'])) {
            throw new Exception('Missing request data');
        }
        $userId = $_REQUEST['id'];

        if (!isset($_REQUEST['code'])) {
            throw new Exception('Missing request data');
        }
        $code = $_REQUEST['code'];


        $current = Db::userById ($userId);
        if ($current) {
            $sameEmailUser = Db::userByEmail ($current['email']);
            $sameLoginUser = Db::userByLogin ($current['login']);
            if ($current['verification_code'] == $code) {
                if ($sameEmailUser && $sameEmailUser['id'] != $current['id']) {
                    $this->redirect($site->generateUrl(null, $this->userZone->getName(), array(Config::$urlVerificationErrorEmailExist)));
                } elseif($parametersMod->getValue('community','user','options','login_type') == 'login' && $sameLoginUser && $sameLoginUser['id'] != $current['id']) {
                    $this->redirect($site->generateUrl(null, $this->userZone->getName(), array(Config::$urlVerificationErrorUserExist)));
                } else {
                    if (!$current['verified']) {
                        Db::verify($current['id']);

                        //deprecated event
                        $site->dispatchEvent('community', 'user', 'registration_verification', array('user_id'=>$current['id']));

                        //new event
                        $data = array('userId' => $current['id']);
                        $dispatcher->notify(new Event($this, Event::REGISTRATION_VERIFICATION, $data));

                        if ($parametersMod->getValue('community', 'user', 'options', 'autologin_after_registration')) {
                            $this->loginUser($current);
                            $this->redirect($this->redirectAfterLoginUrl());
                        }
                    }

                    $this->redirect($site->generateUrl(null, $this->userZone->getName(), array(Config::$urlRegistrationVerified)));
                }
            } else {
                $this->redirect($site->generateUrl(null, $this->userZone->getName(), array(Config::$urlRegistrationVerificationError)));
            }
        } else {
            $this->redirect($site->generateUrl(null, $this->userZone->getName(), array(Config::$urlRegistrationVerificationError)));
        }
    }


    public function newEmailVerification() {
        global $site;
        global $dispatcher;
        if (!isset($_REQUEST['id']) || !isset($_REQUEST['code'])) {
            return; //do nothing. Rendar as a regular page.
        }
    
        $sameEmailUser = Db::userById($_REQUEST['id']);
        if($sameEmailUser) {
            if($sameEmailUser['verification_code'] == $_REQUEST['code']) {
                $user_with_new_email = Db::userByEmail($sameEmailUser['new_email']);
                if($user_with_new_email) {
                    if($user_with_new_email['id'] == $sameEmailUser['id']) {
                        $this->redirect($site->generateUrl(null, $this->userZone->getName(), array(Config::$urlRegistrationVerified)));
                    } else {
                        $this->redirect($site->generateUrl(null, $this->userZone->getName(), array(Config::$urlNewEmailVerificationError)));
                    }
                }else {
                    if($sameEmailUser['new_email'] == '') {
                        $this->redirect($site->generateUrl(null, $this->userZone->getName(), array(Config::$urlRegistrationVerified)));
                    }else {
                        Db::verifyNewEmail($sameEmailUser['id']);

                        //deprecated event
                        $site->dispatchEvent('community', 'user', 'new_email_verification', array('user_id'=>$sameEmailUser['id']));

                        //new event
                        $data = array('userId' => $sameEmailUser['id']);
                        $dispatcher->notify(new Event($this, Event::NEW_EMAIL_VERIFICATION, $data));
    
                        $this->redirect($site->generateUrl(null, $this->userZone->getName(), array(Config::$urlNewEmailVerified)));
                    }
                }
            } else {
                $this->redirect($site->generateUrl(null, $this->userZone->getName(), array(Config::$urlNewEmailVerificationError)));
            }
        }else {
            $this->redirect($site->generateUrl(null, $this->userZone->getName(), array(Config::$urlNewEmailVerificationError)));
        }
    
    
    }
    
    
    public function passwordReset() {
        global $parametersMod;
        global $site;
        global $dispatcher;
        $postData = $_POST;
        $passwordResetForm = Config::getPasswordResetForm();
        $errors = $passwordResetForm->validate($postData);
    
        $tmpUser = Db::userByEmail($_POST['email']);
        if (!$tmpUser) {
            $errors['email'] = $parametersMod->getValue('community', 'user', 'errors', 'email_doesnt_exist');
        }
    
        if(!isset($_POST['password']) || $_POST['password'] == '' || $parametersMod->getValue('community','user','options','type_password_twice') && $_POST['password'] != $_POST['confirm_password']) {
            $errors['password'] = $parametersMod->getValue('community', 'user', 'errors', 'passwords_dont_match');
            $errors['confirm_password'] = $parametersMod->getValue('community', 'user', 'errors', 'passwords_dont_match');
        }
    
    
        if (sizeof($errors) > 0) {
            $data = array(
                'status' => 'error',
                'errors' => $errors
            );
            $this->returnJson($data);
            return;
        } else {
            
            $tmp_code = md5(uniqid(rand(), true));
            if($parametersMod->getValue('community', 'user', 'options', 'encrypt_passwords')) {
                $additionalFields['new_password'] = md5($_POST['password'].\Modules\community\user\Config::$hashSalt);
            } else {
                $additionalFields['new_password'] = $_POST['password'];
            }
            $additionalFields['verification_code'] = $tmp_code;
            $insertId = $passwordResetForm->updateDatabase(DB_PREF.'m_community_user', 'id', $tmpUser['id'], array(), $additionalFields);
            $this->sendPasswordResetLink($_POST['email'], $tmp_code, $tmpUser['id']);

            $data = array('userId' => $tmpUser['id']);
            $dispatcher->notify(new Event($this, Event::PASSWORD_RESET, $data));
            
            $data = array(
                'status' => 'success',
                'redirectUrl' => $site->generateUrl(null, $this->userZone->getName(), array(Config::$urlPasswordResetSentText)) 
            );
            $this->returnJson($data);
            return;
        }

    }
    
    public function passwordResetVerification () {
        global $site;
        global $dispatcher;
        $current = Db::userById($_REQUEST['id']);
        if($current && $current['verified']) {
            if($current['verification_code'] == $_REQUEST['code']) {
                if($current['new_password'] != '') {
                    if(Db::verifyNewPassword($current['id'])) {
                        //deprecated event
                        $site->dispatchEvent('community', 'user', 'password_reset', array('user_id'=>$current['id']));
                        //new event
                        $data = array('userId' => $current['id']);
                        $dispatcher->notify(new Event($this, Event::PASSWORD_RESET_VERIFICATION, $data));

                        $this->redirect($site->generateUrl(null, $this->userZone->getName(), array(Config::$urlPasswordResetVerified)));
                    } else {
                        $this->redirect($site->generateUrl(null, $this->userZone->getName(), array(Config::$urlPasswordResetVerificationError)));
                    }
                } else {
                    $this->redirect($site->generateUrl(null, $this->userZone->getName(), array(Config::$urlPasswordResetVerified)));
                }
            } else {
                $this->redirect($site->generateUrl(null, $this->userZone->getName(), array(Config::$urlPasswordResetVerificationError)));
            }
        } else {
            $this->redirect($site->generateUrl(null, $this->userZone->getName(), array(Config::$urlPasswordResetVerificationError)));
        }
    }
    
    
    
    private function redirectAfterLoginUrl () {
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
                $url = $site->generateUrl(null, $this->userZone->getName(), array(Config::$urlProfile));
            }
        }
        return $url;
    }


    private function loginUser ($user) {
        global $log;
        global $session;
        global $site;
        $session->login($user['id']);
        Db::loginTimestamp($user['id']);
        $log->log('community/user', 'frontend login', $user['login']." ".$user['email']." ".$_SERVER['REMOTE_ADDR']);
    }

    private function sendVerificationLink($to, $code, $userId) {
        global $parametersMod;
        global $site;


        $content = $parametersMod->getValue('community', 'user', 'email_messages', 'text_verify_registration');
        $link = $site->generateUrl(null, null, array(), array("g" => "community", "m" => "user", "a" => "verification", "id" => $userId, "code" => $code));
        $content = str_replace('[[link]]', '<a href="'.$link.'">'.$link.'</a>', $content);

        $websiteName = $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'name');
        $websiteEmail = $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'email');


        $emailData = array(
            'content' => $content,
            'name' => $websiteName,
            'email' => $websiteEmail
        );

        $email = \Ip\View::create('view/email.php', $emailData)->render();
        $from = $websiteEmail;

        $subject = $parametersMod->getValue('community', 'user', 'email_messages', 'subject_verify_registration');

        $files = array();
        $emailQueue = new \Modules\administrator\email_queue\Module();
        $emailQueue->addEmail($from, '', $to, '',  $subject, $email, false, true, $files);

        $emailQueue->send();
    }
    
    private function sendUpdateVerificationLink($to, $code, $userId) {
        global $parametersMod;
        global $site;
        
        
        $content = $parametersMod->getValue('community', 'user', 'email_messages', 'text_verify_new_email');
        $link = $site->generateUrl(null, null, array(), array("g" => "community", "m" => "user", "a" => "newEmailVerification", "id" => $userId, "code" => $code));
        $content = str_replace('[[link]]', '<a href="'.$link.'">'.$link.'</a>', $content);
        
        $websiteName = $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'name');
        $websiteEmail = $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'email');
        
        
        $emailData = array(
                    'content' => $content,
                    'name' => $websiteName,
                    'email' => $websiteEmail
        );
        
        $email = \Ip\View::create('view/email.php', $emailData)->render();
        $from = $websiteEmail;
        
        $subject = $parametersMod->getValue('community', 'user', 'email_messages', 'subject_verify_new_email');
        
        $files = array();
        $emailQueue = new \Modules\administrator\email_queue\Module();
        $emailQueue->addEmail($from, '', $to, '',  $subject, $email, false, true, $files);
        
        $emailQueue->send();

    }
    
    function sendPasswordResetLink($to, $code, $userId) {
        global $parametersMod;
        global $site;
    
        $emailQueue = new \Modules\administrator\email_queue\Module();
        
        $content = $parametersMod->getValue('community', 'user', 'email_messages', 'text_password_reset');
        $link = $site->generateUrl(null, null, array(), array("g" => "community", "m" => "user", "a" => "passwordResetVerification", "id" => $userId, "code" => $code));
        $content = str_replace('[[link]]', '<a href="'.$link.'">'.$link.'</a>', $content);
        
        $websiteName = $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'name');
        $websiteEmail = $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'email');
        
        
        $emailData = array(
            'content' => $content,
            'name' => $websiteName,
            'email' => $websiteEmail
        );
        
        $email = \Ip\View::create('view/email.php', $emailData)->render();
        $from = $websiteEmail;
        
        $subject = $parametersMod->getValue('community', 'user', 'email_messages', 'subject_password_reset');
        
        $files = array();
        $emailQueue = new \Modules\administrator\email_queue\Module();
        $emailQueue->addEmail($from, '', $to, '',  $subject, $email, false, true, $files);
        
        $emailQueue->send();

    }
    
    public function renewRegistration () {
        global $site;//userById
        global $dispatcher;
        if (isset($_GET['id']) && Db::userById($_GET['id'])) {
            if(Db::renewRegistration($_GET['id']) == 1){
                //deprecated event
                $site->dispatchEvent('community', 'user', 'renew_registration', array('user_id'=>$_GET['id']));
                //new event
                $data = array('userId' => $_GET['id']);
                $dispatcher->notify(new Event($this, Event::RENEW_REGISTRATION, $data));

                $this->redirect($site->generateUrl(null, $this->userZone->getName(), array(Config::$urlRenewedRegistration)));
            } else {
                $this->redirect($site->generateUrl(null, $this->userZone->getName(), array(Config::$urlRenewRegistrationError)));
            }
        } else {
            $this->redirect($site->generateUrl(null, $this->userZone->getName(), array(Config::$urlRenewRegistrationError)));
        }
    }
    
    



}
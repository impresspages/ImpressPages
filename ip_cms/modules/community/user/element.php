<?php
/**
 * @package ImpressPages
 *
 * @license see license.html
 */

namespace Modules\community\user;


if (!defined('CMS')) exit;


/**
 *
 *
 * @package ImpressPages
 */

class Element extends \Frontend\Element {

    public function __construct($id, $zoneName){
        global $parametersMod;
        parent::__construct($id, $zoneName);

        $this->buttonTitle = $id;

        switch($id){
            case 'password_reset':
                $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_password_reset');
                $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_password_reset');
                break;
            case 'password_reset_verified':
                $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_password_reset');
                $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_password_reset');
                break;
            case 'password_reset_verification_error':
                $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_password_reset');
                $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_password_reset');
                break;
            case 'password_reset_sent_text':
                $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_password_reset');
                $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_password_reset');
                break;
            case 'registration_verification_required':
                $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
                $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
                break;
            case 'new_email_verification_required':
                $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
                $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
                break;



            case 'registration_verified':
                $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
                $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
                break;
            case 'new_email_verified':
                $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_profile');
                $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_profile');
                break;
            case 'registration_verification_error':
                $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
                $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
                break;
            case 'new_email_verification_error':
                $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_new_email_verification_error');
                $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_new_email_verification_error');
                break;


            case 'login':
                $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_login');
                $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_login');
                break;
            case 'profile':
                $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_profile');
                $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_profile');
                break;
            case 'registration':
                $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
                $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
                break;
            case 'renewed_registration':
                $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
                $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
                break;
            case 'renew_registration_error':
                $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
                $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
                break;
            case 'verification_error_user_exist':
                $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
                $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
                break;
            case 'verification_error_email_exist':
                $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
                $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
                break;
            case 'logout':
                $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_profile');
                $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_profile');
                break;
        }

        $this->zoneName = $zoneName;
    }


    public function getLink() {
        global $site;
        return $site->generateUrl(null, $this->zoneName, array($this->id));
    }

    public function getDepth() {
        return 1;
    }



    public function generateContent() {
        global $site;
        global $session;
        global $parametersMod;

        $answer = '';

        $userZone = $site->getZone($this->zoneName);

        switch($this->getId()) {
            case 'login':
                return $userZone->generateLogin();
                break;
            case 'registration':
                return $userZone->generateRegistration();
                break;
            case 'registration_verification_required':
                $title = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
                $text = $parametersMod->getValue('community', 'user', 'translations', 'text_registration_verification_required');
                $answer .= \Ip\View::create('view/text.php', array('title' => $title, 'text' => $text))->render();
                break;
            case 'logout':
                $answer = '<script>document.location=\''.str_replace('&amp;','&',$userZone->getLinkLogout()).'\';</script>';
                echo $answer; exit;
                break;
            case 'verification_error_user_exist':
                $title = $parametersMod->getValue('community', 'user', 'translations', 'title_registration_verification_error');
                $text = $parametersMod->getValue('community', 'user', 'translations', 'text_user_exist_error');
                $answer .= \Ip\View::create('view/text.php', array('title' => $title, 'text' => $text))->render();
                break;
            case 'registration_verified':
                $answer .= \Ip\View::create('view/registration_verified.php')->render();
                break;
            case 'new_email_verified':
                if($session->loggedIn()) {
                    $answer .= '
                        <script type="text/javascript">document.location = \''.str_replace('&amp;','&',$userZone->getLinkProfile()).'\';</script>
                    ';
                }else {
                    $answer .= '
                        <script type="text/javascript">document.location = \''.str_replace('&amp;','&',$userZone->getLinkLogin()).'\';</script>
                    ';
                }
                break;
            case 'profile':
                if ($session->loggedIn()) {
                    return $userZone->generateProfile();
                } else {
                    return '<script type="text/javascript">document.location = \''.str_replace('&amp;','&',$userZone->getLinkLogin()).'\';</script>';
                }
                break;
            case 'new_email_verification_required':
                $title = $parametersMod->getValue('community', 'user', 'translations', 'title_profile');
                $text = $parametersMod->getValue('community', 'user', 'translations', 'text_new_email_verification_required');
                $answer .= \Ip\View::create('view/text.php', array('title' => $title, 'text' => $text))->render();
                break;
            case 'password_reset':
                if ($session->loggedIn()) {
                    if (isset($_SESSION['modules']['community']['user']['page_after_login'])) {
                        $answer .= '<script type="text/javascript">document.location = \''.$_SESSION['modules']['community']['user']['page_after_login'].'\';</script>';
                    } elseif($parametersMod->getValue('community', 'user', 'options', 'zone_after_login')) {
                        $answer .= '<script type="text/javascript">document.location = \''.$site->generateUrl(null, $parametersMod->getValue('community', 'user', 'options', 'zone_after_login')).'\';</script>';
                    } else {
                        $answer .= '<script type="text/javascript">document.location = \''.$site->generateUrl(null, $this->zoneName, array('profile')).'\';</script>';
                    }
                } else {
                    if($parametersMod->getValue('community', 'user', 'options', 'allow_password_reset')) {
                        $answer .= $userZone->generatePasswordReset();
                    } else {
                        $answer = '';
                    }
                }
                break;
            case 'password_reset_sent_text':
                $title = $parametersMod->getValue('community', 'user', 'translations', 'title_password_reset');
                $text = $parametersMod->getValue('community', 'user', 'translations', 'text_password_reset_sent');
                $answer .= \Ip\View::create('view/text.php', array('title' => $title, 'text' => $text))->render();
                break;
            case 'password_reset_verification_error':
                $title = $parametersMod->getValue('community', 'user', 'translations', 'title_password_reset');
                $text = $parametersMod->getValue('community', 'user', 'translations', 'text_password_verification_error');
                $answer .= \Ip\View::create('view/text.php', array('title' => $title, 'text' => $text))->render();
                break;
            case 'password_reset_verified':
                $answer .= \Ip\View::create('view/password_verified.php');
                break;
                if($parametersMod->getValue('community', 'user', 'options', 'allow_password_reset')) {
                    if($session->loggedIn()) {
                        if($parametersMod->getValue('community', 'user', 'options', 'zone_after_login'))
                        $answer .= '<script type="text/javascript">document.location = \''.$site->generateUrl(null, $parametersMod->getValue('community', 'user', 'options', 'zone_after_login')).'\';</script>';
                        else
                        $answer .= '<script type="text/javascript">document.location = \''.$site->generateUrl(null, $this->zoneName, array('profile')).'\';</script>';
                    } else {
                        $answer .= Template::passwordResetVerified($userZone->generateLogin());
                    }
                }
                break;
            case 'registration_verification_error':
                $title = $parametersMod->getValue('community', 'user', 'translations', 'title_registration_verification_error');
                $text = $parametersMod->getValue('community', 'user', 'translations', 'text_registration_verification_error');
                $answer .= \Ip\View::create('view/text.php', array('title' => $title, 'text' => $text))->render();
                break;
            case 'verification_error_email_exist':
                $title = $parametersMod->getValue('community', 'user', 'translations', 'title_registration_verification_error');
                $text = $parametersMod->getValue('community', 'user', 'translations', 'text_email_exist_error');
                $answer .= \Ip\View::create('view/text.php', array('title' => $title, 'text' => $text))->render();
                break;
            case 'new_email_verification_error':
                $title = $parametersMod->getValue('community', 'user', 'translations', 'title_new_email_verification_error');
                $text = $parametersMod->getValue('community', 'user', 'translations', 'text_new_email_verification_error');
                $answer .= \Ip\View::create('view/text.php', array('title' => $title, 'text' => $text))->render();
            break;
            case 'renewed_registration':
                $title = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
                $text = $parametersMod->getValue('community', 'user', 'translations', 'text_account_renewed');
                $answer .= \Ip\View::create('view/text.php', array('title' => $title, 'text' => $text))->render();
                break;
            case 'renew_registration_error':
                $title = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
                $text = $parametersMod->getValue('community', 'user', 'translations', 'text_account_renewal_error');
                $answer .= \Ip\View::create('view/text.php', array('title' => $title, 'text' => $text))->render();
                break;
        }


        return $answer;
    }



}





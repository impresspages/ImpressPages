<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Modules\community\user;



if (!defined('CMS')) exit;

global $site;

require_once('element.php');

global $site;

require_once \Ip\Config::oldModuleFile('community/user/config.php');

class Zone extends \Ip\Frontend\Zone {
    var $zoneName;
    function __construct($key) {
        $this->zoneName = $key;
    }



    /**
     * Finds all pages of current zone
     * @return array elements
     */
    public function getElements($language = null, $parentElementId = null, $startFrom = 0, $limit = null, $includeHidden = false, $reverseOrder = false) {
        if($parentElementId == null) {
            $elements = array();

            $elements[] = new Element('password_reset', $this->name);
            //$elements[] = new Element('password_reset_verified', $this->name);
            //$elements[] = new Element('password_reset_verification_error', $this->name);
            //$elements[] = new Element('password_reset_sent_text', $this->name);
            //$elements[] = new Element('registration_verification_required', $this->name);
            //$elements[] = new Element('new_email_verification_required', $this->name);
            //$elements[] = new Element('registration_verified', $this->name);
            //$elements[] = new Element('new_email_verified', $this->name);
            //$elements[] = new Element('registration_verification_error', $this->name);
            //$elements[] = new Element('new_email_verification_error', $this->name);
            $elements[] = new Element('login', $this->name);
            $elements[] = new Element('profile', $this->name);
            $elements[] = new Element('registration', $this->name);
            //$elements[] = new Element('renewed_registration', $this->name);
            //$elements[] = new Element('renew_registration_error', $this->name);
            //$elements[] = new Element('verification_error_user_exist', $this->name);
            //$elements[] = new Element('verification_error_email_exist', $this->name);


            return $elements;
        } else {
            return array();
        }

    }


    /**
     * @param int $elementId
     * @return array element
     */
    public  function getElement($elementId) {
        return new Element(null, $this->name); //default zone return element with all url and get variable combinations
    }

    /**
     * @param int $elementId
     * @return string link to specified element
     */
    public function generateUrl($elementId) {
    }


    /**
     * @param array $urlVars
     * @return array element
     */
    public function findElement($urlVars, $getVars) {


        global $site;
        global $parametersMod;
        if(sizeof($urlVars)> 0) {
            switch($urlVars[0]) {
                case Config::$urlPasswordReset:
                    if($parametersMod->getValue('community', 'user', 'options', 'allow_password_reset'))
                    return new Element('password_reset', $this->name);
                    break;
                case Config::$urlPasswordResetVerified:
                    if($parametersMod->getValue('community', 'user', 'options', 'allow_password_reset'))
                    return new Element('password_reset_verified', $this->name);
                    break;
                case Config::$urlPasswordResetVerificationError:
                    if($parametersMod->getValue('community', 'user', 'options', 'allow_password_reset'))
                    return new Element('password_reset_verification_error', $this->name);
                    break;
                case Config::$urlPasswordResetSentText:
                    if($parametersMod->getValue('community', 'user', 'options', 'allow_password_reset')) {
                        return new Element('password_reset_sent_text', $this->name);
                    }
                    break;
                case Config::$urlRegistrationVerificationRequired:
                    return new Element('registration_verification_required', $this->name);
                    break;
                case Config::$urlEmailVerificationRequired:
                    return new Element('new_email_verification_required', $this->name);
                    break;
                case Config::$urlRegistrationVerified:
                    return new Element('registration_verified', $this->name);
                    break;
                case Config::$urlNewEmailVerified:
                    return new Element('new_email_verified', $this->name);
                    break;
                case Config::$urlRegistrationVerificationError:
                    return new Element('registration_verification_error', $this->name);
                    break;
                case Config::$urlNewEmailVerificationError:
                    return new Element('new_email_verification_error', $this->name);
                    break;
                case Config::$urlLogin:
                    return new Element('login', $this->name);
                    break;
                case Config::$urlProfile:
                    return new Element('profile', $this->name);
                    break;
                case Config::$urlRegistration:
                    return new Element('registration', $this->name);
                    break;
                case Config::$urlRenewedRegistration:
                    return new Element('renewed_registration', $this->name);
                    break;
                case Config::$urlRenewRegistrationError:
                    return new Element('renew_registration_error', $this->name);
                    break;
                case Config::$urlVerificationErrorUserExist:
                    return new Element('verification_error_user_exist', $this->name);
                    break;
                case Config::$urlVerificationErrorEmailExist:
                    return new Element('verification_error_email_exist', $this->name);
                    break;
                case Config::$urlLogout:
                    return new Element('logout', $this->name);
                    break;
                default:
                    return false;
                    break;
            }
        } else {
            return new Element('login', $this->name);
        }


    }

    /**
     * @return string html form
     */
    public static function generateRegistration() {
        global $parametersMod;
        global $session;
        
        $data = array(
            'loggedIn' => $session->loggedIn()
        );
        
        return \Ip\View::create('view/registration.php', $data);
    }

    /**
     * @return string html form
     */
    public function generateLogin() {
        global $parametersMod;
        $data = array();
        
        if($parametersMod->getValue('community','user','options','allow_password_reset')) {
            $data['passwordResetLink'] = $this::getLinkPasswordReset();
        }

        if($parametersMod->getValue('community','user','options','registration_on_login_page') && $parametersMod->getValue('community','user','options','enable_registration')) {
            $data['registrationLink'] = $this::getLinkRegistration();
        }

        return \Ip\View::create('view/login.php', $data);
    }

    /**
     * @return string html form
     */
    public function generatePasswordReset() {
        global $session;
        $data = array(
            'loggedIn' => $session->loggedIn()
        );
        return \Ip\View::create('view/password_reset.php', $data);
    }


    /**
     * @return string html form
     */
    public function generateProfile() {
        global $parametersMod;
        global $site;
        global $session;

        $data = array(
            'loggedIn' => $session->loggedIn(),
            'justUpdated' => isset($_REQUEST['message']) && $_REQUEST['message'] = 'updated'
        );
        
        return \Ip\View::create('view/profile.php', $data);
        
    }

    /**
     * @return string link to registration page
     */
    public function getLinkRegistration() {
        global $site;
        return $site->generateUrl(null, $this->getName(), array(Config::$urlRegistration));

    }

    /**
     * @return string link to profile page
     */
    public function getLinkProfile() {
        global $site;
        return $site->generateUrl(null, $this->getName(), array(Config::$urlProfile));
    }

    /**
     * @return string link to login page
     */
    public  function getLinkLogin() {
        global $site;
        return $site->generateUrl(null, $this->getName(), array(Config::$urlLogin));

    }

    /**
     * @return string link to logout
     */
    public function getLinkLogout() {
        global $site;
        return $site->generateUrl(null, null, null, array('g' => 'community', 'm' => 'user', 'a'=>'logout'));
    }

    /**
     * @return string link to password reset service
     */
    public function getLinkPasswordReset() {
        global $site;
        return $site->generateUrl(null, $this->getName(), array(Config::$urlPasswordReset));
    }



}

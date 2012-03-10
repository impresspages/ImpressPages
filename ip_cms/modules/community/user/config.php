<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
namespace Modules\community\user;

if (!defined('CMS')) exit;

require_once(LIBRARY_DIR.'php/form/standard.php');
require_once(BASE_DIR.MODULE_DIR."community/user/db.php");

/**
 * User area (registration) configuration.
 * @package ImpressPages
 */

class Config{
    /** @var password hash salt*/
    public static $hashSalt = '';


    public static $urlPasswordReset = 'password-reset';
    public static $urlPasswordResetVerified = 'password-reset-verified';
    public static $urlPasswordResetVerificationError = 'password-reset-verification-error';
    public static $urlPasswordResetSentText = 'password-reset-sent-text';
    public static $urlRegistrationVerificationRequired = 'registration-verification-required';
    public static $urlEmailVerificationRequired = 'new-email-verification-required';
    public static $urlRegistrationVerified = 'registration-verified';
    public static $urlNewEmailVerified = 'new-email-verified';
    public static $urlRegistrationVerificationError = 'registration-verification-error';
    public static $urlNewEmailVerificationError = 'new-email-verification-error';
    public static $urlLogin = 'login';
    public static $urlProfile = 'profile';
    public static $urlRegistration = 'registration';
    public static $urlRenewedRegistration = 'renewed-registration';
    public static $urlRenewRegistrationError = 'renew-registration-error';
    public static $urlVerificationErrorUserExist = 'verification-error-user-exist';
    public static $urlVerificationErrorEmailExist = 'verification-error-email-exist';
    public static $autologinCookieName = 'ipAutologin';
    public static $autologinCookiePath = '/';


    public static function getRegistrationForm(){
        global $parametersMod;
        global $session;

        
        $form = new \Library\IpForm\Form();
        

        /*hidden fields (required)*/
        $field = new \Library\IpForm\Field\Hidden(
        array(
        'name' => 'a',
        'defaultValue' => 'login'
        ));
        $form->addField($field);

        $field = new \Library\IpForm\Field\Hidden(
        array(
        'name' => 'g',
        'defaultValue' => 'community'
        ));
        $form->addField($field);
        

        $field = new \Library\IpForm\Field\Hidden(
        array(
            'name' => 'm',
            'defaultValue' => 'user'
        ));
        $form->addField($field);

        /*predefined fields (required)*/
        if($parametersMod->getValue('community','user','options','login_type') == 'login'){
            $field = new \Library\IpForm\Field\Text(
            array(
                'name' => 'login',
                'label' => $parametersMod->getValue('community','user','translations','field_login')
            ));
            $field->addValidator('required');
            $form->addField($field);
        }

        $field = new \Library\IpForm\Field\Email(
        array(
            'name' => 'email',
            'label' => $parametersMod->getValue('community','user','translations','field_email')
        ));
        $field->addValidator('required');
        $form->addField($field);

        $field = new \Library\IpForm\Field\Password(
        array(
        'name' => 'password',
        'label' => $parametersMod->getValue('community','user','translations','field_password')
        ));
        $field->addValidator('required');
        $field->addAttribute('autocomplete', 'off');
        $form->addField($field);

        if($parametersMod->getValue('community','user','options','type_password_twice')){
            $field = new \Library\IpForm\Field\Password(
            array(
            'name' => 'confirm_password',
            'disableAutocomplete' => true,
            'label' => $parametersMod->getValue('community','user','translations','field_confirm_password')
            ));
            $field->addValidator('required');
            $field->addAttribute('autocomplete', 'off');
            $form->addField($field);
        }

        /*add your additional fields here*/
        

        //Submit button
        $field = new \Library\IpForm\Field\Submit(
        array(
        'name' => 'submit',
        'defaultValue' => $parametersMod->getValue('community', 'user', 'translations', 'button_register')
        ));
        $form->addField($field);
        
        
        
        return $form;
    }

    public static function getProfileFields(){
        global $parametersMod;
        global $session;

        $dbMod = new Db();

        $profileFields = array();

        /*hidden fields (required)*/
        $field = new \Library\IpForm\Field\Hidden(
        array(
        'name' => 'a',
        'defaultValue' => 'login'
        ));
        $form->addField($field);

        $field = new \Library\IpForm\Field\Hidden(
        array(
        'name' => 'g',
        'defaultValue' => 'community'
        ));
        $form->addField($field);
        

        $field = new \Library\IpForm\Field\Hidden(
        array(
            'name' => 'm',
            'defaultValue' => 'user'
        ));
        $form->addField($field);
        


        /*predefined fields (required)*/
        if($session->loggedIn()){ //security check
            $user = $dbMod->userById($session->userId());


            $field = new \Library\Php\Form\FieldEmail();
            $field->name = 'email';
            $field->caption = $parametersMod->getValue('community','user','translations','field_email');
            $field->required = true;
            $field->value = $user['email'];
            $profileFields[] = $field;



            $field = new \Library\Php\Form\FieldPassword();
            $field->name = 'password';
            $field->disableAutocomplete = true;
            $field->caption = $parametersMod->getValue('community','user','translations','field_password');
            $profileFields[] = $field;

            if($parametersMod->getValue('community','user','options','type_password_twice')){
                $field = new \Library\Php\Form\FieldPassword();
                $field->name = 'confirm_password';
                $field->disableAutocomplete = true;
                $field->caption = $parametersMod->getValue('community','user','translations','field_confirm_password');
                $profileFields[] = $field;
            }



        }

        /*add your additional fields*/


        return $profileFields;
    }


    
    public static function getLoginForm(){
        global $parametersMod;

        $form = new \Library\IpForm\Form();
        


        /*hidden fields (required)*/
        $field = new \Library\IpForm\Field\Hidden(
        array(
        'name' => 'a',
        'defaultValue' => 'login'
        ));
        $form->addField($field);

        $field = new \Library\IpForm\Field\Hidden(
        array(
        'name' => 'g',
        'defaultValue' => 'community'
        ));
        $form->addField($field);
        

        $field = new \Library\IpForm\Field\Hidden(
        array(
            'name' => 'm',
            'defaultValue' => 'user'
        ));
        $form->addField($field);

        /*predefined fields (required)*/
        if($parametersMod->getValue('community','user','options','login_type') == 'login'){
            $field = new \Library\IpForm\Field\Text(
            array(
                'name' => 'login',
                'label' => $parametersMod->getValue('community','user','translations','field_login')
            ));
            $field->addValidator('required');
            $form->addField($field);
        }else{
            $field = new \Library\IpForm\Field\Email(
            array(
                'name' => 'email',
                'label' => $parametersMod->getValue('community','user','translations','field_email')
            ));
            $field->addValidator('required');
            $form->addField($field);
        }


        $field = new \Library\IpForm\Field\Password(
        array(
        'name' => 'password',
        'label' => $parametersMod->getValue('community','user','translations','field_password')
        ));
        $field->addValidator('required');
        $form->addField($field);

        if($parametersMod->getValue('community','user','options','enable_autologin')){
            $field = new \Library\IpForm\Field\Checkbox(
            array(
            'name' => 'autologin',
            'label' => $parametersMod->getValue('community','user','translations','autologin')
            ));
            $form->addField($field);
        }

        /*add your additional fields*/


        //Submit button
        $field = new \Library\IpForm\Field\Submit(
        array(
        'name' => 'submit',
        'defaultValue' => $parametersMod->getValue('community', 'user', 'translations', 'button_login')
        ));
        $form->addField($field);
        
        
        
        return $form;
    }

    /** @var array fields, that are used for user password reset */
    public static function getPasswordResetFields(){
        /** private*/
        global $parametersMod;
        /** private*/
        global $session;

        $passwordResetFields = array();

        /*hidden fields (required)*/
        $field = new \Library\Php\Form\FieldHidden();
        $field->name = 'action';
        $field->value = 'password_reset';
        $passwordResetFields[] = $field;

        $field = new \Library\Php\Form\FieldHidden();
        $field->name = 'module_group';
        $field->value = 'community';
        $passwordResetFields[] = $field;

        $field = new \Library\Php\Form\FieldHidden();
        $field->name = 'module_name';
        $field->value = 'user';
        $passwordResetFields[] = $field;

        /*predefined fields (required)*/

        $field = new \Library\Php\Form\FieldEmail();
        $field->db_field = 'email';
        $field->name = 'email';
        $field->caption = $parametersMod->getValue('community','user','translations','field_email');
        $field->required = true;
        $passwordResetFields[] = $field;


        $field = new \Library\Php\Form\FieldPassword();
        $field->name = 'password';
        $field->caption = $parametersMod->getValue('community','user','translations','field_password');
        $field->required = true;
        $passwordResetFields[]  = $field;

        if($parametersMod->getValue('community','user','options','type_password_twice')){
            $field = new \Library\Php\Form\FieldPassword();
            $field->name = 'confirm_password';
            $field->required = true;
            $field->caption = $parametersMod->getValue('community','user','translations','field_confirm_password');
            $passwordResetFields[]  = $field;
        }

        /*you are able to add additional fields, but i think you don't need to*/

        return $passwordResetFields;
    }

    public static function getCookieDomain() {
        if ($_SERVER['HTTP_HOST'] != 'localhost'){
            return $_SERVER['HTTP_HOST'];
        } else {
            return false;
        }
    }

}

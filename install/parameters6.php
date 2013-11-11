<?php
//language description
$languageCode = "en"; //RFC 4646 code
$languageShort = "EN"; //Short description
$languageLong = "English"; //Long title
$languageUrl = "en";



$moduleGroupTitle["community"] = "Community";
$moduleTitle["community"]["newsletter"] = "Newsletter";

    $parameterGroupTitle["community"]["newsletter"]["admin_translations"] = "Admin translations";
    $parameterGroupAdmin["community"]["newsletter"]["admin_translations"] = "1";

        $parameterTitle["community"]["newsletter"]["admin_translations"]["subject"] = "Subject";
        $parameterValue["community"]["newsletter"]["admin_translations"]["subject"] = "E-mail subject";
        $parameterAdmin["community"]["newsletter"]["admin_translations"]["subject"] = "1";
        $parameterType["community"]["newsletter"]["admin_translations"]["subject"] = "string";

        $parameterTitle["community"]["newsletter"]["admin_translations"]["text"] = "Text";
        $parameterValue["community"]["newsletter"]["admin_translations"]["text"] = "Text";
        $parameterAdmin["community"]["newsletter"]["admin_translations"]["text"] = "1";
        $parameterType["community"]["newsletter"]["admin_translations"]["text"] = "string";

        $parameterTitle["community"]["newsletter"]["admin_translations"]["newsletter"] = "Newsletter";
        $parameterValue["community"]["newsletter"]["admin_translations"]["newsletter"] = "Newsletter";
        $parameterAdmin["community"]["newsletter"]["admin_translations"]["newsletter"] = "1";
        $parameterType["community"]["newsletter"]["admin_translations"]["newsletter"] = "string";

        $parameterTitle["community"]["newsletter"]["admin_translations"]["send"] = "Send";
        $parameterValue["community"]["newsletter"]["admin_translations"]["send"] = "Send";
        $parameterAdmin["community"]["newsletter"]["admin_translations"]["send"] = "1";
        $parameterType["community"]["newsletter"]["admin_translations"]["send"] = "string";

        $parameterTitle["community"]["newsletter"]["admin_translations"]["preview"] = "Preview";
        $parameterValue["community"]["newsletter"]["admin_translations"]["preview"] = "Preview";
        $parameterAdmin["community"]["newsletter"]["admin_translations"]["preview"] = "1";
        $parameterType["community"]["newsletter"]["admin_translations"]["preview"] = "string";

        $parameterTitle["community"]["newsletter"]["admin_translations"]["was_sent"] = "Was sent";
        $parameterValue["community"]["newsletter"]["admin_translations"]["was_sent"] = "Newsletter was sent successfully";
        $parameterAdmin["community"]["newsletter"]["admin_translations"]["was_sent"] = "1";
        $parameterType["community"]["newsletter"]["admin_translations"]["was_sent"] = "string";

        $parameterTitle["community"]["newsletter"]["admin_translations"]["test_email_send"] = "Test e-mail send";
        $parameterValue["community"]["newsletter"]["admin_translations"]["test_email_send"] = "Test e-mail was successfully sent";
        $parameterAdmin["community"]["newsletter"]["admin_translations"]["test_email_send"] = "1";
        $parameterType["community"]["newsletter"]["admin_translations"]["test_email_send"] = "string";

        $parameterTitle["community"]["newsletter"]["admin_translations"]["send_or_not_question"] = "Send or not question.";
        $parameterValue["community"]["newsletter"]["admin_translations"]["send_or_not_question"] = "Send newsletter?";
        $parameterAdmin["community"]["newsletter"]["admin_translations"]["send_or_not_question"] = "1";
        $parameterType["community"]["newsletter"]["admin_translations"]["send_or_not_question"] = "string";

        $parameterTitle["community"]["newsletter"]["admin_translations"]["where_to_send"] = "Where to send?";
        $parameterValue["community"]["newsletter"]["admin_translations"]["where_to_send"] = "Send test e-mail to: ";
        $parameterAdmin["community"]["newsletter"]["admin_translations"]["where_to_send"] = "1";
        $parameterType["community"]["newsletter"]["admin_translations"]["where_to_send"] = "string";

    $parameterGroupTitle["community"]["newsletter"]["options"] = "Options";
    $parameterGroupAdmin["community"]["newsletter"]["options"] = "0";

        $parameterTitle["community"]["newsletter"]["options"]["show_unsubscribe_button"] = "Show unsubscribe button";
        $parameterValue["community"]["newsletter"]["options"]["show_unsubscribe_button"] = "0";
        $parameterAdmin["community"]["newsletter"]["options"]["show_unsubscribe_button"] = "0";
        $parameterType["community"]["newsletter"]["options"]["show_unsubscribe_button"] = "bool";

    $parameterGroupTitle["community"]["newsletter"]["subscription_translations"] = "Translations";
    $parameterGroupAdmin["community"]["newsletter"]["subscription_translations"] = "0";

        $parameterTitle["community"]["newsletter"]["subscription_translations"]["text_incorrect_email"] = "Incorrect e-mail";
        $parameterValue["community"]["newsletter"]["subscription_translations"]["text_incorrect_email"] = "Incorrect e-mail address";
        $parameterAdmin["community"]["newsletter"]["subscription_translations"]["text_incorrect_email"] = "0";
        $parameterType["community"]["newsletter"]["subscription_translations"]["text_incorrect_email"] = "lang";

        $parameterTitle["community"]["newsletter"]["subscription_translations"]["text_subscribed"] = "Text subscribed";
        $parameterValue["community"]["newsletter"]["subscription_translations"]["text_subscribed"] = "<p>You have successfully subscribed to our newsletter.</p>";
        $parameterAdmin["community"]["newsletter"]["subscription_translations"]["text_subscribed"] = "0";
        $parameterType["community"]["newsletter"]["subscription_translations"]["text_subscribed"] = "lang_wysiwyg";

        $parameterTitle["community"]["newsletter"]["subscription_translations"]["text_unsubscribed"] = "Text unsubscribed";
        $parameterValue["community"]["newsletter"]["subscription_translations"]["text_unsubscribed"] = "<p>Your e-mail address has been removed from our database.</p>";
        $parameterAdmin["community"]["newsletter"]["subscription_translations"]["text_unsubscribed"] = "0";
        $parameterType["community"]["newsletter"]["subscription_translations"]["text_unsubscribed"] = "lang_wysiwyg";

        $parameterTitle["community"]["newsletter"]["subscription_translations"]["text_error_cant_unsubscribe"] = "Text error can't unsubscribe";
        $parameterValue["community"]["newsletter"]["subscription_translations"]["text_error_cant_unsubscribe"] = "<p>Specified e-mail address does not exist in database.</p>";
        $parameterAdmin["community"]["newsletter"]["subscription_translations"]["text_error_cant_unsubscribe"] = "0";
        $parameterType["community"]["newsletter"]["subscription_translations"]["text_error_cant_unsubscribe"] = "lang_wysiwyg";

        $parameterTitle["community"]["newsletter"]["subscription_translations"]["text_confirmation"] = "Text confirmation";
        $parameterValue["community"]["newsletter"]["subscription_translations"]["text_confirmation"] = "<p>Thank you for your registration.</p>
<p>The confirmation e-mail has been sent to your e-mail address. Please confirm the registration by clicking on the link in the e-mail.</p>";
        $parameterAdmin["community"]["newsletter"]["subscription_translations"]["text_confirmation"] = "0";
        $parameterType["community"]["newsletter"]["subscription_translations"]["text_confirmation"] = "lang_wysiwyg";

        $parameterTitle["community"]["newsletter"]["subscription_translations"]["text_email_confirmation"] = "Text  e-mail confirmation";
        $parameterValue["community"]["newsletter"]["subscription_translations"]["text_email_confirmation"] = "<p><strong>Thank you for subscribing to our newsletter!</strong></p>
<p>To complete your registration, you need to confirm that you have received this e-mail by clicking on the link below:</p>
<p>[[link]]</p>
<p>If the link does not work, just copy and paste the entire link into your browser.</p>";
        $parameterAdmin["community"]["newsletter"]["subscription_translations"]["text_email_confirmation"] = "0";
        $parameterType["community"]["newsletter"]["subscription_translations"]["text_email_confirmation"] = "lang_wysiwyg";

        $parameterTitle["community"]["newsletter"]["subscription_translations"]["text_error_confirmation"] = "Text error confirmation";
        $parameterValue["community"]["newsletter"]["subscription_translations"]["text_error_confirmation"] = "<p>Incorrect confirmation link.</p>";
        $parameterAdmin["community"]["newsletter"]["subscription_translations"]["text_error_confirmation"] = "0";
        $parameterType["community"]["newsletter"]["subscription_translations"]["text_error_confirmation"] = "lang_wysiwyg";

        $parameterTitle["community"]["newsletter"]["subscription_translations"]["subject_confirmation"] = "Subject confirmation";
        $parameterValue["community"]["newsletter"]["subscription_translations"]["subject_confirmation"] = "Newsletter e-mail confirmation ";
        $parameterAdmin["community"]["newsletter"]["subscription_translations"]["subject_confirmation"] = "0";
        $parameterType["community"]["newsletter"]["subscription_translations"]["subject_confirmation"] = "lang";

        $parameterTitle["community"]["newsletter"]["subscription_translations"]["subscribe"] = "Subscribe";
        $parameterValue["community"]["newsletter"]["subscription_translations"]["subscribe"] = "Subscribe";
        $parameterAdmin["community"]["newsletter"]["subscription_translations"]["subscribe"] = "0";
        $parameterType["community"]["newsletter"]["subscription_translations"]["subscribe"] = "lang";

        $parameterTitle["community"]["newsletter"]["subscription_translations"]["newsletter"] = "Newsletter";
        $parameterValue["community"]["newsletter"]["subscription_translations"]["newsletter"] = "Newsletter";
        $parameterAdmin["community"]["newsletter"]["subscription_translations"]["newsletter"] = "0";
        $parameterType["community"]["newsletter"]["subscription_translations"]["newsletter"] = "lang";

        $parameterTitle["community"]["newsletter"]["subscription_translations"]["unsubscribe"] = "Unsubscribe";
        $parameterValue["community"]["newsletter"]["subscription_translations"]["unsubscribe"] = "Unsubscribe";
        $parameterAdmin["community"]["newsletter"]["subscription_translations"]["unsubscribe"] = "0";
        $parameterType["community"]["newsletter"]["subscription_translations"]["unsubscribe"] = "lang";

        $parameterTitle["community"]["newsletter"]["subscription_translations"]["label"] = "Input label";
        $parameterValue["community"]["newsletter"]["subscription_translations"]["label"] = "Enter e-mail address";
        $parameterAdmin["community"]["newsletter"]["subscription_translations"]["label"] = "0";
        $parameterType["community"]["newsletter"]["subscription_translations"]["label"] = "lang";

        $parameterTitle["community"]["newsletter_subscribers"]["admin_translations"]["email"] = "E-mail";
        $parameterValue["community"]["newsletter_subscribers"]["admin_translations"]["email"] = "E-mail";
        $parameterAdmin["community"]["newsletter_subscribers"]["admin_translations"]["email"] = "1";
        $parameterType["community"]["newsletter_subscribers"]["admin_translations"]["email"] = "string";

        $parameterTitle["community"]["newsletter_subscribers"]["admin_translations"]["verified"] = "Verified";
        $parameterValue["community"]["newsletter_subscribers"]["admin_translations"]["verified"] = "Verified";
        $parameterAdmin["community"]["newsletter_subscribers"]["admin_translations"]["verified"] = "1";
        $parameterType["community"]["newsletter_subscribers"]["admin_translations"]["verified"] = "string";

        $parameterTitle["community"]["newsletter_subscribers"]["admin_translations"]["newsletter_subscribers"] = "Newsletter subscribers";
        $parameterValue["community"]["newsletter_subscribers"]["admin_translations"]["newsletter_subscribers"] = "Newsletter subscribers";
        $parameterAdmin["community"]["newsletter_subscribers"]["admin_translations"]["newsletter_subscribers"] = "1";
        $parameterType["community"]["newsletter_subscribers"]["admin_translations"]["newsletter_subscribers"] = "string";

        $parameterTitle["community"]["newsletter_subscribers"]["admin_translations"]["error_registered"] = "Error registered";
        $parameterValue["community"]["newsletter_subscribers"]["admin_translations"]["error_registered"] = "This e-mail already registered";
        $parameterAdmin["community"]["newsletter_subscribers"]["admin_translations"]["error_registered"] = "1";
        $parameterType["community"]["newsletter_subscribers"]["admin_translations"]["error_registered"] = "string";

        $parameterTitle["community"]["newsletter_subscribers"]["admin_translations"]["error_email"] = "Error e-mail";
        $parameterValue["community"]["newsletter_subscribers"]["admin_translations"]["error_email"] = "Incorrect e-mail address";
        $parameterAdmin["community"]["newsletter_subscribers"]["admin_translations"]["error_email"] = "0";
        $parameterType["community"]["newsletter_subscribers"]["admin_translations"]["error_email"] = "string";

        $parameterTitle["community"]["user"]["admin_translations"]["login"] = "Login";
        $parameterValue["community"]["user"]["admin_translations"]["login"] = "Login";
        $parameterAdmin["community"]["user"]["admin_translations"]["login"] = "0";
        $parameterType["community"]["user"]["admin_translations"]["login"] = "string";

        $parameterTitle["community"]["user"]["admin_translations"]["email"] = "E-mail";
        $parameterValue["community"]["user"]["admin_translations"]["email"] = "E-mail";
        $parameterAdmin["community"]["user"]["admin_translations"]["email"] = "0";
        $parameterType["community"]["user"]["admin_translations"]["email"] = "string";

        $parameterTitle["community"]["user"]["admin_translations"]["password"] = "Password";
        $parameterValue["community"]["user"]["admin_translations"]["password"] = "Password";
        $parameterAdmin["community"]["user"]["admin_translations"]["password"] = "0";
        $parameterType["community"]["user"]["admin_translations"]["password"] = "string";

        $parameterTitle["community"]["user"]["admin_translations"]["verified"] = "Verified";
        $parameterValue["community"]["user"]["admin_translations"]["verified"] = "Verified";
        $parameterAdmin["community"]["user"]["admin_translations"]["verified"] = "0";
        $parameterType["community"]["user"]["admin_translations"]["verified"] = "string";

        $parameterTitle["community"]["user"]["admin_translations"]["created_on"] = "Created on";
        $parameterValue["community"]["user"]["admin_translations"]["created_on"] = "Created on";
        $parameterAdmin["community"]["user"]["admin_translations"]["created_on"] = "0";
        $parameterType["community"]["user"]["admin_translations"]["created_on"] = "string";

        $parameterTitle["community"]["user"]["admin_translations"]["warned_on"] = "Warned on";
        $parameterValue["community"]["user"]["admin_translations"]["warned_on"] = "Warned on";
        $parameterAdmin["community"]["user"]["admin_translations"]["warned_on"] = "0";
        $parameterType["community"]["user"]["admin_translations"]["warned_on"] = "string";

        $parameterTitle["community"]["user"]["admin_translations"]["last_login"] = "Last login";
        $parameterValue["community"]["user"]["admin_translations"]["last_login"] = "Last login";
        $parameterAdmin["community"]["user"]["admin_translations"]["last_login"] = "0";
        $parameterType["community"]["user"]["admin_translations"]["last_login"] = "string";

        $parameterTitle["community"]["user"]["admin_translations"]["user"] = "User";
        $parameterValue["community"]["user"]["admin_translations"]["user"] = "User";
        $parameterAdmin["community"]["user"]["admin_translations"]["user"] = "0";
        $parameterType["community"]["user"]["admin_translations"]["user"] = "string";

        $parameterTitle["community"]["user"]["admin_translations"]["error_email"] = "Error e-mail";
        $parameterValue["community"]["user"]["admin_translations"]["error_email"] = "Incorrect e-mail address";
        $parameterAdmin["community"]["user"]["admin_translations"]["error_email"] = "0";
        $parameterType["community"]["user"]["admin_translations"]["error_email"] = "string";

        $parameterTitle["community"]["user"]["admin_translations"]["registration"] = "Registration";
        $parameterValue["community"]["user"]["admin_translations"]["registration"] = "Registration";
        $parameterAdmin["community"]["user"]["admin_translations"]["registration"] = "0";
        $parameterType["community"]["user"]["admin_translations"]["registrartion"] = "string";
        
    $parameterGroupTitle["community"]["user"]["email_messages"] = "E-mail messages";
    $parameterGroupAdmin["community"]["user"]["email_messages"] = "0";

        $parameterTitle["community"]["user"]["email_messages"]["subject_verify_new_email"] = "Subject - verify new e-mail";
        $parameterValue["community"]["user"]["email_messages"]["subject_verify_new_email"] = "Updated profile verification";
        $parameterAdmin["community"]["user"]["email_messages"]["subject_verify_new_email"] = "0";
        $parameterType["community"]["user"]["email_messages"]["subject_verify_new_email"] = "lang";

        $parameterTitle["community"]["user"]["email_messages"]["subject_verify_registration"] = "Subject - verify registration";
        $parameterValue["community"]["user"]["email_messages"]["subject_verify_registration"] = "Registration";
        $parameterAdmin["community"]["user"]["email_messages"]["subject_verify_registration"] = "0";
        $parameterType["community"]["user"]["email_messages"]["subject_verify_registration"] = "lang";

        $parameterTitle["community"]["user"]["email_messages"]["subject_user_deleted"] = "Subject - user deleted";
        $parameterValue["community"]["user"]["email_messages"]["subject_user_deleted"] = "User deleted";
        $parameterAdmin["community"]["user"]["email_messages"]["subject_user_deleted"] = "0";
        $parameterType["community"]["user"]["email_messages"]["subject_user_deleted"] = "lang";

        $parameterTitle["community"]["user"]["email_messages"]["subject_password_reset"] = "Subject - password reset";
        $parameterValue["community"]["user"]["email_messages"]["subject_password_reset"] = "Password reset instructions";
        $parameterAdmin["community"]["user"]["email_messages"]["subject_password_reset"] = "0";
        $parameterType["community"]["user"]["email_messages"]["subject_password_reset"] = "lang";

        $parameterTitle["community"]["user"]["email_messages"]["subject_account_will_expire"] = "Subject - account will expire";
        $parameterValue["community"]["user"]["email_messages"]["subject_account_will_expire"] = "Account information renewal";
        $parameterAdmin["community"]["user"]["email_messages"]["subject_account_will_expire"] = "0";
        $parameterType["community"]["user"]["email_messages"]["subject_account_will_expire"] = "lang";

        $parameterTitle["community"]["user"]["email_messages"]["text_verify_registration"] = "Text - verify registration";
        $parameterValue["community"]["user"]["email_messages"]["text_verify_registration"] = "<p><strong>Thank you for registering!</strong></p>
<p>To complete your registration you need to confirm that you have received this e-mail by clicking on the link below:</p>
<p>[[link]]</p>
<p>If the link does not work, just copy and paste the entire link into your browser.</p>";
        $parameterAdmin["community"]["user"]["email_messages"]["text_verify_registration"] = "0";
        $parameterType["community"]["user"]["email_messages"]["text_verify_registration"] = "lang_wysiwyg";

        $parameterTitle["community"]["user"]["email_messages"]["text_verify_new_email"] = "Text - verify new e-mail";
        $parameterValue["community"]["user"]["email_messages"]["text_verify_new_email"] = "<p>Your e-mail has been changed. To complete the update process of your profile you need to confirm that you have received this e-mail by clicking on the link below:</p>
<p>[[link]]</p>
<p>If the link does not work, just copy and paste the entire link into your browser.</p>";
        $parameterAdmin["community"]["user"]["email_messages"]["text_verify_new_email"] = "0";
        $parameterType["community"]["user"]["email_messages"]["text_verify_new_email"] = "lang_wysiwyg";

        $parameterTitle["community"]["user"]["email_messages"]["text_user_deleted"] = "Text - user deleted";
        $parameterValue["community"]["user"]["email_messages"]["text_user_deleted"] = "<p>Your account has been deleted.</p>";
        $parameterAdmin["community"]["user"]["email_messages"]["text_user_deleted"] = "0";
        $parameterType["community"]["user"]["email_messages"]["text_user_deleted"] = "lang_wysiwyg";

        $parameterTitle["community"]["user"]["email_messages"]["text_account_will_expire"] = "Text - account will expire";
        $parameterValue["community"]["user"]["email_messages"]["text_account_will_expire"] = "<p>Your account is due to expire at [[date]].</p>
<p>Please visit the website and log in or click the link bellow to renew your account:</p>
<p>[[link]]</p>
<p>&nbsp;</p>
<p>If you take no action until [[date]] your account will be deleted.</p>";
        $parameterAdmin["community"]["user"]["email_messages"]["text_account_will_expire"] = "0";
        $parameterType["community"]["user"]["email_messages"]["text_account_will_expire"] = "lang_wysiwyg";

        $parameterTitle["community"]["user"]["email_messages"]["text_password_reset"] = "Text - password reset confirm";
        $parameterValue["community"]["user"]["email_messages"]["text_password_reset"] = "<p>You asked to reset your password. Please press the link bellow to confirm this action:</p>
<p>[[link]]</p>
<p>If the link does not work, just copy and paste the entire link into your browser.</p>";
        $parameterAdmin["community"]["user"]["email_messages"]["text_password_reset"] = "0";
        $parameterType["community"]["user"]["email_messages"]["text_password_reset"] = "lang_wysiwyg";

    $parameterGroupTitle["community"]["user"]["errors"] = "Errors";
    $parameterGroupAdmin["community"]["user"]["errors"] = "0";

        $parameterTitle["community"]["user"]["errors"]["already_registered"] = "Already registered";
        $parameterValue["community"]["user"]["errors"]["already_registered"] = "Already registered";
        $parameterAdmin["community"]["user"]["errors"]["already_registered"] = "0";
        $parameterType["community"]["user"]["errors"]["already_registered"] = "lang";

        $parameterTitle["community"]["user"]["errors"]["passwords_dont_match"] = "Passwords don't match";
        $parameterValue["community"]["user"]["errors"]["passwords_dont_match"] = "Passwords don't match";
        $parameterAdmin["community"]["user"]["errors"]["passwords_dont_match"] = "0";
        $parameterType["community"]["user"]["errors"]["passwords_dont_match"] = "lang";

        $parameterTitle["community"]["user"]["errors"]["email_doesnt_exist"] = "E-mail doesn't exist";
        $parameterValue["community"]["user"]["errors"]["email_doesnt_exist"] = "Specified e-mail address doesn't exist.";
        $parameterAdmin["community"]["user"]["errors"]["email_doesnt_exist"] = "0";
        $parameterType["community"]["user"]["errors"]["email_doesnt_exist"] = "lang";

        $parameterTitle["community"]["user"]["errors"]["incorrect_email_data"] = "Incorrect e-mail data";
        $parameterValue["community"]["user"]["errors"]["incorrect_email_data"] = "Incorrect e-mail address or password";
        $parameterAdmin["community"]["user"]["errors"]["incorrect_email_data"] = "0";
        $parameterType["community"]["user"]["errors"]["incorrect_email_data"] = "lang";

        $parameterTitle["community"]["user"]["errors"]["incorrect_login_data"] = "Incorrect login data";
        $parameterValue["community"]["user"]["errors"]["incorrect_login_data"] = "Incorrect user name or password";
        $parameterAdmin["community"]["user"]["errors"]["incorrect_login_data"] = "0";
        $parameterType["community"]["user"]["errors"]["incorrect_login_data"] = "lang";

        $parameterTitle["community"]["user"]["options"]["login_type"] = "Login type (login, e-mail)";
        $parameterValue["community"]["user"]["options"]["login_type"] = "login";
        $parameterAdmin["community"]["user"]["options"]["login_type"] = "0";
        $parameterType["community"]["user"]["options"]["login_type"] = "string";

        $parameterTitle["community"]["user"]["options"]["zone_after_login"] = "Redirect to zone after login (enter zone name)";
        $parameterValue["community"]["user"]["options"]["zone_after_login"] = "";
        $parameterAdmin["community"]["user"]["options"]["zone_after_login"] = "0";
        $parameterType["community"]["user"]["options"]["zone_after_login"] = "string";

        $parameterTitle["community"]["user"]["options"]["expires_in"] = "User account expires in (months)";
        $parameterValue["community"]["user"]["options"]["expires_in"] = "24";
        $parameterAdmin["community"]["user"]["options"]["expires_in"] = "0";
        $parameterType["community"]["user"]["options"]["expires_in"] = "integer";

        $parameterTitle["community"]["user"]["options"]["warn_before"] = "Warn user about deletion before (days)";
        $parameterValue["community"]["user"]["options"]["warn_before"] = "30";
        $parameterAdmin["community"]["user"]["options"]["warn_before"] = "0";
        $parameterType["community"]["user"]["options"]["warn_before"] = "integer";

        $parameterTitle["community"]["user"]["options"]["warn_every"] = "Warn user about deletion every (days)";
        $parameterValue["community"]["user"]["options"]["warn_every"] = "9";
        $parameterAdmin["community"]["user"]["options"]["warn_every"] = "0";
        $parameterType["community"]["user"]["options"]["warn_every"] = "integer";

        $parameterTitle["community"]["user"]["options"]["autologin_time"] = "Autologin time (days)";
        $parameterValue["community"]["user"]["options"]["autologin_time"] = "300";
        $parameterAdmin["community"]["user"]["options"]["autologin_time"] = "1";
        $parameterType["community"]["user"]["options"]["autologin_time"] = "integer";

        $parameterTitle["community"]["user"]["options"]["delete_expired_users"] = "Delete expired user";
        $parameterValue["community"]["user"]["options"]["delete_expired_users"] = "0";
        $parameterAdmin["community"]["user"]["options"]["delete_expired_users"] = "0";
        $parameterType["community"]["user"]["options"]["delete_expired_users"] = "bool";

        $parameterTitle["community"]["user"]["options"]["type_password_twice"] = "Type password twice";
        $parameterValue["community"]["user"]["options"]["type_password_twice"] = "1";
        $parameterAdmin["community"]["user"]["options"]["type_password_twice"] = "0";
        $parameterType["community"]["user"]["options"]["type_password_twice"] = "bool";

        $parameterTitle["community"]["user"]["options"]["registration_on_login_page"] = "Registration link on login page";
        $parameterValue["community"]["user"]["options"]["registration_on_login_page"] = "1";
        $parameterAdmin["community"]["user"]["options"]["registration_on_login_page"] = "0";
        $parameterType["community"]["user"]["options"]["registration_on_login_page"] = "bool";

        $parameterTitle["community"]["user"]["options"]["allow_password_reset"] = "Allow password reset";
        $parameterValue["community"]["user"]["options"]["allow_password_reset"] = "1";
        $parameterAdmin["community"]["user"]["options"]["allow_password_reset"] = "0";
        $parameterType["community"]["user"]["options"]["allow_password_reset"] = "bool";

        $parameterTitle["community"]["user"]["options"]["enable_registration"] = "Enable registration";
        $parameterValue["community"]["user"]["options"]["enable_registration"] = "1";
        $parameterAdmin["community"]["user"]["options"]["enable_registration"] = "0";
        $parameterType["community"]["user"]["options"]["enable_registration"] = "bool";

        $parameterTitle["community"]["user"]["options"]["encrypt_passwords"] = "Encrypt passwords";
        $parameterValue["community"]["user"]["options"]["encrypt_passwords"] = "1";
        $parameterAdmin["community"]["user"]["options"]["encrypt_passwords"] = "0";
        $parameterType["community"]["user"]["options"]["encrypt_passwords"] = "bool";

        $parameterTitle["community"]["user"]["options"]["enable_autologin"] = "Enable autologin";
        $parameterValue["community"]["user"]["options"]["enable_autologin"] = "1";
        $parameterAdmin["community"]["user"]["options"]["enable_autologin"] = "1";
        $parameterType["community"]["user"]["options"]["enable_autologin"] = "bool";

        $parameterTitle["community"]["user"]["options"]["require_email_confirmation"] = "Require email confirmation";
        $parameterValue["community"]["user"]["options"]["require_email_confirmation"] = "1";
        $parameterAdmin["community"]["user"]["options"]["require_email_confirmation"] = "1";
        $parameterType["community"]["user"]["options"]["require_email_confirmation"] = "bool";

        $parameterTitle["community"]["user"]["options"]["autologin_after_registration"] = "Autologin after registration";
        $parameterValue["community"]["user"]["options"]["autologin_after_registration"] = "1";
        $parameterAdmin["community"]["user"]["options"]["autologin_after_registration"] = "1";
        $parameterType["community"]["user"]["options"]["autologin_after_registration"] = "bool";

    $parameterGroupTitle["community"]["user"]["translations"] = "Translations";
    $parameterGroupAdmin["community"]["user"]["translations"] = "0";

        $parameterTitle["community"]["user"]["translations"]["autologin"] = "Autologin";
        $parameterValue["community"]["user"]["translations"]["autologin"] = "Remember me";
        $parameterAdmin["community"]["user"]["translations"]["autologin"] = "0";
        $parameterType["community"]["user"]["translations"]["autologin"] = "string";

        $parameterTitle["community"]["user"]["translations"]["field_email"] = "Field - e-mail";
        $parameterValue["community"]["user"]["translations"]["field_email"] = "E-mail";
        $parameterAdmin["community"]["user"]["translations"]["field_email"] = "0";
        $parameterType["community"]["user"]["translations"]["field_email"] = "lang";

        $parameterTitle["community"]["user"]["translations"]["field_password"] = "Field - password";
        $parameterValue["community"]["user"]["translations"]["field_password"] = "Password";
        $parameterAdmin["community"]["user"]["translations"]["field_password"] = "0";
        $parameterType["community"]["user"]["translations"]["field_password"] = "lang";

        $parameterTitle["community"]["user"]["translations"]["field_confirm_password"] = "Field - confirm password";
        $parameterValue["community"]["user"]["translations"]["field_confirm_password"] = "Confirm Password";
        $parameterAdmin["community"]["user"]["translations"]["field_confirm_password"] = "0";
        $parameterType["community"]["user"]["translations"]["field_confirm_password"] = "lang";

        $parameterTitle["community"]["user"]["translations"]["field_login"] = "Field - login";
        $parameterValue["community"]["user"]["translations"]["field_login"] = "User name";
        $parameterAdmin["community"]["user"]["translations"]["field_login"] = "0";
        $parameterType["community"]["user"]["translations"]["field_login"] = "lang";

        $parameterTitle["community"]["user"]["translations"]["button_register"] = "Button - registration";
        $parameterValue["community"]["user"]["translations"]["button_register"] = "Register";
        $parameterAdmin["community"]["user"]["translations"]["button_register"] = "0";
        $parameterType["community"]["user"]["translations"]["button_register"] = "lang";

        $parameterTitle["community"]["user"]["translations"]["button_password_reset"] = "Button - password reset";
        $parameterValue["community"]["user"]["translations"]["button_password_reset"] = "Submit";
        $parameterAdmin["community"]["user"]["translations"]["button_password_reset"] = "0";
        $parameterType["community"]["user"]["translations"]["button_password_reset"] = "lang";

        $parameterTitle["community"]["user"]["translations"]["button_update"] = "Button - update";
        $parameterValue["community"]["user"]["translations"]["button_update"] = "Update";
        $parameterAdmin["community"]["user"]["translations"]["button_update"] = "0";
        $parameterType["community"]["user"]["translations"]["button_update"] = "lang";

        $parameterTitle["community"]["user"]["translations"]["button_login"] = "Button - login";
        $parameterValue["community"]["user"]["translations"]["button_login"] = "Login";
        $parameterAdmin["community"]["user"]["translations"]["button_login"] = "0";
        $parameterType["community"]["user"]["translations"]["button_login"] = "lang";

        $parameterTitle["community"]["user"]["translations"]["title_registration"] = "Title - registration";
        $parameterValue["community"]["user"]["translations"]["title_registration"] = "Registration";
        $parameterAdmin["community"]["user"]["translations"]["title_registration"] = "0";
        $parameterType["community"]["user"]["translations"]["title_registration"] = "lang";

        $parameterTitle["community"]["user"]["translations"]["title_profile"] = "Title - profile";
        $parameterValue["community"]["user"]["translations"]["title_profile"] = "User profile";
        $parameterAdmin["community"]["user"]["translations"]["title_profile"] = "0";
        $parameterType["community"]["user"]["translations"]["title_profile"] = "lang";

        $parameterTitle["community"]["user"]["translations"]["title_login"] = "Title - login";
        $parameterValue["community"]["user"]["translations"]["title_login"] = "Login";
        $parameterAdmin["community"]["user"]["translations"]["title_login"] = "0";
        $parameterType["community"]["user"]["translations"]["title_login"] = "lang";

        $parameterTitle["community"]["user"]["translations"]["title_password_reset"] = "Title - password reset";
        $parameterValue["community"]["user"]["translations"]["title_password_reset"] = "Password reset";
        $parameterAdmin["community"]["user"]["translations"]["title_password_reset"] = "0";
        $parameterType["community"]["user"]["translations"]["title_password_reset"] = "lang";

        $parameterTitle["community"]["user"]["translations"]["profile_updated"] = "Profile updated";
        $parameterValue["community"]["user"]["translations"]["profile_updated"] = "<p>Your profile has been updated.</p>";
        $parameterAdmin["community"]["user"]["translations"]["profile_updated"] = "0";
        $parameterType["community"]["user"]["translations"]["profile_updated"] = "lang_wysiwyg";

        $parameterTitle["community"]["user"]["translations"]["logout"] = "Logout";
        $parameterValue["community"]["user"]["translations"]["logout"] = "Logout";
        $parameterAdmin["community"]["user"]["translations"]["logout"] = "0";
        $parameterType["community"]["user"]["translations"]["logout"] = "lang";

        $parameterTitle["community"]["user"]["translations"]["password_reset"] = "Password reset hint";
        $parameterValue["community"]["user"]["translations"]["password_reset"] = "Forgot password?";
        $parameterAdmin["community"]["user"]["translations"]["password_reset"] = "0";
        $parameterType["community"]["user"]["translations"]["password_reset"] = "lang";

        $parameterTitle["community"]["user"]["translations"]["title_registration_verification_error"] = "Title - registration verification error";
        $parameterValue["community"]["user"]["translations"]["title_registration_verification_error"] = "E-mail verification error";
        $parameterAdmin["community"]["user"]["translations"]["title_registration_verification_error"] = "0";
        $parameterType["community"]["user"]["translations"]["title_registration_verification_error"] = "lang";

        $parameterTitle["community"]["user"]["translations"]["title_new_email_verification_error"] = "Title - new e-mail verification error";
        $parameterValue["community"]["user"]["translations"]["title_new_email_verification_error"] = "E-mail verification error";
        $parameterAdmin["community"]["user"]["translations"]["title_new_email_verification_error"] = "0";
        $parameterType["community"]["user"]["translations"]["title_new_email_verification_error"] = "lang";

        $parameterTitle["community"]["user"]["translations"]["text_registration_verification_error"] = "Text - registration verification error";
        $parameterValue["community"]["user"]["translations"]["text_registration_verification_error"] = "<p>Incorrect confirmation link.</p>";
        $parameterAdmin["community"]["user"]["translations"]["text_registration_verification_error"] = "0";
        $parameterType["community"]["user"]["translations"]["text_registration_verification_error"] = "lang_wysiwyg";

        $parameterTitle["community"]["user"]["translations"]["text_registration_verification_required"] = "Text - registration verification required";
        $parameterValue["community"]["user"]["translations"]["text_registration_verification_required"] = "<p>Thank you for your registration.</p>
<p>The confirmation e-mail has been sent to your e-mail address. Please confirm the registration by clicking on the link in the e-mail.</p>";
        $parameterAdmin["community"]["user"]["translations"]["text_registration_verification_required"] = "0";
        $parameterType["community"]["user"]["translations"]["text_registration_verification_required"] = "lang_wysiwyg";

        $parameterTitle["community"]["user"]["translations"]["text_new_email_verification_required"] = "Text - new e-mail verification required";
        $parameterValue["community"]["user"]["translations"]["text_new_email_verification_required"] = "<p>To update the e-mail address a confirmation link has been sent to your new e-mail address. Please confirm new data by clicking on the link in the e-mail.</p>";
        $parameterAdmin["community"]["user"]["translations"]["text_new_email_verification_required"] = "0";
        $parameterType["community"]["user"]["translations"]["text_new_email_verification_required"] = "lang_wysiwyg";

        $parameterTitle["community"]["user"]["translations"]["text_new_email_verification_error"] = "Text - new e-mail verification error";
        $parameterValue["community"]["user"]["translations"]["text_new_email_verification_error"] = "<p>Verification link is incorrect or requested e-mail address was taken during confirmation time.</p>";
        $parameterAdmin["community"]["user"]["translations"]["text_new_email_verification_error"] = "0";
        $parameterType["community"]["user"]["translations"]["text_new_email_verification_error"] = "lang_wysiwyg";

        $parameterTitle["community"]["user"]["translations"]["text_account_renewed"] = "Text - account renewed";
        $parameterValue["community"]["user"]["translations"]["text_account_renewed"] = "<p>Your account has been renewed successfully.</p>";
        $parameterAdmin["community"]["user"]["translations"]["text_account_renewed"] = "0";
        $parameterType["community"]["user"]["translations"]["text_account_renewed"] = "lang_wysiwyg";

        $parameterTitle["community"]["user"]["translations"]["text_user_exist_error"] = "Text - user exist error";
        $parameterValue["community"]["user"]["translations"]["text_user_exist_error"] = "<p>Requested login name is unavailable.</p>";
        $parameterAdmin["community"]["user"]["translations"]["text_user_exist_error"] = "0";
        $parameterType["community"]["user"]["translations"]["text_user_exist_error"] = "lang_wysiwyg";

        $parameterTitle["community"]["user"]["translations"]["text_email_exist_error"] = "Text - e-mail exist error";
        $parameterValue["community"]["user"]["translations"]["text_email_exist_error"] = "<p>E-mail address is already in use.</p>";
        $parameterAdmin["community"]["user"]["translations"]["text_email_exist_error"] = "0";
        $parameterType["community"]["user"]["translations"]["text_email_exist_error"] = "lang_wysiwyg";

        $parameterTitle["community"]["user"]["translations"]["text_password_reset"] = "Password reset text";
        $parameterValue["community"]["user"]["translations"]["text_password_reset"] = "<p>Please enter your e-mail address and new password.</p>";
        $parameterAdmin["community"]["user"]["translations"]["text_password_reset"] = "0";
        $parameterType["community"]["user"]["translations"]["text_password_reset"] = "lang_wysiwyg";

        $parameterTitle["community"]["user"]["translations"]["text_password_verified"] = "Text - password verified";
        $parameterValue["community"]["user"]["translations"]["text_password_verified"] = "Password was updated successfully. You can login now.";
        $parameterAdmin["community"]["user"]["translations"]["text_password_verified"] = "0";
        $parameterType["community"]["user"]["translations"]["text_password_verified"] = "lang";

        $parameterTitle["community"]["user"]["translations"]["text_registration_successful"] = "Text - registraton successful";
        $parameterValue["community"]["user"]["translations"]["text_registration_successful"] = "<p>Your registration was completed successfully. You can login now.</p>";
        $parameterAdmin["community"]["user"]["translations"]["text_registration_successful"] = "0";
        $parameterType["community"]["user"]["translations"]["text_registration_successful"] = "lang_wysiwyg";

        $parameterTitle["community"]["user"]["translations"]["text_password_reset_sent"] = "Text - password reset instructions were sent";
        $parameterValue["community"]["user"]["translations"]["text_password_reset_sent"] = "<p>Password reset instructions were sent to your e-mail address.</p>";
        $parameterAdmin["community"]["user"]["translations"]["text_password_reset_sent"] = "0";
        $parameterType["community"]["user"]["translations"]["text_password_reset_sent"] = "lang_wysiwyg";

        $parameterTitle["community"]["user"]["translations"]["text_disabled_registration_error"] = "Text - disabled registration error";
        $parameterValue["community"]["user"]["translations"]["text_disabled_registration_error"] = "<p>Registration is disabled. Please contact website administrator.</p>";
        $parameterAdmin["community"]["user"]["translations"]["text_disabled_registration_error"] = "0";
        $parameterType["community"]["user"]["translations"]["text_disabled_registration_error"] = "lang_wysiwyg";

        $parameterTitle["community"]["user"]["translations"]["text_password_verification_error"] = "Text - password reset verification error";
        $parameterValue["community"]["user"]["translations"]["text_password_verification_error"] = "Verification of new password has failed.";
        $parameterAdmin["community"]["user"]["translations"]["text_password_verification_error"] = "0";
        $parameterType["community"]["user"]["translations"]["text_password_verification_error"] = "lang";

        $parameterTitle["community"]["user"]["translations"]["text_account_renewal_error"] = "Text - account renewal error";
        $parameterValue["community"]["user"]["translations"]["text_account_renewal_error"] = "<p>Error occurred while renewing your account. Try to renew your account by logging in and out. If you can't do so, your account may be already deleted.</p>";
        $parameterAdmin["community"]["user"]["translations"]["text_account_renewal_error"] = "0";
        $parameterType["community"]["user"]["translations"]["text_account_renewal_error"] = "lang_wysiwyg";

        $parameterTitle["community"]["user"]["translations"]["text_registration_verified"] = "Text - registration verified";
        $parameterValue["community"]["user"]["translations"]["text_registration_verified"] = "Registration has been aproved. You can login now.";
        $parameterAdmin["community"]["user"]["translations"]["text_registration_verified"] = "0";
        $parameterType["community"]["user"]["translations"]["text_registration_verified"] = "lang_wysiwyg";

        
        
        
        
$moduleGroupTitle["administrator"] = "Administrator";
$moduleTitle["administrator"]["administrators"] = "Administrators";

    $parameterGroupTitle["administrator"]["administrators"]["admin_translations"] = "Admin translations";
    $parameterGroupAdmin["administrator"]["administrators"]["admin_translations"] = "1";

        $parameterTitle["administrator"]["administrators"]["admin_translations"]["name"] = "Name";
        $parameterValue["administrator"]["administrators"]["admin_translations"]["name"] = "Name";
        $parameterAdmin["administrator"]["administrators"]["admin_translations"]["name"] = "1";
        $parameterType["administrator"]["administrators"]["admin_translations"]["name"] = "string";

        $parameterTitle["administrator"]["administrators"]["admin_translations"]["password"] = "Password";
        $parameterValue["administrator"]["administrators"]["admin_translations"]["password"] = "Password";
        $parameterAdmin["administrator"]["administrators"]["admin_translations"]["password"] = "1";
        $parameterType["administrator"]["administrators"]["admin_translations"]["password"] = "string";

        $parameterTitle["administrator"]["administrators"]["admin_translations"]["module"] = "Module";
        $parameterValue["administrator"]["administrators"]["admin_translations"]["module"] = "Module";
        $parameterAdmin["administrator"]["administrators"]["admin_translations"]["module"] = "1";
        $parameterType["administrator"]["administrators"]["admin_translations"]["module"] = "string";

        $parameterTitle["administrator"]["administrators"]["admin_translations"]["administrators"] = "Administrators";
        $parameterValue["administrator"]["administrators"]["admin_translations"]["administrators"] = "Administrators";
        $parameterAdmin["administrator"]["administrators"]["admin_translations"]["administrators"] = "1";
        $parameterType["administrator"]["administrators"]["admin_translations"]["administrators"] = "string";

        $parameterTitle["administrator"]["administrators"]["admin_translations"]["allowed_modules"] = "Allowed modules";
        $parameterValue["administrator"]["administrators"]["admin_translations"]["allowed_modules"] = "Allowed modules";
        $parameterAdmin["administrator"]["administrators"]["admin_translations"]["allowed_modules"] = "1";
        $parameterType["administrator"]["administrators"]["admin_translations"]["allowed_modules"] = "string";

        $parameterTitle["administrator"]["administrators"]["admin_translations"]["permissions"] = "Permissions";
        $parameterValue["administrator"]["administrators"]["admin_translations"]["permissions"] = "Permissions";
        $parameterAdmin["administrator"]["administrators"]["admin_translations"]["permissions"] = "1";
        $parameterType["administrator"]["administrators"]["admin_translations"]["permissions"] = "string";

    $parameterGroupTitle["administrator"]["rss"]["options"] = "Options";
    $parameterGroupAdmin["administrator"]["rss"]["options"] = "0";

        $parameterTitle["administrator"]["rss"]["options"]["size"] = "Size";
        $parameterValue["administrator"]["rss"]["options"]["size"] = "10";
        $parameterAdmin["administrator"]["rss"]["options"]["size"] = "1";
        $parameterType["administrator"]["rss"]["options"]["size"] = "integer";

        $parameterTitle["administrator"]["rss"]["options"]["update_speed"] = "Update speed";
        $parameterValue["administrator"]["rss"]["options"]["update_speed"] = "180";
        $parameterAdmin["administrator"]["rss"]["options"]["update_speed"] = "1";
        $parameterType["administrator"]["rss"]["options"]["update_speed"] = "integer";

        $parameterTitle["administrator"]["rss"]["options"]["title"] = "Title";
        $parameterValue["administrator"]["rss"]["options"]["title"] = "ImpressPages RSS";
        $parameterAdmin["administrator"]["rss"]["options"]["title"] = "0";
        $parameterType["administrator"]["rss"]["options"]["title"] = "lang";

        $parameterTitle["administrator"]["rss"]["options"]["description"] = "Description";
        $parameterValue["administrator"]["rss"]["options"]["description"] = "";
        $parameterAdmin["administrator"]["rss"]["options"]["description"] = "0";
        $parameterType["administrator"]["rss"]["options"]["description"] = "lang";

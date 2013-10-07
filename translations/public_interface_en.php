<?php

//language description
$languageCode = "en"; //RFC 4646 code
$languageShort = "EN"; //Short description
$languageLong = "English"; //Long title
$languageUrl = "en";





$parameterValue["administrator"]["rss"]["options"]["description"] = "";

$parameterValue["administrator"]["rss"]["options"]["title"] = "ImpressPages RSS";

$parameterValue["administrator"]["rss"]["translations"]["rss"] = "RSS news";

$parameterValue["administrator"]["search"]["translations"]["no_results"] = "<p>No pages containing your search term were found.</p>";

$parameterValue["administrator"]["search"]["translations"]["no_search_word"] = "<p>Please enter at least one search keyword.</p>";

$parameterValue["administrator"]["search"]["translations"]["search"] = "Search";

$parameterValue["administrator"]["sitemap"]["translations"]["sitemap"] = "Sitemap";

$parameterValue["community"]["newsletter"]["subscription_translations"]["newsletter"] = "Newsletter";

$parameterValue["community"]["newsletter"]["subscription_translations"]["subject_confirmation"] = "Newsletter e-mail confirmation ";

$parameterValue["community"]["newsletter"]["subscription_translations"]["subscribe"] = "Subscribe";

$parameterValue["community"]["newsletter"]["subscription_translations"]["label"] = "Enter e-mail address";

$parameterValue["community"]["newsletter"]["subscription_translations"]["text_confirmation"] = "<p>Thank you for your registration.</p>
<p>The confirmation e-mail has been sent to your e-mail address. Please confirm the registration by clicking on the link in the e-mail.</p>";

$parameterValue["community"]["newsletter"]["subscription_translations"]["text_email_confirmation"] = "<p><strong>Thank you for subscribing to our newsletter!</strong></p>
<p>To complete your registration, you need to confirm that you have received this e-mail by clicking on the link below:</p>
<p>[[link]]</p>
<p>If the link does not work, just copy and paste the entire link into your browser.</p>";

$parameterValue["community"]["newsletter"]["subscription_translations"]["text_error_cant_unsubscribe"] = "<p>Specified e-mail address does not exist in database.</p>";

$parameterValue["community"]["newsletter"]["subscription_translations"]["text_error_confirmation"] = "<p>Incorrect confirmation link.</p>";

$parameterValue["community"]["newsletter"]["subscription_translations"]["text_incorrect_email"] = "Incorrect e-mail address";

$parameterValue["community"]["newsletter"]["subscription_translations"]["text_subscribed"] = "<p>You have successfully subscribed to our newsletter.</p>";

$parameterValue["community"]["newsletter"]["subscription_translations"]["text_unsubscribed"] = "<p>Your e-mail address has been removed from our database.</p>";

$parameterValue["community"]["newsletter"]["subscription_translations"]["unsubscribe"] = "Unsubscribe";

$parameterValue["standard"]["configuration"]["translations"]["home"] = "Home";

$parameterValue["standard"]["configuration"]["error_404"]["error_broken_link_inside"] = "<p>Sorry, but the page you were trying to get to, does not exist.</p>";

$parameterValue["standard"]["configuration"]["error_404"]["error_broken_link_outside"] = "<p>Sorry, but the page you were trying to get to, does not exist. Apparently, there is a broken link on the page you just came from. We have been notified and will attempt to contact the owner of that page and let them know about it.</p>";

$parameterValue["standard"]["configuration"]["error_404"]["error_mistyped_url"] = "<p>Sorry, but the page you were trying to get to does not exist.</p>
<p>It looks like this was the result of either</p>
<ul>
<li>a mistyped address</li>
<li>or an out-of-date bookmark in your web browser.</li>
</ul>";

$parameterValue["standard"]["configuration"]["main_parameters"]["email_template"] = "<table border=\"0\" width=\"100%\">
<tbody>
<tr>
<td style=\"padding: 20px 0;\" bgcolor=\"#edf0f2\">
<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"590\" align=\"center\">
<tbody>
<tr>
<td style=\"border: 1px solid #d1d2d2; padding: 15px 30px; font-size: 14px; width: 530px; color: #8b8a8a; line-height: 19px; font-family: Arial,Verdana,Tahoma; background-color: white;\">
<h1 style=\"font-family: Trebuchet MS, Verdana, Tahoma; font-size: 28px; color: #00a8da;\">Hi,</h1>
<p>[[content]]</p>
<p style=\"border-top: 1px dotted #7db113; height: 1px; font-size: 1px;\">&nbsp;</p>
<p>[[site_name]]<br />[[site_email]]</p>
<p>[[unsubscribe]]</p>
</td>
</tr>
</tbody>
</table>
<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"590\" align=\"center\">
<tbody>
<tr>
<td>
<div style=\"padding-top: 7px;\"><a href=\"http://www.impresspages.org\"><img style=\"float: left; border: 0;\" src=\"image/repository/impress-pages.gif\" alt=\"\" /></a></div>
</td>
<td style=\"text-align: right;\">
<div style=\"font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #6d6b70; padding-top: 7px;\">Powered by <a style=\"text-decoration: underline; font-family: Verdana, Tahoma, Arial; font-size: 10px; color: #6d6b70;\" href=\"http://www.impresspages.org\">ImpressPages CMS</a></div>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>";

$parameterValue["standard"]["configuration"]["translations"]["copyright"] = "Copyright [dates] by [author/owner]";

$parameterValue["community"]["user"]["email_messages"]["subject_verify_new_email"] = "Updated profile verification";

$parameterValue["community"]["user"]["email_messages"]["subject_verify_registration"] = "Registration";

$parameterValue["community"]["user"]["email_messages"]["subject_user_deleted"] = "User deleted";

$parameterValue["community"]["user"]["email_messages"]["subject_password_reset"] = "Password reset instructions";

$parameterValue["community"]["user"]["email_messages"]["subject_account_will_expire"] = "Account information renewal";

$parameterValue["community"]["user"]["email_messages"]["text_verify_registration"] = "<p><strong>Thank you for registering!</strong></p>
<p>To complete your registration you need to confirm that you have received this e-mail by clicking on the link below:</p>
<p>[[link]]</p>
<p>If the link does not work, just copy and paste the entire link into your browser.</p>";

$parameterValue["community"]["user"]["email_messages"]["text_verify_new_email"] = "<p>Your e-mail has been changed. To complete the update process of your profile you need to confirm that you have received this e-mail by clicking on the link below:</p>
<p>[[link]]</p>
<p>If the link does not work, just copy and paste the entire link into your browser.</p>";

$parameterValue["community"]["user"]["email_messages"]["text_user_deleted"] = "<p>Your account has been deleted.</p>";

$parameterValue["community"]["user"]["email_messages"]["text_account_will_expire"] = "<p>Your account is due to expire at [[date]].</p>
<p>Please visit the website and log in or click the link bellow to renew your account:</p>
<p>[[link]]</p>
<p>&nbsp;</p>
<p>If you take no action until [[date]] your account will be deleted.</p>";

$parameterValue["community"]["user"]["email_messages"]["text_password_reset"] = "<p>You asked to reset your password. Please press the link bellow to confirm this action:</p>
<p>[[link]]</p>
<p>If the link does not work, just copy and paste the entire link into your browser.</p>";

$parameterValue["community"]["user"]["errors"]["already_registered"] = "Already registered";

$parameterValue["community"]["user"]["errors"]["passwords_dont_match"] = "Passwords don't match";

$parameterValue["community"]["user"]["errors"]["email_doesnt_exist"] = "Specified e-mail address doesn't exist.";

$parameterValue["community"]["user"]["errors"]["incorrect_email_data"] = "Incorrect e-mail address or password";

$parameterValue["community"]["user"]["errors"]["incorrect_login_data"] = "Incorrect user name or password";

$parameterValue["community"]["user"]["translations"]["field_email"] = "E-mail";

$parameterValue["community"]["user"]["translations"]["field_password"] = "Password";

$parameterValue["community"]["user"]["translations"]["field_confirm_password"] = "Confirm Password";

$parameterValue["community"]["user"]["translations"]["field_login"] = "User name";

$parameterValue["community"]["user"]["translations"]["button_register"] = "Register";

$parameterValue["community"]["user"]["translations"]["button_password_reset"] = "Submit";

$parameterValue["community"]["user"]["translations"]["button_update"] = "Update";

$parameterValue["community"]["user"]["translations"]["button_login"] = "Login";

$parameterValue["community"]["user"]["translations"]["title_registration"] = "Registration";

$parameterValue["community"]["user"]["translations"]["title_profile"] = "User profile";

$parameterValue["community"]["user"]["translations"]["title_login"] = "Login";

$parameterValue["community"]["user"]["translations"]["title_password_reset"] = "Password reset";

$parameterValue["community"]["user"]["translations"]["profile_updated"] = "<p>Your profile has been updated.</p>";

$parameterValue["community"]["user"]["translations"]["logout"] = "Logout";

$parameterValue["community"]["user"]["translations"]["password_reset"] = "Forgot password?";

$parameterValue["community"]["user"]["translations"]["title_registration_verification_error"] = "E-mail verification error";

$parameterValue["community"]["user"]["translations"]["title_new_email_verification_error"] = "E-mail verification error";

$parameterValue["community"]["user"]["translations"]["text_registration_verification_error"] = "<p>Incorrect confirmation link.</p>";

$parameterValue["community"]["user"]["translations"]["text_registration_verification_required"] = "<p>Thank you for your registration.</p>
<p>The confirmation e-mail has been sent to your e-mail address. Please confirm the registration by clicking on the link in the e-mail.</p>";

$parameterValue["community"]["user"]["translations"]["text_new_email_verification_required"] = "<p>To update the e-mail address a confirmation link has been sent to your new e-mail address. Please confirm new data by clicking on the link in the e-mail.</p>";

$parameterValue["community"]["user"]["translations"]["text_new_email_verification_error"] = "<p>Verification link is incorrect or requested e-mail address was taken during confirmation time.</p>";

$parameterValue["community"]["user"]["translations"]["text_account_renewed"] = "<p>Your account has been renewed successfully.</p>";

$parameterValue["community"]["user"]["translations"]["text_user_exist_error"] = "<p>Requested login name is unavailable.</p>";

$parameterValue["community"]["user"]["translations"]["text_email_exist_error"] = "<p>E-mail address is already in use.</p>";

$parameterValue["community"]["user"]["translations"]["text_password_reset"] = "<p>Please enter your e-mail address and new password.</p>";

$parameterValue["community"]["user"]["translations"]["text_password_verified"] = "Password was updated successfully. You can login now.";

$parameterValue["community"]["user"]["translations"]["text_registration_successful"] = "<p>Your registration was completed successfully. You can login now.</p>";

$parameterValue["community"]["user"]["translations"]["text_password_reset_sent"] = "<p>Password reset instructions were sent to your e-mail address.</p>";

$parameterValue["community"]["user"]["translations"]["text_disabled_registration_error"] = "<p>Registration is disabled. Please contact website administrator.</p>";

$parameterValue["community"]["user"]["translations"]["text_password_verification_error"] = "Verification of new password has failed.";

$parameterValue["community"]["user"]["translations"]["text_account_renewal_error"] = "<p>Error occurred while renewing your account. Try to renew your account by logging in and out. If you can't do so, your account may be already deleted.</p>";

$parameterValue["developer"]["form"]["error_messages"]["unknown"] = "Please correct this value";

$parameterValue["developer"]["form"]["error_messages"]["email"] = "Please enter a valid email address";

$parameterValue["developer"]["form"]["error_messages"]["number"] = "Please enter a valid numeric value";

$parameterValue["developer"]["form"]["error_messages"]["url"] = "Please enter a valid URL";

$parameterValue["developer"]["form"]["error_messages"]["max"] = "Please enter a value no larger than $1";

$parameterValue["developer"]["form"]["error_messages"]["min"] = "Please enter a value of at least $1";

$parameterValue["developer"]["form"]["error_messages"]["required"] = "Please complete this mandatory field";

$parameterValue["developer"]["form"]["error_messages"]["xss"] = "Session has expired. Please refresh the page.";
<?php

/**
 * @package ImpressPages
 *
 *
 */

namespace Backend;

if (!defined('BACKEND')) { exit; }

class Template {

    public static function headerLogin() {
        $answer = '';
        $answer .= '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>ImpressPages</title>
                <link rel="stylesheet" href="' . BASE_URL . BACKEND_DIR . 'design/login/login.css">
                <link rel="shortcut icon" href="' . BASE_URL . 'favicon.ico">
            </head>
            <body>
        ';
        return $answer;
    }

    // TODOX remove if not used
    public static function loginForm($error = null) {
        global $parametersMod;
        global $cms;

        if ($error) {
            $error = htmlspecialchars($error);
        }

        $answer = '';
        $answer .= '
            <a href="http://www.impresspages.org/" class="logo" target="_blank"><img src="' . BASE_URL . BACKEND_DIR . 'design/login/logo.png"></a>
            <div class="verticalAlign"></div>
            <div class="login">
                <div class="loginTitle">
                    <h1>Login</h1>
                </div>
                <form action="' . $cms->generateActionUrl('login') . '" method="post">
                    <span class="loginError">' . $error . '</span>
                    <input type="hidden" name="action" value="login">
                    <label>
                        <span>' . htmlspecialchars($parametersMod->getValue('standard', 'configuration', 'system_translations', 'login_name')) . '</span>
                        <input class="loginInput" id="login_name" name="f_name" type="text">
                    </label>
                    <label>
                        <span>' . htmlspecialchars($parametersMod->getValue('standard', 'configuration', 'system_translations', 'login_password')) . '</span>
                        <input class="loginInput" type="password" name="f_pass">
                    </label>
                    <input class="loginSubmit" type="submit" value="' . htmlspecialchars($parametersMod->getValue('standard', 'configuration', 'system_translations', 'login_login')) . '">
                </form>
            </div>
            <div class="loginFooter">Copyright 2009-' . date("Y") . ' by <a href="http://www.impresspages.org/">ImpressPages UAB</a></div>
            <script>
            //<![CDATA[
              document.getElementById(\'login_name\').focus();
            //]]>
            </script>
        ';
        return $answer;
    }

    function footer() {
        $answer = '
            </body>
            </html>
        ';
        return $answer;
    }
}

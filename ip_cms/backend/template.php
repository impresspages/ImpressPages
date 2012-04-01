<?php

/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Backend;

if (!defined('BACKEND')) {
    exit;
}

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
    
    public static function loginForm($error = null) {
        global $parametersMod;
        global $cms;
    
        if ($error)
        $error = '<label class="error">' . htmlspecialchars($error) . '</label>';
    
        $answer = '';
        $answer .= '
          <div class="wrapper">
                  <a href="http://www.impresspages.org"><img src="' . BASE_URL . BACKEND_DIR . 'design/login/impress-pages.png" class="logo"></a>
                  <div class="image">
                          <div class="box">
                                  <div class="boxTop"></div>
                                  <form style="margin: 0;" action="' . $cms->generateActionUrl('login') . '" method="post">
                                          <div class="boxContent">
                                                  ' . $error . '
                                                  <div class="input"><input type="hidden" name="action" value="login"></div>
                                                  <label>' . htmlspecialchars($parametersMod->getValue('standard', 'configuration', 'system_translations', 'login_name')) . '</label>
                                                  <div class="input"><input id="login_name" name="f_name" type="text"></div>
                                                  <label>' . htmlspecialchars($parametersMod->getValue('standard', 'configuration', 'system_translations', 'login_password')) . '</label>
                                                  <div class="input"><input type="password" name="f_pass"></div>
                                                  <input class="submit" type="submit" value="' . htmlspecialchars($parametersMod->getValue('standard', 'configuration', 'system_translations', 'login_login')) . '">
                                                  <div style="clear: both;"></div>
                                          </div>
                                  </form>
                                  <div class="boxBottom"></div>
                          </div>
                  </div>
          <div class="footer">Copyright 2009-' . date("Y") . ' by <a href="http://www.impresspages.org">ImpressPages LTD</a></div>
          </div>
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

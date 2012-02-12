<?php

/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Backend;

if (!defined('BACKEND'))
exit;

class HtmlOutput {

    var $html;

    function __construct() {
        $this->html = '';
    }

    function headerModule() {
        global $parametersMod;
        $this->html .= '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ImpressPages</title>
    <link rel="shortcut icon" href="' . BASE_URL . 'favicon.ico">
    
    <script type="text/javascript">
        var ip = {
            baseUrl : '.json_encode(BASE_URL).',
            libraryDir : '.json_encode(LIBRARY_DIR).',
            themeDir : '.json_encode(THEME_DIR).',
            moduleDir : '.json_encode(MODULE_DIR).',
            theme : '. json_encode(THEME) .',
            zoneName : '.json_encode(null).',
            pageId : '.json_encode(null).',
            revisionId : '.json_encode(null).',
        };
    </script>
    <script src="' . BASE_URL . LIBRARY_DIR . 'js/default.js"></script>
    <script src="' . BASE_URL . LIBRARY_DIR . 'js/tabs.js"></script>
    <script src="' . BASE_URL . LIBRARY_DIR . 'js/jquery/jquery.js"></script>
    <script src="' . BASE_URL . LIBRARY_DIR . 'js/tiny_mce/jquery.tinymce.js"></script>
    <script src="' . BASE_URL . '?g=standard&amp;m=configuration&amp;a=tinymceConfig"></script>
    
</head>

<body> <!-- display loading until page is loaded-->

    <!-- display loading util page is loaded-->
    <div id="loading">
      <div id="loading_bg"
      style="width:100%; height: 100%; z-index: 999; position: fixed; left: 0; top: 0;
      filter: alpha(opacity=65);
      -moz-opacity: 0.65;
      background-color: #cccccc;
      "
      >

      </div>
      <div id="loading_text"
      style="
      height: 60px; width: 100%; position: fixed; left:0px; top: 180px;
      z-index: 1001;
      "
      >
        <table style="margin-left: auto; margin-right: auto;"><tr>
        <td style="font-family: Verdana, Tahoma, Arial; font-size: 14px; color: #505050; padding: 30px 33px; background-color: #eeeeee; border: 1px solid #999999;">
        ' . $parametersMod->getValue('standard', 'configuration', 'system_translations', 'loading') . '								</td>
        </tr></table>
      </div>
    </div>
    <script>
    //<![CDATA[
    LibDefault.addEvent(window, \'load\', init);

    function init(){
    document.getElementById(\'loading\').style.display = \'none\';
    }
    //]]>
    </script>
    <!-- display loading until page is loaded-->
    ';
    }

    function headerModules() {
        global $cms;
        $this->html .= '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ImpressPages</title>
    <link rel="stylesheet" href="' . BASE_URL . BACKEND_DIR . 'design/ip_admin.css">
    <link rel="shortcut icon" href="' . BASE_URL . 'favicon.ico">
    <script src="' . BASE_URL . LIBRARY_DIR . 'js/default.js"></script>
    <script src="' . BASE_URL . LIBRARY_DIR . 'js/tabs.js"></script>
    <script src="' . BASE_URL . LIBRARY_DIR . 'js/jquery/jquery.js"></script>
    <script src="' . BASE_URL . BACKEND_DIR . 'design/ip_admin.js"></script>
</head>

<body>
<script>
  //<![CDATA[
    function pingResponse(response){
      if(response != \'\'){
        document.location = \'admin.php\';
      }
    }
  
  
    function session_ping(){
        $.ajax({
            type : \'POST\',
            url : \'' . $cms->generateActionUrl('ping') . '\',
            data : Object(),
            success : pingResponse
        });
    }
    iii = setInterval(session_ping, 240000);
    
    


  //]]>
</script> 
    ';


        if( isset($_SESSION['modules']['administrator']['system']['show_system_message'])) {
            if ($_SESSION['modules']['administrator']['system']['show_system_message'] == true) {

                $this->html .= '
    <script>
      //<![CDATA[

            var notice = document.getElementById(\'ipCmsSystemNotice\');
            if(notice) {
              notice.style.display = \'\';
            }
      //]]>
    </script>
        ';
            } else {
                // do nothing
            }

        } else {
            $this->html .= '
  <script>
    //<![CDATA[
      function ipCmsNoticeResponse(response){
        if(response != \'\') {
          var notice = document.getElementById(\'ipCmsSystemNotice\');
          if(notice) {
            responseArray = eval(\'(\' + response + \')\');
            for(var i in responseArray) {
              if(responseArray[i][\'type\'] != \'status\') {
                notice.style.display = \'\';
              }
            }
          }
        }
      }
      LibDefault.ajaxMessage(document.location, \'module_name=system&module_group=administrator&action=getSystemInfo&afterLogin=1\', ipCmsNoticeResponse);
    //]]>
  </script>
      ';
        }





    }

    function headerLogin() {
        $this->html .= '
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
    }

    function loginForm($error = null) {
        global $parametersMod;
        global $cms;

        if ($error)
        $error = '<label class="error">' . htmlspecialchars($error) . '</label>';

        $this->html .= '
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
    }

    function modules($groups) {
        global $cms;
        global $parametersMod;
        $modulesHtml = '';
        $systemModule = null;

        if ($groups !== null) {
            $modulesHtml .= '<ul>';
            $i = 0;
            $ipaActive = ' class="ipaActive"';
            foreach ($groups as $key => $modules) {
                if ($modules !== null) {
                    $modulesHtml .= '<li' . ($i ? '' : $ipaActive) . '><a href="#">' . $key . '</a>';
                    $modulesHtml .= '<ul>';
                    $i2 = 0;
                    foreach ($modules as $key2 => $module) {
                        if($module['g_name'] == 'administrator' && $module['m_name'] == 'system') {
                            $systemModule = $module;
                        }
                        $modulesHtml .= '<li' . ($i || $i2 ? '' : $ipaActive) . ' id="ipAdminModule-' . $module['id'] . '"><a href="' . $cms->generateUrl($module['id']) . '" target="content">' . $module['translation'] . '</a></li>';
                        $i2++;
                    }
                    $modulesHtml .= '</ul>';
                    $modulesHtml .= '</li>';
                } else {
                    trigger_error("No modules");
                }
                $i++;
            }
            $modulesHtml .= '</ul>';
        } else {
            trigger_error("No groups");
        }

        // checking to show system notice
        if($systemModule != null && !empty($_SESSION['modules']['administrator']['system']['show_system_message'])) {
            $systemMessage = '
            <a href="' . $cms->generateUrl($systemModule['id']) . '" target="content" class="ipaNotice">
                ' . $parametersMod->getValue('standard', 'configuration', 'system_translations', 'system_message') . '
            </a>';
        } else {
            $systemMessage = '';
        }

        $this->html .= '
    <div class="ipAdminNav">
        <div class="ipAdminNavActions">
            ' . $systemMessage . '
            <a class="ipaHelp" target="_blank" href="http://www.impresspages.org/help2">
                ' . $parametersMod->getValue('standard', 'configuration', 'system_translations', 'help') . '
            </a>
            <a class="ipaLogout" href="' . $cms->generateActionUrl('logout') . '">
                ' . $parametersMod->getValue('standard', 'configuration', 'system_translations', 'logout') . '
            </a>
        </div>
        <div class="ipAdminNavLinks">
            ' . $modulesHtml . '
        </div>
    </div>
';
    }

    function footer() {
        $this->html .= '
      </body>
      </html>    
    ';
    }

    function html($code) {
        $this->html .= $code;
    }

    function send() {
        echo $this->html;
    }

}


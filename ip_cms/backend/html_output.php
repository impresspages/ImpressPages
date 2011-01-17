<?php

/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license		GNU/GPL, see ip_license.html
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
  <title>ImpressPages</title>
  <link rel="shortcut icon" href="' . BASE_URL . 'favicon.ico" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <script type="text/javascript" src="' . BASE_URL . LIBRARY_DIR . 'js/default.js"></script>
  <script type="text/javascript" src="' . BASE_URL . LIBRARY_DIR . 'js/tabs.js"></script>
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
    <script type="text/javascript">
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
    <title>ImpressPages</title>
  <link href="' . BASE_URL . BACKEND_DIR . 'design/modules/modules.css" rel="stylesheet" type="text/css" />
  <link rel="shortcut icon" href="' . BASE_URL . 'favicon.ico" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <script type="text/javascript" src="' . BASE_URL . LIBRARY_DIR . 'js/default.js"></script>
  <script type="text/javascript" src="' . BASE_URL . LIBRARY_DIR . 'js/tabs.js"></script>
</head>   
	
<body>
<script type="text/javascript">
  //<![CDATA[
    function session_ping(){
      LibDefault.ajaxMessage(\'' . $cms->generateActionUrl('ping') . '\', \'\', pingResponse);
    }
    iii = setInterval(session_ping, 120000);
    
    function pingResponse(response){
      if(response != \'\'){
        document.location = \'admin.php\';
      }
    }
    


  //]]>
</script> 
    ';


    if( isset($_SESSION['modules']['administrator']['system']['show_system_message'])) {
      if ($_SESSION['modules']['administrator']['system']['show_system_message'] == true) {

        $this->html .= '
    <script type="text/javascript">
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
  <script type="text/javascript">
    //<![CDATA[
      function ipCmsNoticeResponse(response){
        if(response != \'\') {
          var notice = document.getElementById(\'ipCmsSystemNotice\');
          if(notice) {
            notice.style.display = \'\';
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
  <title>ImpressPages</title>
  <link href="' . BASE_URL . BACKEND_DIR . 'design/login/login.css" rel="stylesheet" type="text/css" />
  <link rel="shortcut icon" href="' . BASE_URL . 'favicon.ico" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
              <a href="http://www.impresspages.org"><img src="' . BASE_URL . BACKEND_DIR . 'design/login/impress-pages.png" class="logo" /></a>
              <div class="image">
                      <div class="box">
                              <div class="boxTop"></div>
                              <form style="margin: 0;" action="' . $cms->generateActionUrl('login') . '" method="post">
                                      <div class="boxContent">
                                              ' . $error . '
                                              <div class="input"><input type="hidden" name="action" value="login" /></div>
                                              <label>' . htmlspecialchars($parametersMod->getValue('standard', 'configuration', 'system_translations', 'login_name')) . '</label>
                                              <div class="input"><input id="login_name" name="f_name" type="text" /></div>
                                              <label>' . htmlspecialchars($parametersMod->getValue('standard', 'configuration', 'system_translations', 'login_password')) . '</label>
                                              <div class="input"><input type="password" name="f_pass" /></div>
                                              <input class="submit" type="submit" value="' . htmlspecialchars($parametersMod->getValue('standard', 'configuration', 'system_translations', 'login_login')) . '" />
                                              <div style="clear: both;"></div>
                                      </div>
                              </form>
                              <div class="boxBottom"></div>
                      </div>
              </div>
      <div class="footer">Copyright 2007-' . date("Y") . ' by <a href="http://www.aproweb.eu">ImpressPages LTD</a></div>
      </div>
      <script type="text/javascript">
      //<![CDATA[
        document.getElementById(\'login_name\').focus();
      //]]>
      </script>
		
    ';
  }

  function modules($groups) {
    global $cms;
    $groupsHtml = "";
    $systemModule = null;
    $systemModuleGroupListId = '';

    if ($groups !== null) {



      $i = 0;
      foreach ($groups as $key => $modules) {

        if ($modules !== null)
          $groupsHtml .= '<span id="moduleGroupLink' . $i . '" class="left knob opened" >' . $key . '</span>';
        $i++;
      }

      $groupsHtml .= '
  <script type="text/javascript">
  //<![CDATA[
    var modTabs = new LibTabs(\'modTabs\', \'left knob\', \'left knob opened\');
    var last_selected = null;
    function change_page(cur_object, url){
      if(last_selected){
      last_selected.setAttribute(document.all ? "className" : "class", \'top_tabs_normal\');
    }
    last_selected = cur_object;
    last_selected.setAttribute(document.all ? "className" : "class", \'top_tabs_normal top_tabs_selected\');
    parent.content.location=url;

    }
  //]]>
  </script>
  ';
      $i = 0;
      $modulesHtml = '';
      $script_html = '';
      foreach ($groups as $key => $modules) {
        if ($modules !== null) {
          $modulesHtml .= '<div id="moduleGroup' . $i . '" class="top_tabs"><ul>';
          foreach ($modules as $key2 => $module) {
            if($module['g_name'] == 'administrator' && $module['m_name'] == 'system') {
              $systemModule = $module;
              $systemModuleGroupListId = $i;
            }
            if ($i == 0 && $key2 == 0) {
              $modulesHtml .= '<li id="module_'.$module['g_name'].'_'.$module['m_name'].'" id="modTabsFirstModule" class="top_tabs_normal top_tabs_selected" onclick="change_page(this, \'' . $cms->generateUrl($module['id']) . '\')" ><span>' . $module['translation'] . '</span></li>';
              $script_html .= '
              <script type="text/javascript">
                 //<![CDATA[
                last_selected = document.getElementById(\'modTabsFirstModule\');
                //]]>
              </script>';
            }else
              $modulesHtml .= '<li id="module_'.$module['g_name'].'_'.$module['m_name'].'" class="top_tabs_normal" onclick="change_page(this, \'' . $cms->generateUrl($module['id']) . '\')" ><span>' . $module['translation'] . '</span></li>';
          }
          $modulesHtml .= '</ul></div>';
          $modulesHtml .=
                  $script_html . '
          <script type="text/javascript">
            //<![CDATA[
            modTabs.addTab(\'moduleGroupLink' . $i . '\', \'moduleGroup' . $i . '\');
            //]]>
          </script>
          ';
        }else
          trigger_error("No modules");
        $i++;
      }
    }else
      trigger_error("No groups");
    global $parametersMod;
    global $cms;

    if($systemModule != null) {
      $systemMessage = '<a id="ipCmsSystemNotice" style="'.(!empty($_SESSION['modules']['administrator']['system']['show_system_message']) ? '' : 'display: none;').'" class="ipCmsTopNotice" onclick="modTabs.switchTab(\'moduleGroup'.$systemModuleGroupListId.'\'); change_page(document.getElementById(\'module_'.$systemModule['g_name'].'_'.$systemModule['m_name'].'\'), \'' . $cms->generateUrl($systemModule['id']) . '\'); this.style.display=\'none\'; return false;" href="#">' . $parametersMod->getValue('standard', 'configuration', 'system_translations', 'system_message') . '</a>';
    } else {
      $systemMessage = '';
    }

    $this->html .='
      <div class="all">
        <div class="top_menu">

          ' . $groupsHtml . '
          <a class="logout" href="' . $cms->generateActionUrl('logout') . '">
          ' . $parametersMod->getValue('standard', 'configuration', 'system_translations', 'logout') . '
          </a>
          <a class="ipCmsTopHelp" target="_blank" href="http://www.impresspages.org/help">' . $parametersMod->getValue('standard', 'configuration', 'system_translations', 'help') . '</a>
          '.$systemMessage.'
        </div>
        <div class="top_tabs">
        ' . $modulesHtml . '
        </div>
      </div><!-- class="all" -->
						
		
			
			
			';

    $this->html .= '
      <script type="text/javascript">
      //<![CDATA[
        modTabs.switchFirst();
      //]]>
      </script>
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


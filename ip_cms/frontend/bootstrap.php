<?php

if(Db::connect()){
  $log = new Modules\Administrator\Log\Module();


	$parametersMod = new parametersMod();
	$session = new Frontend\Session();

	$site = new Frontend\Site();

	$site->init();

  /*detect browser language*/
  if((!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == '') && $parametersMod->getValue('standard', 'languages', 'options', 'detect_browser_language') && $site->getCurrentUrl() == BASE_URL && !isset($_SESSION['modules']['standard']['languages']['language_selected_by_browser']) && $parametersMod->getValue('standard', 'languages', 'options', 'multilingual')){
    require_once(BASE_DIR.LIBRARY_DIR.'php/browser_detection/language.php');
    $tmpLangArray = Library\Php\BrowserDetection\Language::getLanguages();
    $tmpBrowserLanguageId = null;
    foreach($tmpLangArray as $key => $lang){
      foreach($site->languages as $key2 => $siteLang){
        if($siteLang['code'] == $lang && $tmpBrowserLanguageId == null){
          $tmpBrowserLanguageId = $siteLang['id'];
        }
      }
    }
    if($tmpBrowserLanguageId == null){
      foreach($tmpLangArray as $key => $lang){
        if(strpos($lang, '-') !== false)
          $lang = substr($lang, 0, strpos($lang, '-'));
        foreach($site->languages as $key2 => $siteLang){
          $tmpSiteCode = $siteLang['code'];
          if(strpos($tmpSiteCode, '-') !== false)
            $tmpSiteCode = substr($tmpSiteLang, 0, strpos($tmpSiteLang, '-'));
          if($tmpSiteCode == $lang  && $tmpBrowserLanguageId == null){
            $tmpBrowserLanguageId = $siteLang['id'];
          }
        }
      }
    }

    if($tmpBrowserLanguageId != $site->currentLanguage['id'] && $tmpBrowserLanguageId !== null)
      header("location:".$site->generateUrl($tmpBrowserLanguageId));
  }
  $_SESSION['modules']['standard']['languages']['language_selected_by_browser'] = true;
  /*eof detect browser language*/

  /*check if the website is closed*/
  if($parametersMod->getValue('standard', 'configuration', 'main_parameters', 'closed_site') && !$site->managementState()){
    echo $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'closed_site_message');
    exit;
  } 
  /*eof check if the website is closed*/


	require(BASE_DIR.THEME_DIR.THEME.'/'.$site->getLayout());

  /* 
    Automatic execution of cron. 
    The best solution is to setup cron service to launch file www.yoursite.com/ip_cron.php few times a day.
    By default fake cron is enabled
  */
  if($parametersMod->getValue('standard', 'configuration', 'advanced_options', 'use_fake_cron') && function_exists('curl_init') && $log->lastLogsCount(60, 'system/cron') == 0){
   // create a new curl resource
   
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, BASE_URL.'ip_cron.php');
    curl_setopt($ch, CURLOPT_REFERER, BASE_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    curl_exec($ch);
  }

  \Db::disconnect();  
  
  

}else   trigger_error("Database access");
             

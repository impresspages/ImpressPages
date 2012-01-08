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
    
    $browserLanguages = Library\Php\BrowserDetection\Language::getLanguages(); 
    $selectedLanguageId = null;
    foreach($browserLanguages as $browserLanguageKey => $browserLanguage){
      foreach($site->languages as $siteLanguageKey => $siteLanguage){
        if(strpos($browserLanguage, '-') !== false) {
          $browserLanguage = substr($browserLanguage, 0, strpos($browserLanguage, '-'));
        }
        if(strpos($siteLanguage['code'], '-') !== false) {
          $siteLanguage['code'] = substr($siteLanguage['code'], 0, strpos($siteLanguage['code'], '-'));
        }
        
        if($siteLanguage['code'] == $browserLanguage){
            $selectedLanguageId = $siteLanguage['id'];
            break;
        }
      }
      if ($selectedLanguageId != null) {
        break;
      }
    }

    if($selectedLanguageId != $site->currentLanguage['id'] && $selectedLanguageId !== null)
      header("location:".$site->generateUrl($selectedLanguageId));
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
             

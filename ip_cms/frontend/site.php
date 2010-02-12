<?php
/**
 * ImpressPages CMS main frontend file
 *
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Frontend;



if (!defined('CMS')) exit;

/** @private */
require_once (__DIR__.'/zone.php');
require_once (__DIR__.'/db.php');
require_once (__DIR__.'/db.php');

/**
 * Main frontend class. Manage zones, pages, modules, generate and parse urls.
 * @package ImpressPages
 */
class Site{
  /** @var array variables in url. Before GET */
  public $urlVars;
  /** @var array variables in GET */
  public $getVars;
  /** @var string zone URL */
  public $zoneUrl;
  /** @var string language url (if the site is multilingual) */
  public $languageUrl; //
  /** @var string current zone name */
  public $currentZone;
  /** @var array all registered languages */
  public $languages;
  /** @var array current language */
  public $currentLanguage;
  /** @var bool true if page does not exists */
  public $error404;

  /** private @var array all site zones */
  protected $zones; //
  /** private @var array zones joined by language*/
  protected $otherZones; //


  public function __construct(){
    if (defined('BACKEND') || defined('CRON')) {
      $this->parseUrl();
      $this->languages = \Frontend\Db::getLanguages();
      if(sizeof($this->languages) > 0)
      $this->currentLanguage = reset($this->languages);
    } else {
      $this->parseUrl();

      $this->languages = \Frontend\Db::getLanguages();
      if(sizeof($this->languages) == 0){
        trigger_error('All website languages are hidden.');
        exit;
      }

      if($this->languageUrl != null){
        foreach($this->languages as $key => $language){
          if($language['url'] == $this->languageUrl){
            $this->currentLanguage = $language;
          }
        }
        if($this->currentLanguage == null){
          $this->currentLanguage = reset($this->languages);
          $this->error404();
        }
      }else
      $this->currentLanguage = reset($this->languages);

      setlocale(LC_ALL, $this->currentLanguage['code']);
    }
  }


  /**
   * @private
   * Prepares main website parameters (current language, current zone and so on)
   * @return void
   */
  public function configZones(){
    if (defined('BACKEND')) {
      $zones = \Frontend\Db::getZones($this->currentLanguage['id']);
      foreach ($zones as $key => $zone) {
        $this->zones[$zone['name']] = $zone;
      }
    } else {
      $zones = \Frontend\Db::getZones($this->currentLanguage['id']);
      	
      if (sizeof($zones) == 0) {
        trigger_error('Please insert at least one zone.');
        exit;
      }
      	
      foreach ($zones as $key => $zone) {
        $this->zones[$zone['name']] = $zone;
        if($this->zoneUrl && $this->zoneUrl == $zone['url'])
        $this->currentZone = $zone['name'];
      }
      	
      if (!$this->currentZone && $this->zoneUrl) {
        $this->error404();
      }

      if (!$this->currentZone) {
        foreach ($this->zones as $key => $zone) { //find first zone.
          $this->currentZone = $key;
          break;
        }
      }
      if (!$this->getZone($this->currentZone)->getCurrentElement()) {
        if (sizeof($this->urlVars) == 0 && (sizeof($this->getVars) == 0 || sizeof($this->urlVars) == 0 && sizeof($this->getVars) == 1 && isset($this->getVars['cms_action']))) { //first zone have no pages.
          $redirect = false;
          foreach ($this->zones as $key => $zone) { //try to find first zone with at least one page
            $tmpZone = $this->getZone($key);
            if ($tmpZone->getAssociatedModuleGroup() == 'standard'
            && $tmpZone->getAssociatedModule() == 'content_management' &&
            $tmpZone->findElement(array(), array())) {
              $this->currentZone = $key;
              $redirect = true;
              header("Location: ".$this->generateUrl(null, $key));
              break;
            }
          }

          if(!$redirect) {
            $this->error404();
          }

        } else {
          $this->error404();
        }
      }
    }

  }

  /**
   *
   * return \Frontend\Zone object
   * @param string $zoneName
   */
  public function getZone($zoneName){
    if(isset($this->zones[$zoneName]))
    {
      if(!isset($this->zones[$zoneName]['object']))
      {
        //initialize zone object
        $tmpZone = $this->zones[$zoneName];
        if ($tmpZone['associated_group'] && $tmpZone['associated_module']) {
          if (file_exists(MODULE_DIR.$tmpZone['associated_group'].'/'.$tmpZone['associated_module'].'/zone.php')) {
            require_once(MODULE_DIR.$tmpZone['associated_group'].'/'.$tmpZone['associated_module'].'/zone.php');
          } else {
            require_once(PLUGIN_DIR.$tmpZone['associated_group'].'/'.$tmpZone['associated_module'].'/zone.php');
          }
          eval ('$tmpZoneObject = new \\Modules\\'.$tmpZone['associated_group'].'\\'.$tmpZone['associated_module'].'\\Zone($tmpZone[\'name\']);');
        } else {
          require_once(FRONTEND_DIR.'default_zone.php');
          $tmpZoneObject = new \Frontend\DefaultZone($tmpZone['name']);
    		  }

    		  $tmpZoneObject->setName($tmpZone['name']);
    		  $tmpZoneObject->setTemplate($tmpZone['template']);
    		  $tmpZoneObject->setTitle($tmpZone['title']);
    		  $tmpZoneObject->setUrl($tmpZone['url']);
    		  $tmpZoneObject->setKeywords($tmpZone['keywords']);
    		  $tmpZoneObject->setDescription($tmpZone['description']);
    		  $tmpZoneObject->setAssociatedModuleGroup($tmpZone['associated_group']);
    		  $tmpZoneObject->setAssociatedModule($tmpZone['associated_module']);


    		  $this->zones[$zoneName]['object'] = $tmpZoneObject;
    		  //end initialize zone object
      }
      return $this->zones[$zoneName]['object'];
    }else{
      //error
      $backtrace = debug_backtrace();
      if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
      trigger_error('Requested zone "'.htmlspecialchars($zoneName).'" does not exist. (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
      else
      trigger_error('Requested zone "'.htmlspecialchars($zoneName).'" does not exist.');

      return false;
    }
  }

  /**
   *
   * return current zone object
   * @param object Zone
   */
  public function getCurrentZone(){
    return $this->getZone($this->currentZone);
  }
  
  
  /**
   *
   * return all registered zones
   * @param array Zone
   */
  public function getZones(){
    $answer = array();
    foreach($this->zones as $key => $zone){
      $answer[] = $this->getZone($zone['name']);
    }
    return $answer;
  }

  /**
   * Finds website zone by module group and name.
   * @param string $group module group key
   * @param string $name module key
   * @return array
   */
  public function getZoneByModule($group, $module){
    $answer = null;
    foreach($this->zones as $key => $zone){
      if ($zone['associated_group'] == $group && $zone['associated_module'] == $module) {
        $answer = $this->getZone($zone['name']);
      }
    }
    return $answer;
  }


  /**
   * Parse url and detect language url, zone url, url variables, get variables
   * @private
   */
  public function parseUrl(){
    global $parametersMod;
    // example www.example.com/lt/left-menu/var1/var2/?mod=123
    // result:
    //  languageUrl - 'lt';
    //  zoneUrl - 'left-menu';
    //	urlVars - array(var1, var2)
    //	getVars - array("mod"=>"123")
    	
    //$urlVarsStr = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['SCRIPT_NAME'], '/') + 1);
    $scriptPath = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/') + 1 );
    if( strpos($_SERVER['REQUEST_URI'] , $scriptPath ) === 0 ) { //script location is the same as url path
      $urlVarsStr = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['SCRIPT_NAME'], '/') + 1);
    } else { //script is in the other location than request url (urls are rewriten)
      $urlVarsStr = substr($_SERVER['REQUEST_URI'], 1);
    }
    
    $question_mark = strpos($urlVarsStr, '?');
    if($question_mark !== false){
      $urlVarsStr = substr($urlVarsStr, 0, $question_mark);
    }
    	
    $urlVarsStr = rtrim( $urlVarsStr,  "/");
    $urlVars = explode('/', $urlVarsStr);
     
    for($i=0; $i< sizeof($urlVars); $i++){
      $urlVars[$i] = urldecode($urlVars[$i]);
    }
    if($parametersMod->getValue('standard', 'languages', 'options', 'multilingual'))
      $this->languageUrl = urldecode(array_shift($urlVars));

    $this->zoneUrl = urldecode(array_shift($urlVars));
    $this->urlVars = $urlVars;

    $this->getVars = array();
    foreach ($_GET as $key => $value){
      $this->getVars[$key] = $value;
    }

  }

  /*
   * Redirect to another page if required
   * @private
   * @return null
   */
  public function makeRedirect(){
    $curEl =  $this->zones[$this->currentZone]['object']->getCurrentElement();
    if($curEl){ //if page exist.
      switch($curEl->getType()){
        case 'subpage':
        case 'redirect':
          $currentUrl = $this->generateCurrentUrl();
          if(isset($_SESSION['frontend']['redirects'][$currentUrl])){
            unset($_SESSION['frontend']['redirects']);         
            return;//infinite redirect loop. Stop redirecting;
          } else {
            $_SESSION['frontend']['redirects'][$currentUrl] = 1; //to detect infinite loop
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: '.$curEl->getLink());
            \Db::disconnect();
            exit();
          }
        break;
      }
    }
    unset($_SESSION['frontend']['redirects']);  
  }  
  
  /**
   * Detects which template to use by current page (URL)
   * @private
   * @return string
   */

  public function choseTemplate(){
    global $parametersMod;
    if($this->error404 && $parametersMod->getValue('standard', 'configuration', 'error_404', 'error_page_template') != '')
    return $parametersMod->getValue('standard', 'configuration', 'error_404', 'error_page_template');

    if(isset($this->currentZone))
    return($this->zones[$this->currentZone]['template']);
  }

  /**
   * Generates current URL. Used for logging, links to the same page and so on.
   * @return string URL
   */
  public function generateCurrentUrl(){
    $pageURL = 'http://';
    if ($_SERVER["SERVER_PORT"] != "80") {
      $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
      $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    return $pageURL;
  }







  /**
   * Generates link to page.
   * @param int $languageId id of language
   * @param string $zoneKey key of zone at whish the link should be generated.
   * @param array $urlVars array of additional url variables
   * @param array $getVars array of additional get variables
   * @return link to fist page of currentLanguage
   */
  public function generateUrl($languageId=null, $zoneName = null, $urlVars = null, $getVars = null){
    global $parametersMod;
    	
    if($languageId == null)
    $languageId = $this->currentLanguage['id'];
    	
    /*generates link to first page of current language*/
    // get parameter for cms management
    if(isset($this->getVars['cms_action']) && $this->getVars['cms_action'] == 'manage'){
      if($getVars == null)
      $getVars = array('cms_action' => 'manage');
      else
      $getVars['cms_action'] = 'manage';
    }
    // get parameter for cms management

    if($parametersMod->getValue('standard', 'languages', 'options', 'multilingual')){
      $answer = BASE_URL.urlencode($this->languages[$languageId]['url']).'/';
    }else
    $answer = BASE_URL;

    if($zoneName != null){
      if($languageId == $this->currentLanguage['id']){ //current language
        if(isset($this->zones[$zoneName])){
          $answer .= urlencode($this->zones[$zoneName]['url']).'/';
        }else{
          $backtrace = debug_backtrace();
          if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
          trigger_error('Undefined zone '.$zoneName.' (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
          else
          trigger_error('Undefined zone '.$zoneName);
          return '';
        }
      }else{
        if(!isset($this->otherZones[$languageId]))
        $this->otherZones[$languageId] = \Frontend\Db::getZones($languageId);

        if(isset($this->otherZones[$languageId])){
          $answer .= urlencode($this->otherZones[$languageId][$zoneName]['url']).'/';
        }else{
          $backtrace = debug_backtrace();
          if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
          trigger_error('Undefined zone '.$zoneName.' (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
          else
          trigger_error('Undefined zone '.$zoneName);
          return '';
        }
      }


    }

    if($urlVars){
      foreach($urlVars as $key => $value)
        $answer .= urlencode($value).'/';
    }
    if($getVars && sizeof($getVars) > 0){
      $first = true;
      foreach($getVars as $key => $value){
        if($first)
        $answer .= '?'.$key.'='.urlencode($value);
        else
        $answer .= '&amp;'.$key.'='.urlencode($value);
        $first = false;
      }
    }

    return $answer;
  }

  /**
   * Generates link to first page of current language.
   * @param string $zone_key key of zone at whish the link should be generated.
   * @param array $urlVars array of additional url variables
   * @param array $getVars array of additional get variables
   * @return string link to fist page of defined language
   */
  public function generateLanguageUrl($langUrl){
    $answer = BASE_URL.urlencode($langUrl);
    // get parameter for cms management
    if(isset($this->getVars['cms_action']) && $this->getVars['cms_action'] == 'manage')
    $answer .= '/?cms_action=manage';
    // get parameter for cms management
    return $answer;
  }

  /**
   * Finds the reason why the user come to unexisting URL
   * @return string error message
   */
  public function error404Message(){
    global $parametersMod;
    //find reason
    if(!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == ''){
      $message = $parametersMod->getValue('standard','configuration','error_404', 'error_mistyped_url', $this->currentLanguage['id']);
    }else{
      if(strpos($_SERVER['HTTP_REFERER'], BASE_URL) < 5 && strpos($_SERVER['HTTP_REFERER'], BASE_URL) !== false){
        $message = $parametersMod->getValue('standard','configuration','error_404', 'error_broken_link_inside', $this->currentLanguage['id']);
      }if(strpos($_SERVER['HTTP_REFERER'], BASE_URL) === false){
        $message = $parametersMod->getValue('standard','configuration','error_404', 'error_broken_link_outside', $this->currentLanguage['id']);
      }
    }
    //end find reason
    return $message;
  }


  /**
   * Finds the reason why the user come to unexisting URL
   * Depending on configuraition, redirects to first page or returns error message.
   * @return string error message
   */
  public function error404(){
    require_once (BASE_DIR.MODULE_DIR.'administrator/email_queue/module.php');
    global $parametersMod;
    $this->error404 = true;
    $headers = 'MIME-Version: 1.0'. "\r\n";
    $headers .= 'Content-type: text/html; charset='.CHARSET."\r\n";
    $headers .= 'From: sender@sender.com' . "\r\n";

    $message = '';
    
    //error reporting
    if(!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == ''){
      if(defined('ERRORS_SEND') && ERRORS_SEND && $parametersMod->getValue('standard', 'configuration','error_404', 'report_mistyped_urls', $this->currentLanguage['id']))
        $message = $this->error404Message().'
             Link: '.$this->generateCurrentUrl().'
             Http referer: '.$_SERVER['HTTP_REFERER'];
    }else{
      if(strpos($_SERVER['HTTP_REFERER'], BASE_URL) < 5 && strpos($_SERVER['HTTP_REFERER'], BASE_URL) !== false){
        if(defined('ERRORS_SEND') && ERRORS_SEND && $parametersMod->getValue('standard', 'configuration','error_404', 'report_broken_inside_link', $this->currentLanguage['id']))
          $message = $this->error404Message().'
             Link: '.$this->generateCurrentUrl().'
             Http referer: '.$_SERVER['HTTP_REFERER'];
      }if(strpos($_SERVER['HTTP_REFERER'], BASE_URL) === false){
        if(defined('ERRORS_SEND') && ERRORS_SEND && $parametersMod->getValue('standard', 'configuration','error_404', 'report_broken_outside_link', $this->currentLanguage['id']))
        $message = $this->error404Message().'
             Link: '.$this->generateCurrentUrl().'
             Http referer: '.$_SERVER['HTTP_REFERER'];
      }
      	
    }
    if ($message != '') {
      $queue = new \Modules\administrator\email_queue\Module();
  	  $queue->addEmail($parametersMod->getValue('standard', 'configuration', 'main_parameters', 'email', $this->currentLanguage['id']), $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'name', $this->currentLanguage['id']), ERRORS_SEND, '', BASE_URL." ERROR", $message, false, true);
  	  //we need to set currentLanguage id if this function is trown at $site object construction time.
  	  $queue->send();
    }
    
    //end error reporting

    if(
    $parametersMod->getValue('standard', 'configuration', 'error_404', 'send_to_main_page')
    &&
    ($this->languageUrl != '' || sizeof($this->urlVars) > 0 || sizeof($this->getVars) > 0 || $this->zoneUrl != '')
    ){
      header("Location: ".BASE_URL);
      exit;
    }else{
      header("HTTP/1.0 404 Not Found");
    }
  }

  /**
   * Some modules need to make some actions before any output.
   * This function detects such requirements and executes specified module.
   * If you need to use this feature, simply POST (or GET) two variables:
   * @private
   * $_REQUEST['module_gorup']
   * $_REQUEST['module_name']
   * This function will include file actions.php on specified module directory and axecute method "make_actions()" on class actions_REQUEST['module_gorup']_REQUEST['module_name']
   */
  public function makeActions(){
    if(sizeof($_REQUEST) > 0){
      if(isset($_REQUEST['module_group']) && isset($_REQUEST['module_name'])){ //old deprecated way
        //actions may be set by post or get. The prime way is trouht post. But in some cases it is not possible
        $newModule = \Db::getModule(null, $_REQUEST['module_group'], $_REQUEST['module_name']);
        if($newModule){
          if($newModule['core']){
            require_once(BASE_DIR.MODULE_DIR.$newModule['g_name'].'/'.$newModule['m_name'].'/actions.php');
          } else {
            require_once(BASE_DIR.PLUGIN_DIR.$newModule['g_name'].'/'.$newModule['m_name'].'/actions.php');
          }
          eval('$tmpModule = new \\Modules\\'.$newModule['g_name'].'\\'.$newModule['m_name'].'\\Actions();');
          $tmpModule->makeActions();
        }else{
          $backtrace = debug_backtrace();
          if(isset($backtrace[0]['file']) && isset($backtrace[0]['line']))
            trigger_error("Requested module (".$_REQUEST['module_group'].">".$_REQUEST['module_name'].") does not exitst. (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ");
          else
            trigger_error("Requested module (".$_REQUEST['module_group'].">".$_REQUEST['module_name'].") does not exitst.");
        }
      }
    }
    
    $this->getZone($this->currentZone)->makeActions();
  }

  /**
   * @return string title of current active page
   */
  public function getTitle(){
    $curEl =  $this->zones[$this->currentZone]['object']->getCurrentElement();
    if($curEl && $curEl->getPageTitle() != '')
    return $curEl->getPageTitle();
    else
    return $this->zones[$this->currentZone]['title'];
  }
  /**
   * @return string description of current active page
   */
  public function getDescription(){
    $curEl =  $this->zones[$this->currentZone]['object']->getCurrentElement();
    if($curEl && $curEl->getDescription() != '')
    return $curEl->getDescription();
    else
    return $this->zones[$this->currentZone]['description'];
  }
  /**
   * @return string url of current active page
   */
  public function getUrl(){
    $curEl =  $this->zones[$this->currentZone]['object']->getCurrentElement();
    if($curEl && $curEl->getUrl() != '')
    return $curEl->getUrl();
    else
    return $this->zones[$this->currentZone]['url'];
  }
  /**
   * @return string keywords of current active page
   */
  public function getKeywords(){
    $curEl =  $this->zones[$this->currentZone]['object']->getCurrentElement();
    if($curEl && $curEl->getKeywords() != '')
    return $curEl->getKeywords();
    else
    return $this->zones[$this->currentZone]['keywords'];
  }


  /**
   * @return bool true if the system is in management state
   */
  public function managementState(){
    return (isset($this->getVars['cms_action']) && $this->getVars['cms_action'] == 'manage');
  }

  /**
   * Print out the content or management tools of current page.
   * @return void
   */
  public function generateContent(){
    $answer = '';
    if($this->currentZone){
      if($this->error404){
        $answer .= $this->error404Message();
        global $log;
        $log->log("system", "error404", $this->generateCurrentUrl()." ".$this->error404Message());
      }else{
        if($this->managementState()){

          if(!isset($_SESSION['backend_session']['user_id'])){
            $answer = '<script type="text/javascript">window.location = \''.BASE_URL.BACKEND_MAIN_FILE.'\';</script>';
          }else{
            $answer .= $this->getZone($this->currentZone)->getCurrentElement()->generateManagement();
          }
        }else{
          $answer .= $this->getZone($this->currentZone)->getCurrentElement()->generateContent();
        }
      }
    }

    return $answer;
  }


  public function requireTemplate($file){
    if (file_exists(BASE_DIR.THEME_DIR.THEME.'/modules/'.$file)) {
      require_once(BASE_DIR.THEME_DIR.THEME.'/modules/'.$file);
    } else {
      if(file_exists(BASE_DIR.MODULE_DIR.$file)){
        require_once(BASE_DIR.MODULE_DIR.$file);        
      } else {
        if(file_exists(BASE_DIR.PLUGIN_DIR.$file)){
          require_once(BASE_DIR.PLUGIN_DIR.$file);  
        } else {
          $backtrace = debug_backtrace();
          if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
            trigger_error('Required template does not exist '.$file.'. (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
          else
            trigger_error('Required template does not exist '.$file.'.');
        }     
      }
    }
  }

  public function requireConfig($file){
    if (file_exists(BASE_DIR.CONFIG_DIR.$file)) {
      require_once(BASE_DIR.CONFIG_DIR.$file);
    } else {
      if (file_exists(BASE_DIR.MODULE_DIR.$file)) {
        require_once(BASE_DIR.MODULE_DIR.$file);
      } else {
        require_once(BASE_DIR.PLUGIN_DIR.$file);
      }
    }
  }

  public function usedUrl($url){

    $systemDirs = array();

    $systemDirs['coredir'] = 1;
    $systemDirs[FILE_DIR] = 1;
    $systemDirs[IMAGE_DIR] = 1;
    $systemDirs[VIDEO_DIR] = 1;
    if(isset($systemDirs[$url]))
    return true;
    else
    return false;
  }


  public function getBreadcrumb(){
    $zone = $this->getCurrentZone();
    return $zone->getBreadcrumb();
  }
  
  
  public function getCurrentElement(){
    $zone = $this->getCurrentZone();
    return $zone->getCurrentElement(); 
  }
  
  
  public function dispatchEvent($moduleGroup, $moduleName, $event, $parameters){
    $sql = "select m.name as m_name, mg.name as mg_name from `".DB_PREF."module_group` mg, `".DB_PREF."module` m where m.group_id = mg.id";
    $rs = mysql_query($sql);
    if($rs){
      while($lock = mysql_fetch_assoc($rs)){
        if(file_exists(BASE_DIR.MODULE_DIR.$lock['mg_name'].'/'.$lock['m_name']."/system.php")){
          require(BASE_DIR.MODULE_DIR.$lock['mg_name'].'/'.$lock['m_name']."/system.php");         
          eval('$moduleSystem = new \\Modules\\'.$lock['mg_name'].'\\'.$lock['m_name'].'\\System();');
          if(method_exists($moduleSystem, 'catchEvent')){
            $moduleSystem->catchEvent($moduleGroup, $moduleName, $event, $parameters);
          }
        }
      }
    }        
  }
}


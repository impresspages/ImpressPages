<?php
/*
 * ImpressPages CMS main frontend file
 *
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

if (!defined('CMS')) exit;


require_once (__DIR__.'/zone.php');
require_once (__DIR__.'/db.php');
require_once (__DIR__.'/language.php');



/**
 *
 * Main frontend class. Each time the page is loaded the global instance of this class called $site is created.
 *
 * Access it anywhere by using:
 *
 * global $site;
 *
 * Use it to get information about the website:
 *
 * current language
 *
 * current zone
 *
 * current page
 *
 * current url
 *
 * ...
 *
 *
 */
class Site{

    /** array of variables in URL. If page URL is http://yoursite.com/en/zone_url/lorem/ipsum, then result will be array('lorem', 'ipsum') */
    public $urlVars;

    /** array of GET variables. Use this function instead of direcly acessing $_GET array for code flexibility. */
    public $getVars;

    /** bool true if page does not exists */
    private $error404;
    

    /** @deprecated use getCurrentZone()->getUrl() instead */
    public $zoneUrl;

    /** @deprecated use getCurrentLanguage->getUrl() insead */
    public $languageUrl; //

    /** @deprecated use getCurrentZone()->getName() instead */
    public $currentZone;

    /** @deprecated use getLanguages() instead */
    public $languages;

    /** @deprecated use getCurrentLanguage() instead */
    public $currentLanguage;

    /** array javascript files, required by current page */
    private $_requiredJs;

    /** array required javascript files */
    private $requiredJavascript = array();

    /** array required css files */
    private $requiredCss = array();

    /** string HTML or any other output. If is not null, it will be send to the output. If it is null, required page by request URL will be generated  */
    protected $output;

    /** int Revision of current page.  */
    protected $revision;

    protected $zones;
    protected $otherZones;


    public function __construct(){


    }

    /**
     *
     * @return array - all variables in URL. If page URL is http://yoursite.com/en/zone_url/lorem/ipsum, then result will be array('lorem', 'ipsum');
     *
     */
    public function getUrlVars(){
        return $this->urlVars;
    }


    /**
     *
     * @return array - all GET variables. Use this function instead of direcly accessing $_GET array for code flexibility.
     *
     */
    public function getGetVars(){
        return $this->getVars;
    }

    /**
     *
     * @param data array from database
     * @return Language
     *
     *
     */
    private function createLanguage($data){
        $language = new \Frontend\Language($data['id'], $data['code'], $data['url'], $data['d_long'], $data['d_short'], $data['visible'], $data['text_direction']);
        return $language;
    }

    /**
     *
     * @return array - all website languages. Each element is an object Language
     *
     */
    public function getLanguages(){
        $languages = array();
        foreach($this->languages as $key => $data){
            $languages[] = $this->createLanguage($data);
        }
        return $languages;
    }

    /**
     *
     * @return Language
     *
     */
    public function getLanguageById($id){
        $answer = false;
        foreach($this->languages as $key => $data){
            if($data['id'] == $id){
                $answer = $this->createLanguage($data);
            }
        }
        return $answer;
    }

    /**
     *
     * @return Language
     *
     */
    public function getLanguageByUrl($url){
        $answer = false;
        foreach($this->languages as $key => $data){
            if($data['url'] == $url){
                $answer = $this->createLanguage($data);
            }
        }
        return $answer;
    }

    /**
     *
     * @return Language - current language
     *
     */
    public function getCurrentLanguage(){
        return $this->createLanguage($this->currentLanguage);
    }

    /**
     *
     * Initialize required components. Executed once at startup.
     *
     */
    public function init(){
        global $dispatcher;
        $dispatcher->notify(new \Ip\Event($this, 'site.beforeInit', null));

        if (get_magic_quotes_gpc()) { //fix magic quotes option
            $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
            while (list($key, $val) = each($process)) {
                foreach ($val as $k => $v) {
                    unset($process[$key][$k]);
                    if (is_array($v)) {
                        $process[$key][stripslashes($k)] = $v;
                        $process[] = &$process[$key][stripslashes($k)];
                    } else {
                        $process[$key][stripslashes($k)] = stripslashes($v);
                    }
                }
            }
            unset($process);
        }



        if (defined('BACKEND') || defined('CRON') || defined('SITEMAP')) {
            $this->parseUrl();
            $this->languages = \Frontend\Db::getLanguages(true);

            if(sizeof($this->languages) > 0){
                $this->currentLanguage = reset($this->languages);
            }
        } else {
            $this->parseUrl();

            $this->languages = \Frontend\Db::getLanguages(true);
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
            } else {
                $this->currentLanguage = reset($this->languages);
            }

            setlocale(LC_ALL, $this->currentLanguage['code']);
        }        
        
        
        
        $this->configZones();

        $this->modulesInit();

        if ($this->error404) {
            $this->dispatchError404();
        }
    }
    
    private function error404() {
        $this->error404 = true;
    }
    
    private function dispatchError404() {
        global $dispatcher;
        $dispatcher->notify(new \Ip\Event($this, 'site.error404', null));
    }

    /**
     *
     * Prepare main website parameters (current zone and so on). Executed once at startup.
     *
     */
    private function configZones(){
    if (defined('BACKEND') || defined('SITEMAP')) {
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

        }

    }

    public function checkError404(){
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

    /**
     *
     * @param $zoneName Name of zone you wish to get
     * @return Zone
     *
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

                $tmpZoneObject->setId($tmpZone['id']);
                $tmpZoneObject->setName($tmpZone['name']);
                $tmpZoneObject->setLayout($tmpZone['template']);
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
            return false;
        }
    }

    /**
     *
     * @return Zone Current zone object
     *
     */
    public function getCurrentZone(){
        return $this->getZone($this->currentZone);
    }

    /**
     *
     * @return String Current zone name
     *
     */
    public function getCurrentZoneName(){
        return $this->currentZone;
    }

    /**
     *
     * @return array All registered zones. Use with caution. On big websites it can be very resource demanding operation because it requires all zone objects to be created.
     *
     */
    public function getZones(){
        $answer = array();
        foreach($this->zones as $key => $zone){
            $answer[] = $this->getZone($zone['name']);
        }
        return $answer;
    }

    /**
     * Find website zone by module group and name.
     * @param $group Module group name (string)
     * @param $module Module name (string)
     * @return Zone
     */
    public function getZoneByModule($group, $module){
        $answer = false;
        foreach($this->zones as $key => $zone){
            if ($zone['associated_group'] == $group && $zone['associated_module'] == $module) {
                $answer = $this->getZone($zone['name']);
            }
        }
        return $answer;
    }

    /**
     * Parse url and detect language url, zone url, url variables, get variables
     *
     * <b>Example URL</b>
     *
     * www.example.com/lt/left-menu/var1/var2/?mod=123
     *
     * <b>Result:</b>
     *
     * languageUrl - 'lt'
     *
     * zoneUrl - 'left-menu'
     *
     * urlVars - array(var1, var2)
     *
     * getVars - array("mod"=>"123")
     *
     * @return void
     *
     */
    private function parseUrl(){
        global $parametersMod;

         
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
     * @return null
     *
     */
    public function makeRedirect(){
        $curEl =  $this->getCurrentElement();
        if($curEl){ //if page exist.
            switch($curEl->getType()){
                case 'subpage':
                case 'redirect':
                    $currentUrl = $this->getCurrentUrl();
                    if(isset($_SESSION['frontend']['redirects'][$currentUrl])){
                        unset($_SESSION['frontend']['redirects']);
                        return;//infinite redirect loop. Stop redirecting;
                    } else {
                        if (!isset($_GET['cms_action']) || $_GET['cms_action'] != 'manage_content') {
                            $_SESSION['frontend']['redirects'][$currentUrl] = 1; //to detect infinite loop
                            header('HTTP/1.1 301 Moved Permanently');
                            header('Location: '.$curEl->getLink());

                            \Db::disconnect();
                            exit();
                        }
                    }
                    break;
            }
        }
        unset($_SESSION['frontend']['redirects']);
    }
    /**
     *
     * @return string Current layout file
     *
     */
    public function getLayout(){
        global $parametersMod;

        if ($this->error404 && $parametersMod->getValue('standard', 'configuration', 'error_404', 'error_page_template') != '') {
            return $parametersMod->getValue('standard', 'configuration', 'error_404', 'error_page_template');
        }

        if(isset($this->currentZone)){
            return($this->getCurrentZone()->getLayout());
        }
    }

    /**
     * Get current URL.
     * @return string Current URL
     */
    public function getCurrentUrl(){
        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on"){
            $pageURL .= "s";
        }
        $pageURL .= '://';
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    /**
     * @deprecated Use getCurrentUrl() instead;
     *
     * @return string - Current URL
     */
    public function generateCurrentUrl(){
     return $this->getCurrentUrl();
    }

    /**
     * Generate link to website. Use it with no arguments to get link to main page of current language.
     *
     * Don't use it to generate link to existing page. To get link to existing page, use method getLink() on Element object.
     *
     * @param $languageId
     *   Id of language
     * @param $zoneName
     *   Zone name
     * @param $urlVars
     *   Array of additional url variables. Eg. array('var1', 'var2')
     * @param $getVars
     *   Array of additional get variables. Eg. array('var1'='val1', 'val2'='val2')
     * @return string - requested link or link to first page of current language if all parameters are not specified or null
     */
    public function generateUrl($languageId=null, $zoneName = null, $urlVars = null, $getVars = null){
        global $parametersMod;
         
        if($languageId == null){
            $languageId = $this->currentLanguage['id'];
        }
         
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
        }else{
            $answer = BASE_URL;
        }

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
     * Some modules need to make some actions before any output.
     * This function detects such requirements and executes required action of specified module.
     * If you need to use this feature, simply POST (or GET) thre variables:
     * $_REQUEST['g']
     * $_REQUEST['m']
     * $_REQUEST['a']
     * This function will execute method $_REQUEST['a'] on class \Modules\REQUEST['g']\REQUEST['m']\Controller
     *
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
                    if(isset($backtrace[0]['file']) && isset($backtrace[0]['line'])) {
                        trigger_error("Requested module (".$_REQUEST['module_group']." / ".$_REQUEST['module_name'].") does not exitst. (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ");
                    } else {
                        trigger_error("Requested module (".$_REQUEST['module_group']." / ".$_REQUEST['module_name'].") does not exitst.");
                    }
                }
            }


            if(isset($_REQUEST['g']) && isset($_REQUEST['m'])) { //new way
                $newModule = \Db::getModule(null, $_REQUEST['g'], $_REQUEST['m']);
                if($newModule){
                    if($newModule['core']){
                        require_once(BASE_DIR.MODULE_DIR.$newModule['g_name'].'/'.$newModule['m_name'].'/controller.php');
                    } else {
                        require_once(BASE_DIR.PLUGIN_DIR.$newModule['g_name'].'/'.$newModule['m_name'].'/controller.php');
                    }
                    eval('$tmpModule = new \\Modules\\'.$newModule['g_name'].'\\'.$newModule['m_name'].'\\Controller();');
                    $function = 'index';
                    if (isset($_REQUEST['a'])) {
                        $function = $_REQUEST['a'];
                    }
                    if (method_exists($tmpModule, $function)) {
                        if ($tmpModule->allowAction($function)) {
                            call_user_func(array($tmpModule, $function));
                        }
                    } else {
                        trigger_error("Requested action (".$_REQUEST['g']." / ".$_REQUEST['m']." ".$_REQUEST['a']."()) does not exitst.");
                    }
                } else {
                    $backtrace = debug_backtrace();
                    if(isset($backtrace[0]['file']) && isset($backtrace[0]['line'])) {
                        trigger_error("Requested module (".$_REQUEST['g']." / ".$_REQUEST['m'].") does not exitst. (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ");
                    } else {
                        trigger_error("Requested module (".$_REQUEST['g']." / ".$_REQUEST['m'].") does not exitst.");
                    }
                }

            }


        }

        $this->getZone($this->currentZone)->makeActions(); //old deprecated way. Need to refactor to actions



    }

    /**
     *
     * @return string title of current page
     *
     */
    public function getTitle(){
        $curEl =  $this->zones[$this->currentZone]['object']->getCurrentElement();
        if($curEl && $curEl->getPageTitle() != '')
        return $curEl->getPageTitle();
        else
        return $this->zones[$this->currentZone]['title'];
    }

    /**
     *
     * @return string description of current page
     *
     */
    public function getDescription(){
        $curEl =  $this->zones[$this->currentZone]['object']->getCurrentElement();
        if($curEl && $curEl->getDescription() != '')
        return $curEl->getDescription();
        else
        return $this->zones[$this->currentZone]['description'];
    }

    /**
     *
     * @return string url of current page. This is not a complete URL. It is only url parameter of current page.
     *
     */
    public function getUrl(){
        $curEl =  $this->zones[$this->currentZone]['object']->getCurrentElement();
        if($curEl && $curEl->getUrl() != '')
        return $curEl->getUrl();
        else
        return $this->zones[$this->currentZone]['url'];
    }

    /**
     *
     * @return string keywords of current page
     *
     */
    public function getKeywords(){
        $curEl =  $this->zones[$this->currentZone]['object']->getCurrentElement();
        if($curEl && $curEl->getKeywords() != '')
        return $curEl->getKeywords();
        else
        return $this->zones[$this->currentZone]['keywords'];
    }

    /**
     *
     * @return bool true if the system is in management state
     *
     */
    public function managementState(){
        return (isset($this->getVars['cms_action']) && $this->getVars['cms_action'] == 'manage');
    }

    /**
     *
     * Import required template file or modified version of it from current theme directory.
     *
     * @param $file File name of template that need to be required relative to MODULE_DIR folder.
     *
     * <b>Example:</b>
     *
     * requireTemplate('group/module/template.php');
     * this line will try to require such files:
     * BASE_DIR.THEME_DIR.THEME.'/modules/'.'group/module/template.php'; //customized template file in current theme
     * BASE_DIR.MODULE_DIR.'group/module/template.php';  //original template in module directory
     * BASE_DIR.PLUGIN_DIR.'group/module/template.php';  //original template in plugin directory
     *
     */
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

    /**
     *
     * Import required configuration file or modified version of it from CONFIG_DIR directory.
     * @param $file File name of configuration file that needs to be required (relative to MODULE_DIR folder).
     * <b>Example:</b>
     * requireConfig('group/module/config.php');
     * this line will try to require such files:
     * BASE_DIR.CONFIG_DIR.'group/module/config.php'; //customized config file in config directory
     * BASE_DIR.MODULE_DIR.'group/module/config.php'; //original module config file
     * BASE_DIR.PLUGIN_DIR.'group/module/config.php'; //original plugin config file
     *
     */
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

    /**
     *
     * Beginning of page URL can conflict with CMS system/core folders. This function checks if the folder can be used in URL beginning.
     *
     * @param $folderName
     * @return bool true if URL is reserved for CMS core
     *
     */
    public function usedUrl($folderName){

        $systemDirs = array();

        $systemDirs[PLUGIN_DIR] = 1;
        $systemDirs[CONFIG_DIR] = 1;
        $systemDirs[THEME_DIR] = 1;
        $systemDirs[LIBRARY_DIR] = 1;
        $systemDirs[FILE_DIR] = 1;
        $systemDirs[IMAGE_DIR] = 1;
        $systemDirs[VIDEO_DIR] = 1;
        $systemDirs[AUDIO_DIR] = 1;
        $systemDirs['install'] = 1;
        $systemDirs['update'] = 1;
        if(isset($systemDirs[$folderName])){
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * @return array Each element in array is an Element
     *
     */
    public function getBreadcrumb(){
        $zone = $this->getCurrentZone();
        return $zone->getBreadcrumb();
    }

    /**
     *
     * @return Element - Current page
     *
     */
    public function getCurrentElement(){
        $zone = $this->getCurrentZone();
        return $zone->getCurrentElement();
    }

    /**
     *
     * Dispatch any event to system. Any module can catch this event.
     *
     *
     * @param $moduleGroup group name of module whish throws the event
     * @param $moduleName name of module whish throws the event
     * @param $event event name
     * @param $parameters array of parameters. You can decide what to pass here.
     *
     * To catch the event, create "system.php" file in your plugin (module) directory with content:
     *
     * <?php
     *
     * namespace Modules\your_plugin_group\your_plugin_name;
     *
     * if (!defined('FRONTEND')&&!defined('BACKEND')) exit;
     *
     * class System{
     *   public function catchEvent($moduleGroup, $moduleName, $event, $parameters){
     *       //your actions
     *   }
     * }
     *  ?>
     */
    public function dispatchEvent($moduleGroup, $moduleName, $event, $parameters){
        $sql = "select m.core as m_core, m.name as m_name, mg.name as mg_name from `".DB_PREF."module_group` mg, `".DB_PREF."module` m where m.group_id = mg.id";
        $rs = mysql_query($sql);
        if($rs){
            while($lock = mysql_fetch_assoc($rs)){
                if($lock['m_core']){
                    $dir = BASE_DIR.MODULE_DIR;
                } else {
                    $dir = BASE_DIR.PLUGIN_DIR;
                }

                if(file_exists($dir.$lock['mg_name'].'/'.$lock['m_name']."/system.php")){
                    require_once($dir.$lock['mg_name'].'/'.$lock['m_name']."/system.php");
                    eval('$moduleSystem = new \\Modules\\'.$lock['mg_name'].'\\'.$lock['m_name'].'\\System();');
                    if(method_exists($moduleSystem, 'catchEvent')){
                        $moduleSystem->catchEvent($moduleGroup, $moduleName, $event, $parameters);
                    }
                }
            }
        }
    }

    public function modulesInit(){
        $sql = "select m.core as m_core, m.name as m_name, mg.name as mg_name from `".DB_PREF."module_group` mg, `".DB_PREF."module` m where m.group_id = mg.id";
        $rs = mysql_query($sql);
        if($rs){
            while($lock = mysql_fetch_assoc($rs)){
                if($lock['m_core']){
                    $dir = BASE_DIR.MODULE_DIR;
                } else {
                    $dir = BASE_DIR.PLUGIN_DIR;
                }
                if(file_exists($dir.$lock['mg_name'].'/'.$lock['m_name']."/system.php")){
                    require_once($dir.$lock['mg_name'].'/'.$lock['m_name']."/system.php");
                    eval('$moduleSystem = new \\Modules\\'.$lock['mg_name'].'\\'.$lock['m_name'].'\\System();');
                    if(method_exists($moduleSystem, 'init')){
                        $moduleSystem->init();
                    }
                }
            }
        }
    }


    public function setOutput ($output) {
        $this->output = $output;
    }

    public function getOutput () {
        return $this->output;
    }


    public function generateOutput() {
        global $site;
        global $log;
        global $dispatcher;
        global $parametersMod;
        global $session;

        if (!isset($this->output)) {
            $this->output = \Ip\View::create(BASE_DIR.THEME_DIR.THEME.'/'.$this->getLayout(), array())->render();
        }

        return $this->output;
    }


    public function addCss($file, $stage = 1) {
        $this->requiredCss[(int)$stage][$file] = $file;
    }

    public function removeCss($file) {
        foreach($this->requiredCss as $levelKey => &$level) {
            if (isset($this->requiredCss[$levelKey][$file])) {
                unset($this->requiredCss[$levelKey][$file]);
            }
        }
    }

    public function addJavascript($file, $stage = 1) {
        $this->requiredJavascript[(int)$stage][$file] = $file;
    }

    public function removeJavascript($file) {
        foreach($this->requiredJavascript as $levelKey => &$level) {
            if (isset($this->requiredJavascript[$levelKey][$file])) {
                unset($this->requiredJavascript[$levelKey][$file]);
            }
        }
    }

    public function generateHead() {

        ksort($this->requiredCss);
        $cssFiles = array();
        foreach($this->requiredCss as $levelKey => $level) {
            $cssFiles = array_merge($cssFiles, $level);
        }

        $data = array (
            'title' => $this->getTitle(),
            'keywords' => $this->getKeywords(),
            'description' => $this->getDescription(),
            'favicon' => BASE_URL.'favicon.ico',
            'charset' => CHARSET,
            'css' => $cssFiles
        );

        return \Ip\View::create(BASE_DIR.MODULE_DIR.'standard/configuration/view/head.php', $data)->render();
    }

    public function generateJavascript() {
        $revision = $this->getRevision();


        ksort($this->requiredJavascript);
        $javascriptFiles = array();
        foreach($this->requiredJavascript as $levelKey => $level) {
            $javascriptFiles = array_merge($javascriptFiles, $level);
        }

        
        $data = array (
            'ipBaseUrl' => BASE_URL,
            'ipLibraryDir' => LIBRARY_DIR,
            'ipThemeDir' => THEME_DIR,
            'ipModuleDir' => MODULE_DIR,
            'ipTheme' => THEME,
            'ipManagementUrl' => $this->generateUrl(),
            'ipZoneName' => $this->getCurrentZone()->getName(),
            'ipPageId' => $this->getCurrentElement() ? $this->getCurrentElement()->getId() : null,
            'ipRevisionId' => $revision['revisionId'],
            'javascript' => $javascriptFiles
        );

        return \Ip\View::create(BASE_DIR.MODULE_DIR.'standard/configuration/view/javascript.php', $data)->render();


    }


    public function generateBlock($blockName) {
        global $dispatcher;
        global $site;
        $data = array (
            'blockName' => $blockName
        );

        $event = new \Ip\Event($site, 'site.generateBlock', $data);

        $processed = $dispatcher->notifyUntil($event);

        if ($processed && $event->issetValue('content')) {
            return $event->getValue('content');
        } else {
            require_once(BASE_DIR.MODULE_DIR.'standard/content_management/model.php');
            $revision = $this->getRevision();
             
            if ($revision != false) {
                return \Modules\standard\content_management\Model::generateBlock($blockName, $revision['revisionId'], $this->managementState());
            } else {
                return '';
            }
        }


    }

    /**
     * If we are in the management state and last revision is published, then create new revision.
     *
     */
    public function getRevision() {
        //todo cache revision
        $revision = null;
        if ($this->managementState()){
            if (isset($this->getVars['cms_revision'])) {
                $revisionId = $this->getVars['cms_revision'];
                $revision = \Ip\Revision::getRevision($revisionId);
            }
             
            if ($revision === false || $revision['zoneName'] != $this->getCurrentZone()->getName() || $revision['pageId'] != $this->getCurrentElement()->getId() ) {
                if (!$this->getCurrentElement()) {
                    return null;
                }
                $revision = \Ip\Revision::getLastRevision($this->getCurrentZone()->getName(), $this->getCurrentElement()->getId());
                if ($revision['published']) {
                    $revision = $this->_duplicateRevision($revision['revisionId']);
                }
            }

        } else {
            require_once(BASE_DIR.MODULE_DIR.'standard/content_management/model.php');
            $currentElement = $this->getCurrentElement();
            if ($currentElement) {
                $revision = \Ip\Revision::getPublishedRevision($this->getCurrentZone()->getName(), $currentElement->getId());
            }
            
        }
        return $revision;
    }


    private function _createRevision(){
        $revisionId = \Ip\Revision::createRevision($this->getCurrentZone()->getName(), $this->getCurrentElement()->getId(),0);
        $revision = \Ip\Revision::getRevision($revisionId);
        if ($revision === false) {
            throw new \Ip\CoreException("Can't find created revision " . $revisionId, \Ip\CoreException::REVISION);
        }
        return $revision;
    }
    
    private function _duplicateRevision($oldRevisionId){
        $revisionId = \Ip\Revision::duplicateRevision($oldRevisionId);
        $revision = \Ip\Revision::getRevision($revisionId);
        if ($revision === false) {
            throw new \Ip\CoreException("Can't find created revision " . $revisionId, \Ip\CoreException::REVISION);
        }
        return $revision;
    }    
}


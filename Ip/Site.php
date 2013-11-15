<?php
/*
 * ImpressPages CMS main frontend file
 *
 * @package ImpressPages
 *
 *
 */



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


    /** bool true if page does not exists */
    private $error404;
    



    /** @deprecated use getCurrentZone()->getName() instead */
    public $currentZone;

    /** @deprecated use getLanguages() instead */
    public $languages;

    /** @deprecated use getCurrentLanguage() instead */
    public $currentLanguage;


    /** string HTML or any other output. If is not null, it will be send to the output. If it is null, required page by request URL will be generated  */
    protected $output;

    /** int Revision of current page.  */
    protected $revision;


    public function __construct(){


    }






    /**
     *
     * @return \Ip\Frontend\Language
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
        \Ip\ServiceLocator::getContent()->getCurrentLanguage();
    }




    protected function homeZone()
    {
        $zones = \Ip\Frontend\Db::getZones($this->currentLanguage['id']);
        foreach ($zones as $key => $zoneInfo) {
            if ($zoneInfo['url'] == '') {
                $zone = $this->getZone($zoneInfo['name']);

                // if first url element is not in home zone, we are not in home zone
                if (!$zone->findElement(array($this->zoneUrl), array())) {
                    return;
                }

                $this->currentZone = $zoneInfo['name'];
                array_unshift($this->urlVars, urlencode($this->zoneUrl));
                $this->zoneUrl = '';
                break;
            }
        }
    }





    /**
     * Find website zone by module group and name.
     * @param $group Module group name (string)
     * @param $module Module name (string)
     * @return \Ip\Frontend\Zone
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



    /*
     * Redirect to another page if required
     * @return null
     *
     */
    public function makeRedirect(){
        $curEl =  $this->getCurrentPage();
        if($curEl){ //if page exist.
            switch($curEl->getType()){
                case 'subpage':
                case 'redirect':
                    $currentUrl = \Ip\Internal\UrlHelper::getCurrentUrl();
                    if(isset($_SESSION['frontend']['redirects'][$currentUrl])){
                        unset($_SESSION['frontend']['redirects']);
                        return;//infinite redirect loop. Stop redirecting;
                    } else {
                        if (!isset($_GET['cms_action']) || $_GET['cms_action'] != 'manage_content') {
                            $_SESSION['frontend']['redirects'][$currentUrl] = 1; //to detect infinite loop
                            header('HTTP/1.1 301 Moved Permanently');
                            header('Location: '.$curEl->getLink());

                            \Ip\Internal\Deprecated\Db::disconnect();
                            exit();
                        }
                    }
                    break;
            }
        }
        unset($_SESSION['frontend']['redirects']);
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
     * @param $escape
     *   Escape & with &amp;
     * @return string - requested link or link to first page of current language if all parameters are not specified or null
     */
    public function generateUrl($languageId=null, $zoneName = null, $urlVars = null, $getVars = null, $escape = true){

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

        if(ipGetOption('Config.multilingual')){
            $answer = \Ip\Config::baseUrl(urlencode($this->languages[$languageId]['url']).'/');
        }else{
            $answer = \Ip\Config::baseUrl('');
        }

        if($zoneName != null){
            if($languageId == $this->currentLanguage['id']){ //current language
                if(isset($this->zones[$zoneName])){
                    if ($this->zones[$zoneName]['url']) {
                        $answer .= urlencode($this->zones[$zoneName]['url']).'/';
                    }
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
                $this->otherZones[$languageId] = \Ip\Frontend\Db::getZones($languageId);

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
            foreach($urlVars as $value) {
                $answer .= urlencode($value).'/';
            }
        }


        if ($escape) {
            $amp = '&amp;';
        } else {
            $amp = '&';
        }

        if($getVars && sizeof($getVars) > 0){
            $answer .= '?'.http_build_query($getVars, '', $amp);
        }

        return $answer;
    }



    /**
     *
     * @return string title of current page
     *
     */
    public function getTitle(){
        $curZone = ipGetCurrentZone();
        if (!$curZone) {
            return '';
        }
        $curEl =  $curZone->getCurrentPage();
        if($curEl && $curEl->getPageTitle() != '') {
            return $curEl->getPageTitle();
        } else {
            return $curZone->getTitle();
        }
    }

    /**
     *
     * @return string description of current page
     *
     */
    public function getDescription(){
        $curZone = ipGetCurrentZone();
        if (!$curZone) {
            return '';
        }
        $curEl =  $curZone->getCurrentPage();
        if($curEl && $curEl->getDescription() != '') {
            return $curEl->getDescription();
        } else {
            return $curZone->getDescription();
        }
    }



    /**
     *
     * @return string keywords of current page
     *
     */
    public function getKeywords(){
        $curZone = ipGetCurrentZone();
        if (!$curZone) {
            return '';
        }
        
        $curEl = $curZone->getCurrentPage();
        if($curEl && $curEl->getKeywords() != '') {
            return $curEl->getKeywords();
        } else {
            return $curZone->getKeywords();
        }
    }

    /**
     *
     * @return bool true if the system is in management state
     *
     */
    public function managementState(){
        $backendLoggedIn = isset($_SESSION['backend_session']['userId']) && $_SESSION['backend_session']['userId'] != null;
        return ($backendLoggedIn && \Ip\ServiceLocator::getRequest()->getQuery('cms_action', 0) == 'manage');
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
        $systemDirs[\Ip\Config::getRaw('PLUGIN_DIR')] = 1;
        $systemDirs[\Ip\Config::getRaw('THEME_DIR')] = 1;
        $systemDirs[\Ip\Config::getRaw('LIBRARY_DIR')] = 1;
        $systemDirs[\Ip\Config::getRaw('FILE_DIR')] = 1;
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
     * @return \Ip\Frontend\Element - Current page
     *
     */
    public function getCurrentPage(){
        $zone = ipGetCurrentZone();
        if ($zone) {
            return $zone->getCurrentPage();
        }
    }




    public function setOutput ($output) {
        if ($output === null) {
            $output = '';
        }
        $this->output = $output;
    }

    public function getOutput () {
        return $this->output;
    }


    public function generateOutput() {

        if (!isset($this->output)) {
            if (\Ip\Module\Admin\Model::isSafeMode()) {
                //TODOX skip this for admin pages with admin layout
                return \Ip\View::create(\Ip\Config::coreModuleFile('Admin/View/safeModeLayout.php'), array())->render();
            }


            $layout = $this->getLayout();
            if ($layout) {
                if ($layout[0] == '/') {
                    $viewFile = $layout;
                } else {
                    $viewFile = \Ip\Config::themeFile($layout);
                }
                $this->output = \Ip\View::create($viewFile, array())->render();

            } else {
                // DEPRECATED just for backward compatibility
                $site = \Ip\ServiceLocator::getSite();
                $this->output = ipBlock('main')->render();
            }
        }

        return $this->output;
    }



    /**
     * If we are in the management state and last revision is published, then create new revision.
     *
     */
    public function getRevision() {
        //TODOX cache revision
        $revision = null;
        if ($this->managementState()){
            if (isset($this->getVars['cms_revision'])) {
                $revisionId = $this->getVars['cms_revision'];
                $revision = \Ip\Revision::getRevision($revisionId);
            }

            if ($revision === false || $revision['zoneName'] != ipGetCurrentZone()->getName() || $revision['pageId'] != $this->getCurrentPage()->getId() ) {
                if (!$this->getCurrentPage()) {
                    return null;
                }
                $revision = \Ip\Revision::getLastRevision(ipGetCurrentZone()->getName(), $this->getCurrentPage()->getId());
                if ($revision['published']) {
                    $revision = $this->_duplicateRevision($revision['revisionId']);
                }
            }

        } else {
            $currentElement = $this->getCurrentPage();
            if ($currentElement) {
                $revision = \Ip\Revision::getPublishedRevision(ipGetCurrentZone()->getName(), $currentElement->getId());
            }

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


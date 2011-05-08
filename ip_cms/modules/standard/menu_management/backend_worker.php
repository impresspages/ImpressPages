<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
namespace Modules\standard\menu_management;

if (!defined('BACKEND')) exit;



require_once(__DIR__.'/model.php');
require_once(__DIR__.'/db.php');
require_once(__DIR__.'/template.php');


class BackendWorker {


    function __construct() {

    }


    function work() {
        global $parametersMod;
        global $site;

        if (!isset($_REQUEST['action']) ) {
            return;
        }

        switch ($_REQUEST['action']) {
            case 'getChildren' :
                $this->_getChildren ();
                break;
            case 'getUpdatePageForm' :
                $this->_getPageForm();
                break;
            case 'getPageLink' :
                $this->_getPageLink();
                break;
            case 'updatePage' :
                $this->_updatePage();
                break;
            case 'getCreatePageForm' :
                $this->_getCreatePageForm();
                break;
            case 'createPage' :
                $this->_createPage();
                break;
            case 'deletePage' :
                $this->_deletePage();
                break;
            case 'movePage' :
                $this->_movePage();
                break;
            case 'copyPage' :
                $this->_copyPage();
                break;
        }

    }



    private function _getChildren () {
        $parentType = isset($_REQUEST['type']) ? $_REQUEST['type'] : null;
        $parentWebsiteURL = isset($_REQUEST['websiteId']) ? $_REQUEST['websiteId'] : null;
        $parentLanguageId = isset($_REQUEST['languageId']) ? $_REQUEST['languageId'] : null;
        $parentZoneName = isset($_REQUEST['zoneName']) ? $_REQUEST['zoneName'] : null;
        $parentId = isset($_REQUEST['pageId']) ? $_REQUEST['pageId'] : null;

        $list = $this->_getList ($parentType, $parentWebsiteURL, $parentLanguageId, $parentZoneName, $parentId);

        $this->_printJson ($list);
    }

    private function _getList ($parentType, $parentWebsiteId, $parentLanguageId, $parentZoneName, $parentId) {
        global $site;

        $site->requireConfig('standard/menu_management/remotes.php');

        $remotes = Remotes::getRemotes();

        $answer = array();

        switch ($parentType) {
            case '' : //return websites
                $websites = array();
                $websites[0] = BASE_URL;
                //$websites[1] = remote website1
                //$websites[2] = remote website2



                if ($parentId == null || $parentId == '') {
                    $answer[] = array(
            				'attr' => array('id' => 'page_0', 'rel' => 'website', 'websiteId' => 0, 'pageId' => 0),
            				'data' => BASE_URL,
            				'state' => 'open',
                  	'children' => $this->_getList($parentType = 'website', $parentWebsiteId = 0, $parentLanguageId = null, $parentZoneName = null, $parentId = 0)
                    );

                    foreach($remotes as $key => $remote) {
                        $answer[] = array(
              				'attr' => array('id' => 'page_' . ($key + 1), 'rel' => 'website', 'websiteId' => $key + 1, 'pageId' => $key + 1),
              				'data' => $remote['url'],
              				'state' => 'closed'
                		);
                    }

                }

                break;
            case 'website' : //parent node is website
                if ($parentId == 0) {
                    $languages = Db::getLanguages();
                } else {
                    if (isset($remotes[$parentId-1])) {
                        $remote = $remotes[$parentId-1];
                        $languages = $this->_remoteRequest($remote, 'getLanguages');
                    }
                }

                if (is_array($languages)) {
                    foreach ($languages as $languageKey => $language) {
                        $answer[] = array(
              				'attr' => array('id' => 'page_'.$parentWebsiteId.'_'.$language['id'], 'rel' => 'language', 'websiteId' => $parentWebsiteId, 'languageId' => $language['id'], 'pageId' => $language['id']),
              				'data' => $language['d_short'] . '', //transform null into empty string. Null break JStree into infinite loop 
              				'state' => 'closed'
                		);
                    }
                }


                break;
            case 'language' : //parent node is language


                if ($parentWebsiteId == 0) {
                    $zones = Db::getZones();
                } else {
                    $remote = $remotes[$parentWebsiteId-1];
                    $zones = $this->_remoteRequest($remote, 'getZones');
                }
                

                foreach ($zones as $zoneKey => $zone) {
                    $zoneElement = Db::rootContentElement($zone['id'], $parentId);

                    if($zoneElement == null) { /*try to create*/
                        Db::createRootZoneElement($zone['id'], $parentId);
                        $zoneElement = Db::rootContentElement($zone['id'], $parentId);
                        if($zoneElement == null) {	/*fail to create*/
                            trigger_error("Can't create root zone element.");
                            return false;
                        }
                    }


                    $answer[] = array (
    				'attr' => array('id' => 'page_'.$parentWebsiteId.'_'.$parentLanguageId.'_'.$zone['name'].'_'.$zoneElement['id'], 'rel' => 'zone', 'websiteId' => $parentWebsiteId, 'languageId' => $parentLanguageId, 'zoneName' => $zone['name'], 'pageId' => $zoneElement['id']),
    				'data' => $zone['title'] . '', //transform null into empty string. Null break JStree into infinite loop 
    				'state' => 'closed'
    				);
                }

                break;
            case 'zone' : //parent node is zone
            case 'page' : //parent node is page

                
                if ($parentWebsiteId == 0) {
                    $children = Db::pageChildren($parentId);
                } else {
                    $remote = $remotes[$parentWebsiteId-1];
                    $data = array (
                        'parentId' => $parentId
                    );
                    $children = $this->_remoteRequest($remote, 'getChildren', $data);
                }                
                
                
                foreach($children as $childKey => $child) {

                    if ($child['visible'])
                    $disabled = 0;
                    else
                    $disabled = 1;

                    $answer[] = array (
    				'attr' => array('id' => 'page_'.$parentWebsiteId.'_'.$parentLanguageId.'_'.$parentZoneName.'_'.$child['id'], 'rel' => 'page', 'disabled' => $disabled, 'websiteId' => $parentWebsiteId, 'languageId' => $parentLanguageId, 'zoneName' => $parentZoneName, 'pageId' => $child['id']),
    				'data' => $child['button_title'] . '', //transform null into empty string. Null break JStree into infinite loop 
    				'state' => 'closed'
    				);

                }
                break;
            default :
                trigger_error('Unknown type '.$parentType);
                return false;
                break;
        }

        return $answer;
    }



    private function _getPageForm() {
        global $site;
        global $parametersMod;

        if (!isset($_REQUEST['pageId'])) {
            trigger_error("Page id is not set");
            return;
        }

        $pageId = $_REQUEST['pageId'];

        if (!isset($_REQUEST['zoneName'])) {
            trigger_error("Zone name is not set");
            return false;
        }

        $zone = $site->getZone($_REQUEST['zoneName']);

        if (!($zone)) {
            trigger_error("Can't find zone");
            return false;
        }

        $page = $zone->getElement($pageId);

        if (! $page) {
            trigger_error ("Page does not exist");
            return false;
        }

        $tabs = array();


        $title = $parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'general');
        $content = Template::generateTabGeneral();
        $tabs[] = array('title' => $title, 'content' => $content);

        $title = $parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'seo');
        $content = Template::generateTabSEO();
        $tabs[] = array('title' => $title, 'content' => $content);

        $title = $parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'advanced');
        $content = Template::generateTabAdvanced();
        $tabs[] = array('title' => $title, 'content' => $content);


        $answer = array();
        $answer['page'] = array();


        $answer['page']['pageId'] = $page->getId();
        $answer['page']['zoneName'] = $page->getZoneName();
        $answer['page']['buttonTitle'] = $page->getButtonTitle() . '';
        $answer['page']['visible'] = $page->getVisible();
        $answer['page']['createdOn'] = $page->getCreatedOn();
        $answer['page']['lastModified'] = $page->getLastModified();
         
        $answer['page']['pageTitle'] = $page->getPageTitle() . '';
        $answer['page']['keywords'] = $page->getKeywords() . '';
        $answer['page']['description'] = $page->getDescription() . '';
        $answer['page']['url'] = $page->getUrl() . '';
         
        $answer['page']['type'] = $page->getType();
        $answer['page']['redirectURL'] = $page->getRedirectUrl() . '';

        $answer['html'] = Template::generatePageProperties($tabs);

        $this->_printJson ($answer);
    }


    private function _getCreatePageForm() {
        global $site;
        global $parametersMod;

        if (!isset($_REQUEST['pageId'])) {
            trigger_error("Page ID is not set");
            return;
        }

        $parentPageId = $_REQUEST['pageId'];

        if (!isset($_REQUEST['zoneName'])) {
            trigger_error("Zone name is not set");
            return false;
        }

        $zoneName = $_REQUEST['zoneName'];
        $zone = $site->getZone($_REQUEST['zoneName']);

        if (! $zone) {
            trigger_error("Can't find zone");
            return false;
        }

        $parentPage = $zone->getElement($parentPageId);

        if (! $parentPage) {
            trigger_error ("Page does not exist");
            return false;
        }

        $tabs = array();

        $page = new \Frontend\Element('', $zoneName);

        if($parametersMod->getValue('standard', 'menu_management', 'options', 'hide_new_pages')) {
            $page->setVisible(!$parametersMod->getValue('standard', 'menu_management', 'options', 'hide_new_pages'));
        }

        $title = $parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'general');
        $content = Template::generateTabGeneral($page, $parentPage);
        $tabs[] = array('title' => $title, 'content' => $content);

        $title = $parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'seo');
        $content = Template::generateTabSEO($page, $parentPage);
        $tabs[] = array('title' => $title, 'content' => $content);

        $title = $parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'advanced');
        $content = Template::generateTabAdvanced($page, $parentPage);
        $tabs[] = array('title' => $title, 'content' => $content);


        $answer = array();

        $page = new \Frontend\Element('', $zoneName);
        $page->setCreatedOn(date('Y-m-d'));
        $page->setLastModified(date('Y-m-d'));
        $page->setType('default');

        $answer['parent']['pageId'] = $parentPageId;
        $answer['page']['pageId'] = $page->getId();
        $answer['page']['zoneName'] = $page->getZoneName();
        $answer['page']['buttonTitle'] = $page->getButtonTitle();
        $answer['page']['visible'] = $page->getVisible();
        $answer['page']['createdOn'] = $page->getCreatedOn();
        $answer['page']['lastModified'] = $page->getLastModified();
         
        $answer['page']['pageTitle'] = $page->getPageTitle();
        $answer['page']['keywords'] = $page->getKeywords();
        $answer['page']['description'] = $page->getDescription();
        $answer['page']['url'] = $page->getUrl();
         
        $answer['page']['type'] = $page->getType();
        $answer['page']['redirectURL'] = $page->getRedirectUrl();

        $answer['html'] = Template::generatePageProperties($tabs);

        $this->_printJson ($answer);
    }


    private function _getPageLink() {
        global $site;
        $answer = array();

        if (!isset($_REQUEST['websiteId'])) {
            trigger_error("Website Id is not set");
            return false;
        }
        $websiteId = $_REQUEST['websiteId'];


        if (!isset($_REQUEST['type'])) {
            trigger_error("Page type is not set");
            return false;
        }

        $type = $_REQUEST['type'];

        switch ($type) {
            case 'website':
                $answer['link'] = $websiteId;
                break;
            case 'language':
                if (!isset($_REQUEST['languageId'])) {
                    trigger_error("Language Id is not set");
                    return false;
                }
                $answer['link'] = $site->generateUrl($_REQUEST['languageId']);
                break;

            case 'zone':
                if (!isset($_REQUEST['languageId'])) {
                    trigger_error("Language Id is not set");
                    return false;
                }
                if (!isset($_REQUEST['zoneName'])) {
                    trigger_error("Zone name is not set");
                    return false;
                }

                $answer['link'] = $site->generateUrl($_REQUEST['languageId'], $_REQUEST['zoneName']);

                break;
            case 'page':
                if (!isset($_REQUEST['languageId'])) {
                    trigger_error("Language Id is not set");
                    return false;
                }
                if (!isset($_REQUEST['zoneName'])) {
                    trigger_error("Zone name is not set");
                    return false;
                }
                if (!isset($_REQUEST['pageId'])) {
                    trigger_error("Page Id is not set");
                    return false;
                }

                $pageId = $_REQUEST['pageId'];
                $zone = $site->getZone($_REQUEST['zoneName']);

                if (! $zone) {
                    trigger_error("Ca'nt find zone");
                    return false;
                }

                $page = $zone->getElement($pageId);

                if (! $page) {
                    trigger_error("Can't find page");
                    return false;
                }

                $answer['link'] = $page->getLink();


                break;


            default:
                trigger_error('Undefined page type');
                return false;
        }

        $this->_printJson ($answer);
    }


    private function _updatePage () {
        global $parametersMod;
        global $site;

        $answer = array();


        if (!isset($_REQUEST['pageId'])) {
            trigger_error("Page id is not set");
            return false;
        }
        $pageId = $_REQUEST['pageId'];

        //make url
        if ($_POST['url'] == '') {
            if ($_POST['pageTitle'] != '') {
                $_POST['url'] = Db::makeUrl($_POST['pageTitle'], $pageId);
            } else {
                if ($_POST['buttonTitle'] != '') {
                    $_POST['url'] = Db::makeUrl($_POST['buttonTitle'], $pageId);
                }
            }
        } else {
            $tmpUrl = str_replace("/", "-", $_POST['url']);
            $i = 1;
            while (!Db::availableUrl($tmpUrl, $_POST['pageId'])) {
                $tmpUrl = $_POST['url'].'-'.$i;
                $i++;
            }
            $_POST['url'] = $tmpUrl;
        }
        //end make url

        if (strtotime($_POST['createdOn']) === false) {
            $answer['errors'][] = array('field' => 'createdOn', 'message' => $parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'error_date_format').date("Y-m-d"));
        }

        if (strtotime($_POST['lastModified']) === false) {
            $answer['errors'][] = array('field' => 'lastModified', 'message' => $parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'error_date_format').date("Y-m-d"));
        }

        if ($_POST['type'] == 'redirect' && $_POST['redirectURL'] == '') {
            $answer['errors'][] = array('field' => 'redirectURL', 'message' => $parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'error_type_url_empty'));
        }


        if (empty($answer['errors'])) {
            $zone = $site->getZone($_POST['zoneName']);
            $oldPage = $zone->getElement($_POST['pageId']);

            Db::updatePage($_POST['pageId'], $_POST);

            if($oldPage->getUrl() != $_POST['url']){
                $newElement = $zone->getElement($_POST['pageId']);
                $site->dispatchEvent('administrator', 'system', 'url_change', array('old_url'=>$oldPage->getLink(true), 'new_url'=>$newPage->getLink(true)));
            }

        }

        $answer['status'] = 'success';

        $this->_printJson ($answer);
    }



    private function _createPage () {
        global $parametersMod;
        global $site;

        $answer = array();


        //make url
        if ($_POST['url'] == '') {
            if ($_POST['pageTitle'] != '') {
                $_POST['url'] = Db::makeUrl($_POST['pageTitle']);
            } else {
                $_POST['url'] = Db::makeUrl($_POST['buttonTitle']);
            }
        } else {
            $tmpUrl = str_replace("/", "-", $_POST['url']);
            $i = 1;
            while (!Db::availableUrl($tmpUrl)) {
                $tmpUrl = $_POST['url'].'-'.$i;
                $i++;
            }
            $_POST['url'] = $tmpUrl;
        }
        //end make url


        if (strtotime($_POST['createdOn']) === false) {
            $answer['errors'][] = array('field' => 'createdOn', 'message' => $parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'error_date_format').date("Y-m-d"));
        }

        if (strtotime($_POST['lastModified']) === false) {
            $answer['errors'][] = array('field' => 'lastModified', 'message' => $parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'error_date_format').date("Y-m-d"));
        }

        if ($_POST['type'] == 'redirect' && $_POST['redirectURL'] == '') {
            $answer['errors'][] = array('field' => 'redirectURL', 'message' => $parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'error_type_url_empty'));
        }

        if (empty($answer['errors'])) {

            $_POST['createdOn'] = date("Y-m-d", strtotime($_POST['createdOn']));
            $_POST['lastModified'] = date("Y-m-d", strtotime($_POST['lastModified']));

            $visible = $_POST['visible'];

            $newPageId = Db::insertPage($_POST['parentId'], $_POST);

            $answer['status'] = 'success';
            $answer['page']['id'] = $newPageId;
        }

        $this->_printJson ($answer);
    }


    private function _deletePage () {
        if (!isset($_REQUEST['pageId'])) {
            trigger_error("Page id is not set");
            return false;
        }
        $pageId = $_REQUEST['pageId'];

        Model::deletePage($pageId);

        $answer = array ();
        $answer['status'] = 'success';

        $this->_printJson($answer);
    }

    private function _movePage () {
        global $site;

        if (!isset($_REQUEST['pageId'])) {
            trigger_error("Page id is not set");
            return false;
        }
        $pageId = $_REQUEST['pageId'];

        if (!isset($_REQUEST['zoneName'])) {
            trigger_error("Zone name is not set");
            return false;
        }
        $zoneName = $_REQUEST['zoneName'];

        if (!isset($_REQUEST['languageId'])) {
            trigger_error("Language id is not set");
            return false;
        }
        $languageId = $_REQUEST['languageId'];

        if (!isset($_REQUEST['websiteId'])) {
            trigger_error("Website Id is not set");
            return false;
        }
        $websiteId = $_REQUEST['websiteId'];

        if (!isset($_REQUEST['destinationPageId'])) {
            trigger_error("Destination page ID is not set");
            return false;
        }
        $destinationPageId = $_REQUEST['destinationPageId'];

        if (!isset($_REQUEST['destinationPosition'])) {
            trigger_error("Destination position is not set");
            return false;
        }
        $destinationPosition = $_REQUEST['destinationPosition'];



        //report url cange
        $pageZone = $site->getZone($zoneName);
        $page = $pageZone->getElement($pageId);
        $oldUrl = $page->getLink(true);
        //report url change

        $newParentChildren = Db::pageChildren($destinationPageId);

        $newIndex = 0; //initial value

        if(count($newParentChildren) > 0) { //set as first page
            $newIndex = $newParentChildren[0]['row_number'] - 1;

            if ($destinationPosition > 0) {
                if (isset($newParentChildren[$destinationPosition - 1]) && isset($newParentChildren[$destinationPosition])) { //new position is in the middle of other pages
                    $newIndex = ($newParentChildren[$destinationPosition - 1]['row_number'] + $newParentChildren[$destinationPosition]['row_number']) / 2; //average
                } else { //new position is at the end
                    $newIndex = $newParentChildren[count($newParentChildren) - 1]['row_number'] + 1;
                }
            }
        }

        $data = array (
            'parentId' => $destinationPageId,
            'rowNumber' => $newIndex
        );
        Db::updatePage($pageId, $data);

        //report url change
        $pageZone = $site->getZone($zoneName);
        $page = $pageZone->getElement($pageId);
        $newUrl = $page->getLink(true);
        $site->dispatchEvent('administrator', 'system', 'url_change', array('old_url'=>$oldUrl, 'new_url'=>$newUrl));
        //report url change


        $answer = array();
        $answer['status'] = 'success';

        $this->_printJson($answer);

    }

    /**
     * 
     * Copy page from one place to another
     */
    private function _copyPage() {
        global $site;
        $site->requireConfig('standard/menu_management/remotes.php');
        $answer = array();
        
        if (!isset($_REQUEST['websiteId'])) {
            trigger_error("Website id is not set");
            return false;
        }
        $websiteId = $_REQUEST['websiteId'];
                
        if (!isset($_REQUEST['pageId'])) {
            trigger_error("Page id is not set");
            return false;
        }
        $pageId = $_REQUEST['pageId'];

        if (!isset($_REQUEST['destinationPageId'])) {
            trigger_error("Destination page id is not set");
            return false;
        }
        $destinationPageId = $_REQUEST['destinationPageId'];

        
        if ($websiteId == 0) { //local page
            $children = Db::pageChildren($destinationPageId);
            $destinationPosition = count($children); //paste at the bottom
            Model::copyPage($pageId, $destinationPageId, $destinationPosition);            
        } else { //remote page
            $remotes = Remotes::getRemotes();
            $remote = $remotes[$websiteId - 1];            
            $data = array (
                'pageId' => $pageId
            );
            $remotePages = $this->_remoteRequest($remote, 'getData', $data);
            $this->_createPagesRecursion($destinationPageId, $remotePages);
            

            $contentManagementSystem = new \Modules\standard\content_management\System();
            $contentManagementSystem->clearCache(BASE_URL);            
                
            $answer['data'] = $data;
        }

        $answer['status'] = 'success';
        $answer['destinationPageId'] = $destinationPageId;

        $this->_printJson($answer);
    }

    
    /**
     * 
     * Array of pages and subpages
     * @param array $pages
     */
    private function _createPagesRecursion ($targetPageId, $pages) {
        foreach ($pages as $pageKey => $page) {
            
            Db::insertPage($targetPageId, $page);
            foreach ($page['widgets'] as $widgetKey => $widget) {
               Model::addWidget($targetId = $page['id'], $widget['data'], $widget); 
            }
            
            if (! empty($page['subpages'])) {
                self::_createPagesRecursion($page['id'], $page['subpages']);
            }
        }
    }


    /**
     *
     * Ask for data on the remote server
     * @param array $remote array(url, username, password)
     * @param string $action
     * @param array $data
     */
    private function _remoteRequest ($remote, $action, $data = array()) {
        if (!function_exists('curl_init')){
            trigger_error('CURL is not installed');
            return;
        }


        $data['action'] = $action;
        $data['version1'] = true; //supported API version number 1
        $data['module_group'] = 'standard';
        $data['module_name'] = 'menu_management';
        $data['username'] = $remote['username'];
        $data['password'] = $remote['password'];

        $dataString = '';
        foreach ($data as $key=>$value) {
            $dataString .= $key.'='.$value.'&';
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, count($data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_URL, $remote['url']);
        curl_setopt($ch, CURLOPT_REFERER, BASE_URL);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla4/1.0");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        curl_close($ch);

        $responseData = json_decode($response, true);
        if ($responseData === null || empty ($responseData['status']) || $responseData['status'] != 'success' || empty ($responseData['response'])) {
            trigger_error('Incorrect response from the server '.$response);
            return;
        }

        return $responseData['response'];
    }


    private function _printJson ($data) {
        header("HTTP/1.0 200 OK");
        header('Content-type: text/json; charset=utf-8');
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Pragma: no-cache");
        echo json_encode($data);

    }







}
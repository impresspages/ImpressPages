<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Modules\standard\menu_management;

if (!defined('BACKEND')) exit;



require_once(__DIR__.'/model.php');
require_once(__DIR__.'/model_tree.php');
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
            case 'closePage' :
                $this->_closePage();
                break;


        }

    }


    /**
     *
     * Get children of selected jsTree node
     */
    private function _getChildren () {
        $parentType = isset($_REQUEST['type']) ? $_REQUEST['type'] : null;
        $parentWebsiteId = isset($_REQUEST['websiteId']) ? $_REQUEST['websiteId'] : null;
        $parentLanguageId = isset($_REQUEST['languageId']) ? $_REQUEST['languageId'] : null;
        $parentZoneName = isset($_REQUEST['zoneName']) ? $_REQUEST['zoneName'] : null;
        $parentId = isset($_REQUEST['pageId']) ? $_REQUEST['pageId'] : null;

        if (!isset($_REQUEST['externalLinking'])) {
            trigger_error("Popup status is not set");
            return;
        }
        $externalLinking = $_REQUEST['externalLinking'];

        $list = $this->_getList ($externalLinking, $parentType, $parentWebsiteId, $parentLanguageId, $parentZoneName, $parentId);


        $this->_printJson ($list);
    }
    /**
     *
     * Return array of children
     * @param bool $externalLinking true if this command is executed on external linking popup. That means we need to retun all available zones, not only content management.
     * @param string $parentType
     * @param mixed $parentWebsiteId
     * @param int $parentLanguageId
     * @param string $parentZoneName
     * @param mixed $parentId
     */
    private function _getList ($externalLinking, $parentType, $parentWebsiteId, $parentLanguageId, $parentZoneName, $parentId) {
        global $site;
        global $parametersMod;

        $jsTreeId = self::_jsTreeId($parentWebsiteId, $parentLanguageId, $parentZoneName, $parentId);


        //store status only on local menu tree
        if (true || $parentWebsiteId == 0) {
            $_SESSION['modules']['standard']['menu_management']['openNode'][$jsTreeId] = 1;
        }

        $site->requireConfig('standard/menu_management/remotes.php');

        $remotes = Remotes::getRemotes();

        $answer = array();

        switch ($parentType) {
            case '' : //return websites
                $items = ModelTree::getWebsites();

                $answer = array();

                foreach ($items as $itemKey => $item) {

                    $state = 'closed';
                    $children = false;

                    if ($itemKey == 0) {
                        $state = 'open';
                        $children = $this->_getList($externalLinking, 'website', $item['id'], null, null, $item['id']);
                    }

                    $answer[] = array (
                        'attr' => array('id' => $this->_jsTreeId($item['id']), 'rel' => 'website', 'websiteId' => $item['id'], 'pageId' => $item['id']),
                        'data' => $item['title'],
                        'state' => $state,
                        'children' => $children
                    );
                }

                break;
            case 'website' : //parent node is website

                if ($parentId == 0) { //if this local website
                    $items = ModelTree::getLanguages();
                } else { //if remote website
                    if (isset($remotes[$parentWebsiteId-1])) { //if requested remote is within remotes configuration list
                        $remote = $remotes[$parentWebsiteId-1];
                        $items = $this->_remoteRequest($remote, 'getLanguages');
                    }
                }

                 
                //generate jsTree response array
                foreach ($items as $itemsKey => $item) {

                    $state = 'closed';
                    $children = false;

                    $jsTreeId = $this->_jsTreeId($parentWebsiteId, $item['id'], $parentZoneName, $parentId);

                    //if node status is open
                    if ( !empty($_SESSION['modules']['standard']['menu_management']['openNode'][$jsTreeId])) {
                        $state = 'open';
                        $children = $this->_getList($externalLinking, 'language', $parentWebsiteId, $item['id'], null, $item['id']);
                        if (count($children) == 0) {
                            $children = false;
                            $state = 'leaf';
                        }
                    }


                    $answer[] = array (
                        'attr' => array('id' => $jsTreeId, 'rel' => 'language', 'websiteId' => $parentWebsiteId, 'languageId' => $item['id'], 'pageId' => $item['id']),
                        'data' => $item['title'] . '', //transform null into empty string. Null break JStree into infinite loop 
                        'state' => $state,  		    
                        'children' => $children
                    );
                }


                break;
            case 'language' : //parent node is language


                if ($parentWebsiteId == 0) {
                    $items = ModelTree::getZones($externalLinking);
                } else {
                    if (isset($remotes[$parentWebsiteId-1])) {
                        $remote = $remotes[$parentWebsiteId-1];
                        $data = array (
                            'includeNonManagedZones' => $externalLinking
                        );
                        $items = $this->_remoteRequest($remote, 'getZones', $data);
                    } else {
                        trigger_error('Can\'t find required remote website. ' . $parentWebsiteId);
                        return;
                    }
                }


                //generate jsTree response array
                foreach ($items as $itemKey => $item) {

                    $state = 'closed';
                    $children = false;

                    $jsTreeId = $this->_jsTreeId($parentWebsiteId, $parentLanguageId, $item['id'], $item['id']);
                    //if node status is open
                    if (!empty($_SESSION['modules']['standard']['menu_management']['openNode'][$jsTreeId])) {
                        $state = 'open';
                        $children = $this->_getList($externalLinking, 'zone', $parentWebsiteId, $parentLanguageId, $item['id'], $item['id']);
                        if (count($children) == 0) {
                            $children = false;
                            $state = 'leaf';
                        }
                    }


                    $answer[] = array (
                        'attr' => array('id' => $jsTreeId, 'rel' => 'zone', 'websiteId' => $parentWebsiteId, 'languageId' => $parentLanguageId, 'zoneName' => $item['id'], 'pageId' => $item['id']),
                        'data' => $item['title'] . '', //transform null into empty string. Null break JStree into infinite loop 
                        'state' => $state,  		    
                        'children' => $children
                    );
                }


                break;
            case 'zone' : //parent node is zone

                if ($parentWebsiteId == 0) {
                    $items = ModelTree::getZonePages($parentLanguageId, $parentZoneName);
                } else {
                    if (isset($remotes[$parentWebsiteId-1])) {
                        $remote = $remotes[$parentWebsiteId-1];
                        $data = array (
                            'languageId' => $parentLanguageId,
                            'zoneName' => $parentZoneName
                        );
                        $items = $this->_remoteRequest($remote, 'getZonePages', $data);
                    }
                }


                //generate jsTree response array
                foreach ($items as $itemKey => $item) {

                    $state = 'closed';
                    $children = false;

                    $jsTreeId = $this->_jsTreeId($parentWebsiteId, $parentLanguageId, $parentZoneName, $item['id']);

                    //if node status is open
                    if (!empty($_SESSION['modules']['standard']['menu_management']['openNode'][$jsTreeId])) {
                        $state = 'open';
                        $children = $this->_getList($externalLinking, 'page', $parentWebsiteId, $parentLanguageId, $parentZoneName, $item['id']);
                        if (count($children) == 0) {
                            $children = false;
                            $state = 'leaf';
                        }
                    }

                    if ($item['visible']) {
                        $icon = '';
                    } else {
                        $icon = BASE_URL.MODULE_DIR.'standard/menu_management/img/file_hidden.png';
                    }
                    

                    $answer[] = array (
                        'attr' => array('id' => $jsTreeId, 'rel' => 'page', 'websiteId' => $parentWebsiteId, 'languageId' => $parentLanguageId, 'zoneName' => $parentZoneName, 'pageId' => $item['id']),
                        'data' => array('title' => $item['title'] . '', 'icon' => $icon), //transform null into empty string. Null break JStree into infinite loop 
                        'state' => $state,
                        'children' => $children
                    );
                }


                break;
            case 'page' : //parent node is page
                if ($parentWebsiteId == 0) {
                    $items = ModelTree::getPages($parentId);
                } else {
                    $remote = $remotes[$parentWebsiteId-1];
                    $data = array (
                        'parentId' => $parentId
                    );
                    $items = $this->_remoteRequest($remote, 'getPages', $data);
                }


                //generate jsTree response array
                foreach ($items as $itemKey => $item) {

                    $state = 'closed';
                    $children = false;

                    $jsTreeId = $this->_jsTreeId($parentWebsiteId, $parentLanguageId, $parentZoneName, $item['id']);

                    if ($item['visible']) {
                        $icon = '';
                    } else {
                        $icon = BASE_URL.MODULE_DIR.'standard/menu_management/img/file_hidden.png';
                    }

                    //if node status is open
                    if (!empty($_SESSION['modules']['standard']['menu_management']['openNode'][$jsTreeId])) {
                        $state = 'open';
                        $children = $this->_getList($externalLinking, 'page', $parentWebsiteId, $parentLanguageId, $parentZoneName, $item['id']);
                        if (count($children) == 0) {
                            $children = false;
                            $state = 'leaf';
                        }
                    }


                    $answer[] = array (
                        'attr' => array('id' => $jsTreeId, 'rel' => 'page', 'websiteId' => $parentWebsiteId, 'languageId' => $parentLanguageId, 'zoneName' => $parentZoneName, 'pageId' => $item['id']),
                        'data' => array ('title' => $item['title'] . '', 'icon' => $icon), //transform null into empty string. Null break JStree into infinite loop 
                        'state' => $state,
                        'children' => $children
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


    /**
     *
     * Get page upadate form HTML
     */
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

        $title = $parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'design');
        $content = $this->_getPageDesignOptionsHtml($zone, $page, array('show_submit_button' => true));
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
        $answer['page']['rss'] = $page->getRss();

        $answer['html'] = Template::generatePageProperties($tabs);

        $this->_printJson ($answer);
    }

    /**
     * @param $zone
     * @param $page
     * @return string content
     */
    private function _getPageDesignOptionsHtml($zone, $page, $data)
    {
        $data['defaultLayout'] = $zone->getLayout();
        $data['layouts'] = \Ip\Module\Content\Model::getThemeLayouts();

        $data['layout'] = \Frontend\Db::getPageLayout(
            $zone->getAssociatedModuleGroup(),
            $zone->getAssociatedModule(),
            $page->getId()
        );

        if (!$data['layout']) {
            $data['layout'] = $data['defaultLayout'];
        }

        return \Ip\View::create('view/page_options_design.php', $data)->render();
    }


    /**
     *
     * Get URL of the page
     */
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
                    trigger_error("Can't find zone");
                    return false;
                }

                $page = $zone->getElement($pageId);

                if (! $page) {
                    trigger_error("Can't find page");
                    return false;
                }

                $answer['link'] = $page->getLink(true);


                break;


            default:
                trigger_error('Undefined page type');
                return false;
        }

        $this->_printJson ($answer);
    }

    /**
     *
     * Update page
     */
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
            Db::updatePage($_POST['zoneName'], $_POST['pageId'], $_POST);
            $answer['status'] = 'success';
        } else {
            $answer['status'] = 'error';
        }


        $this->_printJson ($answer);
    }


    /**
     *
     * Create new page
     */
    private function _createPage () {
        global $parametersMod;
        global $site;

        $answer = array();

        if (!isset($_REQUEST['buttonTitle'])) {
            trigger_error('Button title is not set');
            return;
        }
        $buttonTitle = $_REQUEST['buttonTitle'];


        if (isset($_REQUEST['languageId'])) {
            $language = $site->getLanguageById($_REQUEST['languageId']);
        } else {
            $languages = Db::getLanguages();
            $languageArray = $languages[0];
            $language = $site->getLanguageById($languageArray['id']);
        }

        if (empty($language) || !$language) {
            trigger_error('Can\'t find any language');
            return;
        }


        if (isset($_REQUEST['zoneName'])) {
            $zone = $site->getZone($_REQUEST['zoneName']);
        } else {
            $associatedZones = Db::getZones();
            $zoneArray = array_shift($associatedZones);
            if ($zoneArray) {
                $zone = $site->getZone($zoneArray['name']);
            }
        }

        if (empty($zone) || $zone == false) {
            trigger_error('Can\'t find any zone');
            return;
        }

        if (isset($_REQUEST['pageId'])) {
            $parentPage = $zone->getElement($_REQUEST['pageId']);
        }



        if (empty($parentPage)) {
            $parentPageId = Db::rootContentElement($zone->getId(), $language->getId());

            if($parentPageId === false) {
                trigger_error("Can't find root zone element.");
                return false;
            }

            $parentPage = $zone->getElement($parentPageId);
        }

        if (empty($parentPage)) {
            trigger_error('Can\'t find where to create new page');
            return;
        }

         
        $data = array();

        $data['buttonTitle'] = $buttonTitle;
        $data['pageTitle'] = $buttonTitle;
        $data['url'] = Db::makeUrl($buttonTitle);
        $data['createdOn'] = date("Y-m-d");
        $data['lastModified'] = date("Y-m-d");
        $data['visible'] = !$parametersMod->getValue('standard', 'menu_management', 'options', 'hide_new_pages');

        $autoRssZones = Db::getAutoRssZones();
        $data['rss'] = in_array($zone->getName(), $autoRssZones);
        if($data['rss'] === '') {
            $data['rss'] = 0;
        }



        $newPageId = Db::insertPage($parentPage->getId(), $data);

        $answer['status'] = 'success';

        //find language
        require_once(BASE_DIR.FRONTEND_DIR.'db.php');
        $tmpId = $parentPage->getId();
        $element = \Ip\Module\Content\DbFrontend::getElement($tmpId);
        while($element['parent'] !== null) {
            $tmpUrlVars[] = $element['url'];
            $element = \Ip\Module\Content\DbFrontend::getElement($element['parent']);
        }
        $languageId = \Ip\Module\Content\DbFrontend::languageByRootElement($element['id']);
        //end find language

        $answer['refreshId'] = $this->_jsTreeId(0, $languageId, $parentPage->getZoneName(), $parentPage->getId());
         
        $this->_printJson ($answer);
    }


    /**
     *
     * Delete the page
     */
    private function _deletePage () {
        if (!isset($_REQUEST['pageId'])) {
            trigger_error("Page id is not set");
            return false;
        }
        $pageId = $_REQUEST['pageId'];

        if (!isset($_REQUEST['zoneName'])) {
            trigger_error("zoneName is not set");
            return false;
        }
        $zoneName = $_REQUEST['zoneName'];
        
        Model::deletePage($zoneName, $pageId);

        $answer = array ();
        $answer['status'] = 'success';

        $this->_printJson($answer);
    }

    /**
     *
     * Move page to another location
     */
    private function _movePage () {
        global $site;
        global $log;
        global $dispatcher;


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
        
        if (!isset($_REQUEST['position'])) {
            trigger_error("Position is not set");
            return false;
        }
        $position = $_REQUEST['position'];
        

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


        if (!isset($_REQUEST['destinationZoneName'])) {
            trigger_error("Destination zone name is not set");
            return false;
        }
        $destinationZoneName = $_REQUEST['destinationZoneName'];


        if (!isset($_REQUEST['destinationPageType'])) {
            trigger_error("Destination type is not set");
            return false;
        }
        $destinationPageType = $_REQUEST['destinationPageType'];


        if (!isset($_REQUEST['destinationLanguageId'])) {
            trigger_error("Destination language ID is not set");
            return false;
        }
        $destinationLanguageId = $_REQUEST['destinationLanguageId'];

        //check if destination page exists
        $destinationZone = $site->getZone($destinationZoneName);
        if ($destinationPageType == 'zone') {
            $rootElementId = Db::rootContentElement($destinationZone->getId(), $destinationLanguageId);
            if (!$rootElementId) {
                trigger_error('Can\'t find root zone element.');
                return false;
            }
            $destinationPage = $destinationZone->getElement($rootElementId);
        } else {
            $destinationPage = $destinationZone->getElement($destinationPageId);
        }


        if (!$destinationPage) {
            trigger_error("Destination page does not exist");
            return false;
        }




        if (!isset($_REQUEST['destinationPosition'])) {
            trigger_error("Destination position is not set");
            return false;
        }
        $destinationPosition = $_REQUEST['destinationPosition'];



        //report url cange
        $page = $destinationZone->getElement($pageId);
        $oldUrl = $page->getLink(true);
        //report url change
        
        $movePageValues = array(
            'pageId' => $pageId,
                 
        );
        
        $this->_notifyPageMove($pageId, $languageId, $zoneName, $page->getParentId(), $position, $destinationLanguageId, $destinationZoneName, $destinationPage->getParentId(), $destinationPosition);
        

        $newParentChildren = Db::pageChildren($destinationPage->getId());
        $newIndex = 0; //initial value

        if(count($newParentChildren) > 0) {
            $newIndex = $newParentChildren[0]['row_number'] - 1;  //set as first page
            if ($destinationPosition > 0) {
                if (isset($newParentChildren[$destinationPosition - 1]) && isset($newParentChildren[$destinationPosition])) { //new position is in the middle of other pages
                    $newIndex = ($newParentChildren[$destinationPosition - 1]['row_number'] + $newParentChildren[$destinationPosition]['row_number']) / 2; //average
                } else { //new position is at the end
                    $newIndex = $newParentChildren[count($newParentChildren) - 1]['row_number'] + 1;
                }
            }
        }


        $data = array (
            'parentId' => $destinationPage->getId(),
            'rowNumber' => $newIndex
        );
        Db::updatePage($zoneName, $pageId, $data);

        //report url change
        $pageZone = $site->getZone($zoneName);
        $page = $pageZone->getElement($pageId);
        $newUrl = $page->getLink(true);

        global $dispatcher;
        $dispatcher->notify(new \Ip\Event\UrlChanged($this, $oldUrl, $newUrl));
        //report url change


        $answer = array();
        $answer['status'] = 'success';

        $this->_printJson($answer);



    }
    
    /**
     * Page is not moved yet. So we still can access all pages as they were before moving and throw move notifications
     * @param unknown_type $pageId
     * @param unknown_type $languageId
     * @param unknown_type $zoneName
     * @param unknown_type $parentId
     * @param unknown_type $position
     * @param unknown_type $destinationLanguageId
     * @param unknown_type $destinationZoneName
     * @param unknown_type $destinationParentId
     * @param unknown_type $destinationPosition
     */
    private function _notifyPageMove($pageId, $languageId, $zoneName, $parentId, $position, $destinationLanguageId, $destinationZoneName, $destinationParentId, $destinationPosition) {
        global $site;
        global $dispatcher;
        $movePageEvent = new \Ip\Event\PageMoved(null, $pageId, $languageId, $zoneName, $parentId, $position, $destinationLanguageId, $destinationZoneName, $destinationParentId, $destinationPosition);
        $dispatcher->notify($movePageEvent);
        
        $children = $site->getZone($zoneName)->getElements($languageId, $pageId);
        foreach ($children as $key => $child) {
            self::_notifyPageMove($child->getId(), $languageId, $zoneName, $pageId, $position, $destinationLanguageId, $destinationZoneName, $pageId, $position);
        }
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

        if (!isset($_REQUEST['destinationPageType'])) {
            trigger_error("Destination page type is not set");
            return false;
        }
        $destinationPageType = $_REQUEST['destinationPageType'];


        if (!isset($_REQUEST['destinationLanguageId'])) {
            trigger_error("Destination language id is not set");
            return false;
        }
        $destinationLanguageId = $_REQUEST['destinationLanguageId'];


        if (!isset($_REQUEST['destinationZoneName'])) {
            trigger_error("Destination zone name is not set");
            return false;
        }
        $destinationZoneName = $_REQUEST['destinationZoneName'];

        //check if destination page exists


        $destinationZone = $site->getZone($destinationZoneName);
        if ($destinationPageType == 'zone') {
            $rootElementId = Db::rootContentElement($destinationZone->getId(), $destinationLanguageId);
            if (!$rootElementId) {
                trigger_error('Can\'t find root zone element.');
                return false;
            }

            $destinationPage = $destinationZone->getElement($rootElementId);
        } else {
            $destinationPage = $destinationZone->getElement($destinationPageId);
        }


        if (!$destinationPage) {
            trigger_error("Destination page does not exist");
            return false;
        }


        if ($websiteId == 0) { //local page
            $children = Db::pageChildren($destinationPage->getId());
            $destinationPosition = count($children); //paste at the bottom
            Model::copyPage($zoneName, $pageId, $destinationZoneName, $destinationPage->getId(), $destinationPosition);
        } else { //remote page
            trigger_error("remotes are not supported yest.");
        }

        $answer['status'] = 'success';
        $answer['destinationPageId'] = $destinationPage->getId();

        $this->_printJson($answer);
    }


    /**
     *
     * Array of pages and subpages
     * @param array $pages
     */
    private function _createPagesRecursion ($targetPageId, $pages) {
        foreach ($pages as $pageKey => $page) {

            $newPageId = Db::insertPage($targetPageId, $page);
            if ($newPageId == false) {
                return;
            }

            foreach ($page['widgets'] as $widgetKey => $widget) {
                Model::addWidget($targetId = $newPageId, $widget['data'], $widget);
            }

            if (! empty($page['subpages'])) {
                self::_createPagesRecursion($newPageId, $page['subpages']);
            }
        }
    }

    /**
     * Remove page from session as open one.
     *
     */
    private function _closePage () {
        $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : null;
        $websiteId = isset($_REQUEST['websiteId']) ? $_REQUEST['websiteId'] : null;
        $languageId = isset($_REQUEST['languageId']) ? $_REQUEST['languageId'] : null;
        $zoneName = isset($_REQUEST['zoneName']) ? $_REQUEST['zoneName'] : null;
        $id = isset($_REQUEST['pageId']) ? $_REQUEST['pageId'] : null;

        $jsTreeId = $this->_jsTreeId($websiteId, $languageId, $zoneName, $id);

        unset($_SESSION['modules']['standard']['menu_management']['openNode'][$jsTreeId]);
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
        if ($responseData === null || empty ($responseData['status']) || $responseData['status'] != 'success' || ! isset ($responseData['response'])) {
            trigger_error('Incorrect response from the server '.$response);
            return false;
        }

        return $responseData['response'];
    }




    /*
     * Print Json answer
     */
    private function _printJson ($data) {
        header("HTTP/1.0 200 OK");
        header('Content-type: text/json; charset=utf-8');
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Pragma: no-cache");
        echo json_encode($data);

    }


    /**
     *
     * Generate unique id to uniquely identify node in jsTree
     * @param int $websiteId
     * @param int $languageId
     * @param string $zoneName
     * @param mixed $id
     */
    private function _jsTreeId($websiteId, $languageId = null, $zoneName = null, $id = null) {
        $answer = 'page_' . $websiteId;
        if($languageId !== null && $languageId !== '') {
            $answer .= '_' . $languageId;
            if($zoneName !== null && $zoneName !== '') {
                $answer .= '_' . $zoneName;
                if($id !== null && $id !== '') {
                    $answer .= '_' . $id;
                }
            }
        }
        return $answer;
    }





}
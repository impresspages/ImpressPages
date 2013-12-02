<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Module\Pages;





class AdminController extends \Ip\Controller
{


    public function index()
    {
        $app = \Ip\ServiceLocator::application();
        $data = array (
            'securityToken' =>  $app->getSecurityToken(),
            'imageDir' => ipFile('Ip/Module/Pages/img/')
        );
        $content = Template::content($data);
        $answer = Template::addLayout($content);

        return new \Ip\Response($answer);
    }





    /**
     *
     * Get children of selected jsTree node
     */
    public function getChildren () {
        $parentType = isset($_REQUEST['type']) ? $_REQUEST['type'] : null;
        $parentWebsiteId = isset($_REQUEST['websiteId']) ? $_REQUEST['websiteId'] : null;
        $parentLanguageId = isset($_REQUEST['languageId']) ? $_REQUEST['languageId'] : null;
        $parentZoneName = isset($_REQUEST['zoneName']) ? $_REQUEST['zoneName'] : null;
        $parentId = isset($_REQUEST['pageId']) ? (int)$_REQUEST['pageId'] : null;

        if (!isset($_REQUEST['externalLinking'])) {
            trigger_error("Popup status is not set");
            return;
        }
        $externalLinking = $_REQUEST['externalLinking'];
        $list = $this->getList ($externalLinking, $parentType, $parentWebsiteId, $parentLanguageId, $parentZoneName, $parentId);


        return new \Ip\Response\Json($list);
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
    private function getList ($externalLinking, $parentType, $parentWebsiteId, $parentLanguageId, $parentZoneName, $parentId) {


        $jsTreeId = self::_jsTreeId($parentWebsiteId, $parentLanguageId, $parentZoneName, $parentId);

        //store status only on local menu tree
        if (true || $parentWebsiteId == 0) {
            $_SESSION['modules']['standard']['menu_management']['openNode'][$jsTreeId] = 1;
        }

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
                        $children = $this->getList($externalLinking, 'website', $item['id'], null, null, $item['id']);
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
                foreach ($items as $item) {

                    $state = 'closed';
                    $children = false;

                    $jsTreeId = $this->_jsTreeId($parentWebsiteId, $item['id'], $parentZoneName, $parentId);

                    //if node status is open
                    if ( !empty($_SESSION['modules']['standard']['menu_management']['openNode'][$jsTreeId])) {
                        $state = 'open';
                        $children = $this->getList($externalLinking, 'language', $parentWebsiteId, $item['id'], null, $item['id']);
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
                foreach ($items as $item) {

                    $state = 'closed';
                    $children = false;

                    $jsTreeId = $this->_jsTreeId($parentWebsiteId, $parentLanguageId, $item['id'], $item['id']);
                    //if node status is open
                    if (!empty($_SESSION['modules']['standard']['menu_management']['openNode'][$jsTreeId])) {
                        $state = 'open';
                        $children = $this->getList($externalLinking, 'zone', $parentWebsiteId, $parentLanguageId, $item['id'], $item['id']);
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
                        $children = $this->getList($externalLinking, 'page', $parentWebsiteId, $parentLanguageId, $parentZoneName, $item['id']);
                        if (count($children) == 0) {
                            $children = false;
                            $state = 'leaf';
                        }
                    }

                    if ($item['visible']) {
                        $icon = '';
                    } else {
                        $icon = ipUrl('Ip/Module/Pages/img/file_hidden.png');
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
                        $icon = ipUrl('Ip/Module/Pages/img/file_hidden.png');
                    }

                    //if node status is open
                    if (!empty($_SESSION['modules']['standard']['menu_management']['openNode'][$jsTreeId])) {
                        $state = 'open';
                        $children = $this->getList($externalLinking, 'page', $parentWebsiteId, $parentLanguageId, $parentZoneName, $item['id']);
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
     * Get page update form HTML
     */
    public function getPageForm() {

        if (!isset($_REQUEST['pageId'])) {
            trigger_error("Page id is not set");
            return;
        }

        $pageId = (int)$_REQUEST['pageId'];

        if (!isset($_REQUEST['zoneName'])) {
            trigger_error("Zone name is not set");
            return false;
        }

        $zone = ipContent()->getZone($_REQUEST['zoneName']);

        if (!($zone)) {
            trigger_error("Can't find zone");
            return false;
        }

        $page = $zone->getPage($pageId);

        if (! $page) {
            trigger_error ("Page does not exist");
            return false;
        }

        $tabs = array();


        $title = __('General', 'ipAdmin');
        $content = Template::generateTabGeneral();
        $tabs[] = array('title' => $title, 'content' => $content);

        $title = __('SEO', 'ipAdmin');
        $content = Template::generateTabSEO();
        $tabs[] = array('title' => $title, 'content' => $content);

        $title = __('Advanced', 'ipAdmin');
        $content = Template::generateTabAdvanced();
        $tabs[] = array('title' => $title, 'content' => $content);

        $title = __('Design', 'ipAdmin');
        $content = $this->getPageDesignOptionsHtml($zone, $page, array('show_submit_button' => true));
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

        return new \Ip\Response\Json($answer);
    }

    public function getZoneProperties() {

        $params = \Ip\ServiceLocator::request()->getRequest();

        if (empty($params['zoneName'])) {
            throw new \Ip\CoreException("Missing required parameter");
        }
        $zoneName = $params['zoneName'];

        if (empty($params['languageId'])) {
            throw new \Ip\CoreException("Missing required parameter");
        }
        $languageId = $params['languageId'];

        $zones = \Ip\Internal\ContentDb::getZones($languageId);
        if (!$zones) {
            throw new \Ip\CoreException("Language doesn't exist");
        }

        if (empty($zones[$zoneName])) {
            throw new \Ip\CoreException("Zone doesn't exist");
        }
        $zoneData = $zones[$zoneName];

        $answer = array();

        $title = __('SEO', 'ipAdmin');

        $propertiesData = array (
            'form' => Forms::zoneSeoForm($languageId, $zoneName, $zoneData['title'], $zoneData['url'], $zoneData['keywords'], $zoneData['description'])
        );
        $content = \Ip\View::create('view/zoneProperties.php', $propertiesData)->render();
        $tabs[] = array('title' => $title, 'content' => $content);

        $data = array (
            'tabs' => $tabs
        );

        $tabsView = \Ip\View::create('view/tabs.php', $data);



        $answer['html'] = $tabsView->render();
        return new \Ip\Response\Json($answer);
    }


    public function getLanguageProperties() {

        $params = \Ip\ServiceLocator::request()->getRequest();

        if (empty($params['languageId'])) {
            throw new \Ip\CoreException("Missing required parameter");
        }
        $languageId = $params['languageId'];
        $answer = array();

        $title = __('SEO', 'ipAdmin');

        $language = \Ip\ServiceLocator::content()->getLanguageById($languageId);

        if (!$language) {
            throw new \Ip\CoreException("Language doesn't exist. Language id: " . $languageId);
        }

        $propertiesData = array (
            'form' => Forms::languageForm($languageId, $language->getVisible(), $language->getTitle(), $language->getAbbreviation(), $language->getUrl(), $language->getCode(), $language->getTextDirection())
        );
        $content = \Ip\View::create('view/languageProperties.php', $propertiesData)->render();
        $tabs[] = array('title' => $title, 'content' => $content);

        $data = array (
            'tabs' => $tabs
        );

        $tabsView = \Ip\View::create('view/tabs.php', $data);



        $answer['html'] = $tabsView->render();
        return new \Ip\Response\Json($answer);
    }

    public function saveZoneProperties()
    {
        $request = \Ip\ServiceLocator::request();
        $request->mustBePost();
        $params = $request->getPost();


        if (empty($params['zoneName'])) {
            throw new \Ip\CoreException("Missing required parameter");
        }
        $zoneName = $params['zoneName'];
        $zoneId = ipContent()->getZone($zoneName)->getId();

        if (empty($params['languageId'])) {
            throw new \Ip\CoreException("Missing required parameter");
        }
        $languageId = $params['languageId'];

        $form = Forms::zoneSeoForm($languageId, $zoneName);

        $data = $form->filterValues($params);

        $errors = $form->validate($params);


        if (empty($errors)) {
            $zoneData = array(
                'title' => $data['title'],
                'url' => $data['url'],
                'keywords' => $data['keywords'],
                'description' => $data['description']
            );

            try {
                ZoneModel::updateZone($languageId, $zoneId, $zoneData);
            } catch (DuplicateUrlException $e) {
                $errors['url'] = __('Following url already has been used.', 'ipAdmin');
            }
        }




        if ($errors) {
            $data = array(
                'status' => 'error',
                'errors' => $errors
            );
        } else {
            $data = array(
                'status' => 'success',
            );
        }
        return new \Ip\Response\Json($data);
    }



    public function saveLanguageProperties()
    {
        $request = \Ip\ServiceLocator::request();
        $request->mustBePost();
        $params = $request->getPost();

        if (empty($params['languageId'])) {
            throw new \Ip\CoreException("Missing required parameter");
        }
        $languageId = $params['languageId'];

        $form = Forms::languageForm($languageId, '', '', '', '', '', '');

        $data = $form->filterValues($params);

        $errors = $form->validate($params);


        if (empty($errors)) {
            $languageData = array(
                'd_long' => $data['title'],
                'url' => urlencode($data['url']),
                'd_short' => $data['abbreviation'],
                'text_direction' => $data['direction'],
                'code' => $data['code'],
                'visible' => isset($data['visible']) ? 1 : 0
            );

            try {
                $languageModel = new LanguageModel();
                $languageModel->updateLanguage($languageId, $languageData);
            } catch (DuplicateUrlException $e) {
                $errors['url'] = __('Following url already has been used.', 'ipAdmin');
            }
        }



        if ($errors) {
            $data = array(
                'status' => 'error',
                'errors' => $errors
            );
        } else {
            $data = array(
                'status' => 'success',
            );
        }
        return new \Ip\Response\Json($data);
    }

    /**
     * @param $zone
     * @param $page
     * @return string content
     */
    private function getPageDesignOptionsHtml($zone, $page, $data)
    {
        $data['defaultLayout'] = $zone->getLayout();
        $data['layouts'] = \Ip\Module\Content\Model::getThemeLayouts();

        $data['layout'] = \Ip\Internal\ContentDb::getPageLayout(
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
    public function getPageLink() {
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
                $answer['link'] = \Ip\Internal\Deprecated\Url::generate($_REQUEST['languageId']);
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

                $answer['link'] = \Ip\Internal\Deprecated\Url::generate($_REQUEST['languageId'], $_REQUEST['zoneName']);

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

                $pageId = (int)$_REQUEST['pageId'];
                $zone = ipContent()->getZone($_REQUEST['zoneName']);
                if (! $zone) {
                    trigger_error("Can't find zone");
                    return false;
                }

                $page = $zone->getPage($pageId);

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

        return new \Ip\Response\Json($answer);
    }

    /**
     *
     * Update page
     */
    public function updatePage () {

        $answer = array();


        if (!isset($_REQUEST['pageId'])) {
            trigger_error("Page id is not set");
            return false;
        }
        $pageId = (int)$_REQUEST['pageId'];

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
            while (!Db::availableUrl($tmpUrl, $pageId)) {
                $tmpUrl = $_POST['url'].'-'.$i;
                $i++;
            }
            $_POST['url'] = $tmpUrl;
        }
        //end make url

        if (strtotime($_POST['createdOn']) === false) {
            $answer['errors'][] = array('field' => 'createdOn', 'message' => __('Incorrect date format. Example:  ', 'ipAdmin').date("Y-m-d"));
        }

        if (strtotime($_POST['lastModified']) === false) {
            $answer['errors'][] = array('field' => 'lastModified', 'message' => __('Incorrect date format. Example:  ', 'ipAdmin').date("Y-m-d"));
        }

        if ($_POST['type'] == 'redirect' && $_POST['redirectURL'] == '') {
            $answer['errors'][] = array('field' => 'redirectURL', 'message' => __('External url can\'t be empty', 'ipAdmin'));
        }


        if (empty($answer['errors'])) {
            Db::updatePage($_POST['zoneName'], $pageId, $_POST);
            $answer['status'] = 'success';
        } else {
            $answer['status'] = 'error';
        }

        return new \Ip\Response\Json($answer);
    }


    /**
     *
     * Create new page
     */
    public function createPage () {

        $answer = array();

        if (!isset($_REQUEST['buttonTitle'])) {
            trigger_error('Button title is not set');
            return;
        }
        $buttonTitle = $_REQUEST['buttonTitle'];


        if (isset($_REQUEST['languageId'])) {
            $language = \Ip\ServiceLocator::content()->getLanguageById($_REQUEST['languageId']);
        } else {
            $languages = Db::getLanguages();
            $languageArray = $languages[0];
            $language = \Ip\ServiceLocator::content()->getLanguageById($languageArray['id']);
        }

        if (empty($language) || !$language) {
            trigger_error('Can\'t find any language');
            return;
        }


        if (isset($_REQUEST['zoneName'])) {
            $zone = ipContent()->getZone($_REQUEST['zoneName']);
        } else {
            $associatedZones = Db::getZones();
            $zoneArray = array_shift($associatedZones);
            if ($zoneArray) {
                $zone = ipContent()->getZone($zoneArray['name']);
            }
        }

        if (empty($zone) || $zone == false) {
            trigger_error('Can\'t find any zone');
            return;
        }

        if (isset($_REQUEST['pageId'])) {
            $parentPage = $zone->getPage((int)$_REQUEST['pageId']);
        }



        if (empty($parentPage)) {
            $parentPageId = Db::rootContentElement($zone->getId(), $language->getId());

            if($parentPageId === false) {
                trigger_error("Can't find root zone element.");
                return false;
            }

            $parentPage = $zone->getPage($parentPageId);
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
        $data['visible'] = !ipGetOption('Pages.hideNewPages');



        $newPageId = Db::insertPage($parentPage->getId(), $data);

        $answer['status'] = 'success';

        //find language
        $tmpId = $parentPage->getId();
        $element = \Ip\Module\Content\DbFrontend::getPage($tmpId);
        while($element['parent'] !== null) {
            $tmpUrlVars[] = $element['url'];
            $element = \Ip\Module\Content\DbFrontend::getPage($element['parent']);
        }
        $languageId = \Ip\Module\Content\DbFrontend::languageByRootPage($element['id']);
        //end find language

        $answer['refreshId'] = $this->_jsTreeId(0, $languageId, $parentPage->getZoneName(), $parentPage->getId());

        return new \Ip\Response\Json($answer);
    }


    /**
     *
     * Delete the page
     */
    public function deletePage () {
        if (!isset($_REQUEST['pageId'])) {
            trigger_error("Page id is not set");
            return false;
        }
        $pageId = (int)$_REQUEST['pageId'];

        if (!isset($_REQUEST['zoneName'])) {
            trigger_error("zoneName is not set");
            return false;
        }
        $zoneName = $_REQUEST['zoneName'];

        Model::deletePage($zoneName, $pageId);

        $answer = array ();
        $answer['status'] = 'success';

        return new \Ip\Response\Json($answer);
    }

    /**
     *
     * Move page to another location
     */
    public function movePage () {
        if (!isset($_REQUEST['pageId'])) {
            trigger_error("Page id is not set");
            return false;
        }
        $pageId = (int)$_REQUEST['pageId'];

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
        $destinationZone = ipContent()->getZone($destinationZoneName);
        if ($destinationPageType == 'zone') {
            $rootElementId = Db::rootContentElement($destinationZone->getId(), $destinationLanguageId);
            if (!$rootElementId) {
                trigger_error('Can\'t find root zone element.');
                return false;
            }
            $destinationPage = $destinationZone->getPage($rootElementId);
        } else {
            $destinationPage = $destinationZone->getPage($destinationPageId);
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
        $page = $destinationZone->getPage($pageId);
        $oldUrl = $page->getLink(true);
        //report url change

        $movePageValues = array(
            'pageId' => $pageId,

        );

        $this->notifyPageMove($pageId, $languageId, $zoneName, $page->getParentId(), $position, $destinationLanguageId, $destinationZoneName, $destinationPage->getParentId(), $destinationPosition);


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
        $pageZone = ipContent()->getZone($zoneName);
        $page = $pageZone->getPage($pageId);
        $newUrl = $page->getLink(true);

        ipDispatcher()->notify('site.urlChanged', array('oldUrl' => $oldUrl, 'newUrl' => $newUrl));
        //report url change


        $answer = array();
        $answer['status'] = 'success';

        return new \Ip\Response\Json($answer);



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
    private function notifyPageMove($pageId, $languageId, $zoneName, $parentId, $position, $destinationLanguageId, $destinationZoneName, $destinationParentId, $destinationPosition) {

        $eventData = array(
            'pageId' => $pageId,
            'oldLanguageId' => $languageId,
            'oldZoneName' => $zoneName,
            'oldParentId' => $parentId,
            'oldPosition' => $position,
            'newLanguageId' => $destinationLanguageId,
            'newZoneName' => $destinationZoneName,
            'newParentId' => $destinationParentId,
            'newPosition' => $destinationPosition,
        );

        ipDispatcher()->notify('site.pageMoved', $eventData);

        $children = ipContent()->getZone($zoneName)->getPages($languageId, $pageId);
        foreach ($children as $key => $child) {
            self::_notifyPageMove($child->getId(), $languageId, $zoneName, $pageId, $position, $destinationLanguageId, $destinationZoneName, $pageId, $position);
        }
    }

    /**
     *
     * Copy page from one place to another
     */
    public function copyPage() {
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
        $pageId = (int)$_REQUEST['pageId'];

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


        $destinationZone = ipContent()->getZone($destinationZoneName);
        if ($destinationPageType == 'zone') {
            $rootElementId = Db::rootContentElement($destinationZone->getId(), $destinationLanguageId);
            if (!$rootElementId) {
                trigger_error('Can\'t find root zone element.');
                return false;
            }

            $destinationPage = $destinationZone->getPage($rootElementId);
        } else {
            $destinationPage = $destinationZone->getPage($destinationPageId);
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

        return new \Ip\Response\Json($answer);
    }



    /**
     * Remove page from session as open one.
     *
     */
    public function closePage () {
        $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : null;
        $websiteId = isset($_REQUEST['websiteId']) ? $_REQUEST['websiteId'] : null;
        $languageId = isset($_REQUEST['languageId']) ? $_REQUEST['languageId'] : null;
        $zoneName = isset($_REQUEST['zoneName']) ? $_REQUEST['zoneName'] : null;
        $id = isset($_REQUEST['pageId']) ? (int)$_REQUEST['pageId'] : null;

        $jsTreeId = $this->_jsTreeId($websiteId, $languageId, $zoneName, $id);

        unset($_SESSION['modules']['standard']['menu_management']['openNode'][$jsTreeId]);
        return new \Ip\Response\Json(array('success' => 1));
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
        curl_setopt($ch, CURLOPT_REFERER, ipUrl(''));
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
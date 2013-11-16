<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip;

//TODOX move to Content service
use Guzzle\Parser\ParserRegistry;

/**
 *
 * Event dispatcher class
 *
 */
class Content {
    protected $currentLanguage;
    /**
     * @var \Ip\Frontend\Language[]
     */
    protected $languages;

    protected $layout;

    protected $zones = null;
    protected $zonesData = null;

    protected $languageUrl = null;
    protected $urlVars = null;
    protected $zoneUrl = null;
    protected $currentZoneName = null;

    protected $blockContent = null;
    protected $slotContent = null;

    protected $currentPage = null;


    /**
     *
     * @return bool true if the system is in management state
     *
     */
    public function isManagementState(){
        $backendLoggedIn = isset($_SESSION['backend_session']['userId']) && $_SESSION['backend_session']['userId'] != null;
        return ($backendLoggedIn && \Ip\ServiceLocator::getRequest()->getQuery('cms_action', 0) === 'manage');
    }


    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function getLayout()
    {
        if (!$this->layout) {
            $layout = 'main.php';

            $zone = $this->getCurrentZone();
            if ($zone) {
                $page = $this->getCurrentPage();
                if ($page) {
                    $layout = \Ip\Frontend\Db::getPageLayout($zone->getAssociatedModuleGroup(), $zone->getAssociatedModule(), $page->getId());
                }

                if (!$layout || !is_file(\Ip\Config::themeFile($layout))) {
                    $layout = $zone->getLayout();
                }

            }
            if (!is_file(\Ip\Config::themeFile($layout))) {
                $layout = 'main.php';
            }

            $this->layout = $layout;
        }

        return $this->layout;
    }

    /**
     * @return \Ip\Frontend\Language
     */
    public function getCurrentLanguage()
    {
        if (!$this->currentLanguage) {
            $languageUrl = $this->getLanguageUrl();
            foreach ($this->getLanguages() as $language) {
                if ($language->getUrl() == $languageUrl) {
                    $this->currentLanguage = $language;
                }
            }
        }
        return $this->currentLanguage;
    }

    /**
     *
     * @param $zoneName Name of zone you wish to get
     * @return \Ip\Frontend\Zone
     *
     */
    public function getZone($zoneName)
    {
        if ($zoneName === '') {
            return new \Ip\Frontend\Zone404(null, null);
        }

        if (isset($this->zones[$zoneName])) {
            return $this->zones[$zoneName];
        }

        $zonesData= $this->getZonesData();

        if(!isset($zonesData[$zoneName]))
        {
            return false;
        }

        $zoneData = $this->zonesData[$zoneName];
        $this->zones[$zoneName] = $this->createZone($zoneData);
        return $this->zones[$zoneName];

    }

    /**
     *
     * @return \Ip\Frontend\Zone[]
     *
     */
    public function getZones(){
        $answer = array();
        foreach ($this->getZonesData() as $zoneData) {
            $answer[] = $this->getZone($zoneData['name']);
        }
        return $answer;
    }

    protected function getZonesData()
    {
        if (!$this->zonesData) {
            $this->zonesData = \Ip\Frontend\Db::getZones($this->getCurrentLanguage()->getId());
        }
        return $this->zonesData;
    }

    public function getCurrentZone()
    {
        if ($this->currentZoneName === null) {
            $this->parseUrl();
        }
        return $this->getZone($this->currentZoneName);
    }

    public function getCurrentPage()
    {
        if ($this->currentPage === null) {
            $this->currentPage = $this->getCurrentZone()->getCurrentPage();
            if ($this->currentPage === null) {
                $this->currentPage = new \Ip\Frontend\Page404(1, '');
            }
        }
        return $this->currentPage;
    }



    /**
     *
     * @return array - all website languages. Each element is an object Language
     *
     */
    public function getLanguages()
    {
        if ($this->languages === null) {
            $languages = \Ip\Frontend\Db::getLanguages(true);
            $this->languages = array();
            foreach($languages as $data){
                $this->languages[] = $this->createLanguage($data);
            }
        }
        return $this->languages;
    }

    /**
     *
     * @return \Ip\Frontend\Language
     *
     */
    public function getLanguageById($id){
        foreach($this->getLanguages() as $language){
            if ($language->getId() === $id) {
                return $language;
            }
        }
        return false;
    }


    /**
     *
     * @param data array from database
     * @return Language
     *
     *
     */
    private function createLanguage($data)
    {
        $language = new \Ip\Frontend\Language($data['id'], $data['code'], $data['url'], $data['d_long'], $data['d_short'], $data['visible'], $data['text_direction']);
        return $language;
    }

    private function createZone($zoneData)
    {
        if ($zoneData['associated_module']) {
            $class = '\\Ip\\Module\\' . $zoneData['associated_module'] . '\\Zone';
            if (class_exists($class)) {
                $zoneObject = new $class($zoneData['name']);
            } else {
                $class = '\\Plugin\\' . $zoneData['associated_module'] . '\\Zone';
                $zoneObject = new $class($zoneData['name']);
            }
        } else {
            $zoneObject = new \Ip\Frontend\DefaultZone($zoneData);
        }

        $zoneObject->setId($zoneData['id']);
        $zoneObject->setName($zoneData['name']);
        $zoneObject->setLayout($zoneData['template']);
        $zoneObject->setTitle($zoneData['title']);
        $zoneObject->setUrl($zoneData['url']);
        $zoneObject->setKeywords($zoneData['keywords']);
        $zoneObject->setDescription($zoneData['description']);
        $zoneObject->setAssociatedModuleGroup($zoneData['associated_group']);
        $zoneObject->setAssociatedModule($zoneData['associated_module']);
        return $zoneObject;
    }



    public function getZoneUrl()
    {
        if ($this->zoneUrl === null) {
            $this->parseUrl();
        }
        return $this->zoneUrl;
    }

    public function getLanguageUrl()
    {
        if ($this->languageUrl === null) {
            $this->parseUrl();
        }
        return $this->languageUrl;
    }

    public function getUrlVars()
    {
        if ($this->urlVars === null) {
            $this->parseUrl();
        }
        return $this->urlVars;
    }

    private function parseUrl()
    {
        $path = \Ip\ServiceLocator::getRequest()->getRelativePath();
        $urlVars = explode('/', rtrim(parse_url($path, PHP_URL_PATH), '/'));
        if ($urlVars[0] == '') {
            array_shift($urlVars);
        }
        $this->urlVars = $urlVars;
        for ($i=0; $i< sizeof($urlVars); $i++){
            $urlVars[$i] = urldecode($urlVars[$i]);
        }
        if (ipGetOption('Config.multilingual')) {
            $this->languageUrl = urldecode(array_shift($urlVars));
        } else {
            $firstLanguageData = \Ip\Frontend\Db::getFirstLanguage();
            $this->languageUrl = $firstLanguageData['url'];
        }

        $zonesData = $this->getZonesData();

        if (count($urlVars)) {
            $potentialZoneUrl = urldecode($urlVars[0]);
            foreach ($zonesData as $zoneData) {
                if ($zoneData['url'] == $potentialZoneUrl) {
                    $this->zoneUrl = $potentialZoneUrl;
                    $this->currentZoneName = $zoneData['name'];
                    array_shift($urlVars);
                    break;
                }
            }
            if (!$this->zoneUrl) {
                $zoneWithNoUrl = null;
                foreach ($zonesData as $zoneData) {
                    if ($zoneData['url'] === '') {
                        $zoneWithNoUrl = $zoneData['name'];
                        $this->zoneUrl = '';
                        $this->currentZoneName = $zoneData['name'];
                        break;
                    }
                }
                if (!$zoneWithNoUrl) {
                    $this->currentZoneName = '';
                }

            }
        } else {
            if (empty($zonesData)) {
                throw new \Ip\CoreException('Please insert at least one zone');
            } else {
                $firstZoneData = array_shift($zonesData);
                $this->currentZoneName = $firstZoneData['name'];
            }
        }



        $this->urlVars = $urlVars;
    }

    public function generateUrl($languageId=null, $zoneName = null, $urlVars = null, $getVars = null, $escape = true){
        return \Ip\ServiceLocator::getSite()->generateUrl($languageId, $zoneName, $urlVars, $getVars, $escape);
    }





    public function setBlockContent($block, $content)
    {
        $this->blockContent[$block] = $content;
    }

    public function getBlockContent($block)
    {
        if (isset($this->blockContent[$block])) {
            return $this->blockContent[$block];
        } else {
            return null;
        }
    }

    public function generateBlock($blockName) {
        return new \Ip\Block($blockName);
    }


    public function setSlotContent($name, $content)
    {
        $this->slotContent[$name] = $content;
    }

    public function getSlotContent($name)
    {
        if (isset($this->slotContent[$name])) {
            return $this->slotContent[$name];
        } else {
            return null;
        }
    }

    public function generateSlot($name) {
        $dispatcher = \Ip\ServiceLocator::getDispatcher();
        $data = array (
            'slotName' => $name,
        );
        $event = new \Ip\Event($this, 'site.generateSlot', $data);
        $processed = $dispatcher->notifyUntil($event);

        if ($processed && $event->issetValue('content')) {
            $content = $event->getValue('content');
            if (is_object($content) && method_exists($content, 'render')) {
                $content = $content->render();
            }
            return (string)$content;
        } else {
            $predefinedContent = $this->getSlotContent($name);
            if ($predefinedContent !== null) {
                return $predefinedContent;
            }
        }
        return '';
    }



    /**
     * If we are in the management state and last revision is published, then create new revision.
     *
     */
    public function getRevision() {
        //TODOX cache revision
        $revision = null;
        if (\Ip\ServiceLocator::getContent()->isManagementState()){
            if (ipGetRequest()->getQuery('cms_revision')) {
                $revisionId = ipGetRequest()->getQuery('cms_revision');
                $revision = \Ip\Revision::getRevision($revisionId);
            }

            if ($revision === false || $revision['zoneName'] != ipGetCurrentZone()->getName() || $revision['pageId'] != $this->getCurrentPage()->getId() ) {
                if (!$this->getCurrentPage()) {
                    return null;
                }
                $revision = \Ip\Revision::getLastRevision(ipGetCurrentZone()->getName(), $this->getCurrentPage()->getId());
                if ($revision['published']) {
                    $revision = $this->duplicateRevision($revision['revisionId']);
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



    private function duplicateRevision($oldRevisionId){
        $revisionId = \Ip\Revision::duplicateRevision($oldRevisionId);
        $revision = \Ip\Revision::getRevision($revisionId);
        if ($revision === false) {
            throw new \Ip\CoreException("Can't find created revision " . $revisionId, \Ip\CoreException::REVISION);
        }
        return $revision;
    }


    /**
     *
     * @return array Each element in array is an Element
     *
     */
    public function getBreadcrumb($zoneName = null, $pageId = null){
        if ($zoneName === null && $pageId !== null || $zoneName !== null && $pageId === null) {
            trigger_error("This method can accept none or both parameters");
        }

        if ($zoneName === null && $pageId === null) {
            $zone = ipGetCurrentZone();
            if (!$zone) {
                return array();
            }
            return $zone->getBreadcrumb();
        } else {
            $zone = $this->getZone($zoneName);
            if (!$zone) {
                return array();
            }
            return $zone->getBreadcrumb($pageId);
        }

    }


    /**
     * TODOX check zone and language url's against this function
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


}
<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip;




/**
 *
 * Event dispatcher class
 *
 */
class Content
{
    protected $currentLanguage;
    /**
     * @var \Ip\Language[]
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

    protected $currentPage = null;
    protected $revision = null;

    /**
     *
     * @return bool true if the system is in management state
     *
     */
    public function isManagementState()
    {
        $backendLoggedIn = !empty($_SESSION['backend_session']['userId']);
        return $backendLoggedIn && \Ip\Internal\Content\Service::isManagementMode();
    }


    /**
     * @return \Ip\Language
     */
    public function getCurrentLanguage()
    {
        if (!$this->currentLanguage) {
            $this->parseUrl();
        }
        return $this->currentLanguage;
    }

    /**
     *
     * @param $zoneName
     * @return \Ip\Zone
     *
     */
    public function getZone($zoneName)
    {
        if ($zoneName === '404') {
            return new \Ip\Zone404(array('name' => '404'));
        }

        if (isset($this->zones[$zoneName])) {
            return $this->zones[$zoneName];
        }

        $zonesData = $this->getZonesData();

        if (!isset($zonesData[$zoneName])) {
            return false;
        }

        $zoneData = $this->zonesData[$zoneName];
        $this->zones[$zoneName] = $this->createZone($zoneData);
        return $this->zones[$zoneName];

    }

    /**
     *
     * @return \Ip\Zone[]
     *
     */
    public function getZones()
    {
        $answer = array();
        foreach ($this->getZonesData() as $zoneData) {
            $answer[] = $this->getZone($zoneData['name']);
        }
        return $answer;
    }

    protected function getZonesData()
    {
        if (!$this->zonesData) {
            $this->zonesData = Internal\ContentDb::getZones($this->getCurrentLanguage()->getId());
        }
        return $this->zonesData;
    }

    /**
     * @return Zone
     */
    public function getCurrentZone()
    {
        if ($this->currentZoneName === null) {
            $this->parseUrl();
        }
        return $this->getZone($this->currentZoneName);
    }

    /**
     * @return \Ip\Page
     */
    public function getCurrentPage()
    {
        if ($this->currentPage === null) {
            $this->parseUrl();
        }
        return $this->currentPage;
    }


    /**
     *
     * @return \Ip\Language[] - all website languages. Each element is an object Language
     *
     */
    public function getLanguages()
    {
        if ($this->languages === null) {
            $languages = Internal\ContentDb::getLanguages(true);
            $this->languages = array();
            foreach ($languages as $data) {
                $this->languages[] = $this->createLanguage($data);
            }
        }
        return $this->languages;
    }


    /**
     * @param $id
     * @return bool|Language
     */
    public function getLanguage($id)
    {
        $id = (int)$id;
        foreach ($this->getLanguages() as $language) {
            if ($language->getId() === $id) {
                return $language;
            }
        }
        return false;
    }


    /**
     * @param $data
     * @return Language
     */
    private function createLanguage($data)
    {
        $language = new \Ip\Language($data['id'], $data['code'], $data['url'], $data['d_long'], $data['d_short'], $data['visible'], $data['text_direction']);
        return $language;
    }

    private function createZone($zoneData)
    {
        if ($zoneData['associated_module']) {
            $class = '\\Ip\\Internal\\' . $zoneData['associated_module'] . '\\Zone';
            if (class_exists($class)) {
                $zoneObject = new $class($zoneData);
            } else {
                $class = '\\Plugin\\' . $zoneData['associated_module'] . '\\Zone';
                $zoneObject = new $class($zoneData);
            }
        } else {
            $zoneObject = new \Ip\DefaultZone($zoneData);
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
        $languages = $this->getLanguages();

        //check if admin
        if (!ipRequest()->isDefaultAction()) {
            //admin pages don't have zones
            if (!empty($_SESSION['ipLastLanguageId'])) {
                $this->currentLanguage = $this->getLanguage($_SESSION['ipLastLanguageId']);
                if (!$this->currentLanguage) {
                    $this->currentLanguage = $languages[0];
                }
            } else {
                $this->currentLanguage = $languages[0];
            }
            $this->languageUrl = $this->currentLanguage->getUrl();
            $this->currentZoneName = false;
            return;
        }

        //find language
        $path = \Ip\ServiceLocator::request()->getRelativePath();
        $urlVars = explode('/', rtrim(parse_url($path, PHP_URL_PATH), '/'));
        if ($urlVars[0] == '') {
            array_shift($urlVars);
        }
        $this->urlVars = $urlVars;
        for ($i = 0; $i < sizeof($urlVars); $i++) {
            $urlVars[$i] = urldecode($urlVars[$i]);
        }
        if (ipGetOption('Config.multilingual') && !empty($urlVars[0])) {
            $languageUrl = urldecode(array_shift($urlVars));
            $this->urlVars = $urlVars;
            foreach ($languages as $language) {
                if ($language->getUrl() == $languageUrl) {
                    $this->currentLanguage = $language;
                    $this->languageUrl = $languageUrl;
                    break;
                }
            }
            //language not found. Set current language as first language from the database and set current zone to '' which means error 404
            if (!$this->currentLanguage) {
                $this->currentLanguage = $languages[0];
                $this->languageUrl = $this->currentLanguage->getId();
                $this->currentZoneName = '';
                return;
            }
        } else {
            $this->currentLanguage = $languages[0];
            $this->languageUrl = $this->currentLanguage->getUrl();
        }
        $_SESSION['ipLastLanguageId'] = $this->currentLanguage->getId();

        //find zone
        $zonesData = $this->getZonesData();
        if (count($urlVars)) {
            $potentialZoneUrl = urldecode($urlVars[0]);
            foreach ($zonesData as $zoneData) {
                if ($zoneData['url'] == $potentialZoneUrl) {
                    $this->zoneUrl = $potentialZoneUrl;
                    $this->currentZoneName = $zoneData['name'];
                    array_shift($urlVars);
                    $this->urlVars = $urlVars;
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


        //find current page

        $zone = $this->getZone($this->currentZoneName);

        if ($zone) {
            $currentPage = $zone->getCurrentPage();
        } else {
            $currentPage = false;
        }

        if ($currentPage) {
            $this->currentPage = $currentPage;
        } else {
            $this->currentZoneName = '404';
            $this->currentPage = $this->currentPage = new \Ip\Page404(1, '404');
        }


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

    public function generateBlock($blockName)
    {
        return new \Ip\Block($blockName);
    }





    /**
     * If we are in the management state and last revision is published, then create new revision.
     *
     */
    public function getRevision()
    {
        if ($this->revision !== null) {
            return $this->revision;
        }
        $revision = false;
        if (\Ip\ServiceLocator::content()->isManagementState()) {
            if (ipRequest()->getQuery('cms_revision')) {
                $revisionId = ipRequest()->getQuery('cms_revision');
                $revision = \Ip\Revision::getRevision($revisionId);
            }

            if ($this->getCurrentPage()) {
                if ($revision === false || $revision['zoneName'] != ipContent()->getCurrentZone()->getName(
                    ) || $revision['pageId'] != $this->getCurrentPage()->getId()
                ) {
                    $revision = \Ip\Revision::getLastRevision(
                        ipContent()->getCurrentZone()->getName(),
                        $this->getCurrentPage()->getId()
                    );
                    if ($revision['published']) {
                        $revision = $this->duplicateRevision($revision['revisionId']);
                    }
                }
            } else {
                $revision = false;
            }
        } else {
            $currentPage = $this->getCurrentPage();
            if ($currentPage) {
                $revision = \Ip\Revision::getPublishedRevision(
                    ipContent()->getCurrentZone()->getName(),
                    $currentPage->getId()
                );
            }

        }
        $this->revision = $revision;
        return $revision;
    }


    private function duplicateRevision($oldRevisionId)
    {
        $revisionId = \Ip\Revision::duplicateRevision($oldRevisionId);
        $revision = \Ip\Revision::getRevision($revisionId);
        if ($revision === false) {
            throw new \Ip\CoreException("Can't find created revision " . $revisionId, \Ip\CoreException::REVISION);
        }
        return $revision;
    }


    /**
     * @param null $zoneName
     * @param null $pageId
     * @return \Ip\Page[]
     */
    public function getBreadcrumb($zoneName = null, $pageId = null)
    {
        if ($zoneName === null && $pageId !== null || $zoneName !== null && $pageId === null) {
            trigger_error("This method can accept none or both parameters");
        }

        if ($zoneName === null && $pageId === null) {
            $zone = ipContent()->getCurrentZone();
            if (!$zone) {
                return array();
            }
            $breadcrumb = $zone->getBreadcrumb();
        } else {
            $zone = $this->getZone($zoneName);
            if (!$zone) {
                return array();
            }
            $breadcrumb = $zone->getBreadcrumb($pageId);
        }

        if (is_array($breadcrumb)) {
            return $breadcrumb;
        } else {
            return array();
        }

    }


    /**
     *
     * @return string title of current page
     *
     */
    public function getTitle()
    {
        $curZone = ipContent()->getCurrentZone();
        if (!$curZone) {
            return '';
        }
        $curEl = $curZone->getCurrentPage();
        if ($curEl && $curEl->getPageTitle() != '') {
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
    public function getDescription()
    {
        $curZone = ipContent()->getCurrentZone();
        if (!$curZone) {
            return '';
        }
        $curEl = $curZone->getCurrentPage();
        if ($curEl && $curEl->getDescription() != '') {
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
    public function getKeywords()
    {
        $curZone = ipContent()->getCurrentZone();
        if (!$curZone) {
            return '';
        }

        $curEl = $curZone->getCurrentPage();
        if ($curEl && $curEl->getKeywords() != '') {
            return $curEl->getKeywords();
        } else {
            return $curZone->getKeywords();
        }
    }

    /**
     * Invalidate zones cache. Use this method if you have added or removed some zones
     */
    //TODOX make private and execute when needed
    public function invalidateZones()
    {
        $this->zones = null;
        $this->zonesData = null;
    }


}
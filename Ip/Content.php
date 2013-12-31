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
    /**
     * @var \Ip\Language[]
     */
    protected $languages;

    protected $zones = null;
    protected $zonesData = null;


    protected $blockContent = null;

    protected $revision = null;

    protected $requestParser;


    public function __construct()
    {
        $this->requestParser = new \Ip\Internal\Content\RequestParser();
    }




    /**
     * @return \Ip\Language
     */
    public function getCurrentLanguage()
    {
        return $this->requestParser->getCurrentLanguage();
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
        $this->zones[$zoneName] = \Ip\Internal\Content\Helper::createZone($zoneData);
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
        return $this->requestParser->getCurrentZone();
    }

    /**
     * @return \Ip\Page
     */
    public function getCurrentPage()
    {
        return $this->requestParser->getCurrentPage();
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
                $this->languages[] = \Ip\Internal\Content\Helper::createLanguage($data);
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






    public function getUrlPath()
    {
        return $this->requestParser->getUrlPath();
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
        if (ipIsManagementState()) {
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
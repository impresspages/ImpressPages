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
    protected $requestParser;

    public function __construct()
    {
        $this->requestParser = new \Ip\Internal\Content\RequestParser();
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
     * @return \Ip\Page
     */
    public function getCurrentPage()
    {
        return $this->requestParser->getCurrentPage();
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

    public function getUrlPath()
    {
        return $this->requestParser->getUrlPath();
    }

    public function getBlockContent($block)
    {
        if (isset($this->blockContent[$block])) {
            return $this->blockContent[$block];
        } else {
            return null;
        }
    }

    public function setBlockContent($block, $content)
    {
        $this->blockContent[$block] = $content;
    }

    public function generateBlock($blockName)
    {
        return new \Ip\Block($blockName);
    }

    /**
     * If we are in the management state and last revision is published, then create new revision.
     *
     */
    public function getCurrentRevision()
    {
        return $this->requestParser->getCurrentRevision();
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
     * @return Zone
     */
    public function getCurrentZone()
    {
        return $this->requestParser->getCurrentZone();
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


    public static function addLanguage($title, $abbreviation, $code, $url, $visible, $textDirection = 'ltr', $position = null)
    {
        $languageId = \Ip\Internal\Languages\Service::addLanguage($title, $abbreviation, $code, $url, $visible, $textDirection, $position = null);
        return $languageId;
    }

    public static function deleteLanguage($languageId)
    {
        \Ip\Internal\Languages\Service::delete($languageId);
    }


    public static function addZone($title, $name, $url, $layout, $metaTitle, $metaKeywords, $metaDescription, $position)
    {

        $zoneName = \Ip\Internal\Pages\Service::addZone($title, $name, $url, $layout, $metaTitle, $metaKeywords, $metaDescription, $position);
        return $zoneName;
    }

    public static function updateZone($zoneName, $languageId, $title, $url, $name, $layout, $metaTitle, $metaKeywords, $metaDescription)
    {
        \Ip\Internal\Pages\Service::updateZone($zoneName, $languageId, $title, $url, $name, $layout, $metaTitle, $metaKeywords, $metaDescription);
    }


    /**
     * @param string $zoneName
     * @param int $pageId
     * @param array $data
     */
    public static function updatePage($zoneName, $pageId, $data)
    {
        \Ip\Internal\Pages\Service::updatePage($zoneName, $pageId, $data);
    }


    public static function addPage($parentId, $title, $data = array())
    {
        $newPageId = \Ip\Internal\Pages\Service::addPage($parentId, $title, $data );
        return $newPageId;
    }


    /**
     * @param string $zoneName
     * @param int $languageId
     * @return int
     */
    public static function getRootId($zoneName, $languageId)
    {
        $rootId = \Ip\Internal\Pages\Service::rootId($zoneName, $languageId);
        return $rootId;
    }

    public static function copyPage($pageId, $destinationParentId, $destinationPosition)
    {
        $pageId = \Ip\Internal\Pages\Service::copyPage($pageId, $destinationParentId, $destinationPosition);
        return $pageId;
    }


    public static function movePage($pageId, $destinationParentId, $destinationPosition)
    {
        \Ip\Internal\Pages\Service::movePage($pageId, $destinationParentId, $destinationPosition);
    }

    public static function deletePage($pageId)
    {
        \Ip\Internal\Pages\Service::deletePage($pageId);
    }


}
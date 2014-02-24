<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip;


/**
 * Language, page, block and other CMS content
 * Can be treated as a gateway to CMS content.
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

    public function __construct()
    {
    }

    /**
     * Get all available zones
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

    /**
     * @return array|null
     */
    protected function getZonesData()
    {
        if (!$this->zonesData) {
            $this->zonesData = Internal\ContentDb::getZones($this->getCurrentLanguage()->getId());
        }
        return $this->zonesData;
    }

    /**
     * Get current language object
     * @return \Ip\Language
     */
    public function getCurrentLanguage()
    {
        return ipCurrentPage()->getLanguage();
    }

    /**
     * Get specific zone object
     * @param string $zoneName
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
            return new \Ip\Zone404(array('name' => '404'));
        }

        $zoneData = $this->zonesData[$zoneName];
        $this->zones[$zoneName] = \Ip\Internal\Content\Helper::createZone($zoneData);
        return $this->zones[$zoneName];

    }

    public function getPage($pageId)
    {
        try {
            $page = new \Ip\Page($pageId);
        } catch (\Ip\Exception $e) {
            return FALSE;
        }
        return $page;

    }

    /**
     * Get current page object
     *
     * @return \Ip\Page
     */
    public function getCurrentPage()
    {
        return ipCurrentPage()->getPage();
    }

    /**
     * Get specific language object
     * @param int $id Language ID
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
     * Get all website languages
     *
     * @return \Ip\Language[] All website languages. Each element is a Language object.
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
     * Get URL of the current page
     *
     * @return array
     */
    public function getUrlPath()
    {
        if (!ipCurrentPage()) {
            return array();
        }
        return ipCurrentPage()->getUrlPath();
    }

    /**
     * Get page block HTML content
     * @param string $block Block name
     * @return string HTML code
     */
    public function getBlockContent($block)
    {
        if (isset($this->blockContent[$block])) {
            return $this->blockContent[$block];
        } else {
            return null;
        }
    }

    /**
     * Set page block HTML content
     * @param string $block Block name
     * @param string $content HTML code
     */
    public function setBlockContent($block, $content)
    {
        $this->blockContent[$block] = $content;
    }

    /**
     * Generate block object
     *
     * @param string $blockName
     * @return Block
     */
    public function generateBlock($blockName)
    {
        return new \Ip\Block($blockName);
    }

    /**
     * If in management state and the last revision was published, create a new revision.
     * @ignore
     */
    public function getCurrentRevision()
    {
        return ipCurrentPage()->getCurrentRevision();
    }

    /**
     * Get a breadcrumb
     *
     * Gets an array of pages representing a tree path to a current page.
     *
     * @param int $pageId
     * @return \Ip\Page[]
     */
    public function getBreadcrumb($pageId = null)
    {
        if ($pageId !== null) {
            $page = new \Ip\Page($pageId);
        } else {
            $page = ipContent()->getCurrentPage();
        }

        if ($page) {
            $pages[] = $page;
            $parentPageId = $page->getParentId();
            while (!empty($parentPageId)) {
                $parentPage = new \Ip\Page($parentPageId);
                $pages[] = $parentPage;
                $parentPageId = $parentPage->getParentId();
            }
        }
        array_pop($pages);

        $breadcrumb = array_reverse($pages);
        return $breadcrumb;
    }

    /**
     * Get current zone object
     *
     * @return Zone
     */
    public function getCurrentZone()
    {
        return ipCurrentPage()->getZone();
    }

    /**
     * Get a page title
     *
     * @return string Title of the current page
     *
     */
    public function getTitle()
    {
        $page = ipCurrentPage()->getPage();
        if ($page) {
            return $page->getTitle();
        }
    }

    /**
     * Get the current page description
     *
     * @return string Description of the current page
     *
     */
    public function getDescription()
    {
        $page = ipCurrentPage()->getPage();
        if ($page) {
            return $page->getDescription();
        }
    }

    /**
     * Get the current page keywords
     *
     * @return string Keywords for the current page
     *
     */
    public function getKeywords()
    {
        $page = ipCurrentPage()->getPage();
        if ($page) {
            return $page->getKeywords();
        }
    }

    /**
     * Add website language
     *
     * @param string $title
     * @param string $abbreviation
     * @param string $code
     * @param string $url
     * @param bool $visible
     * @param string $textDirection
     * @param null $position
     * @return mixed
     */
    public static function addLanguage($title, $abbreviation, $code, $url, $visible, $textDirection = 'ltr', $position = null)
    {
        $languageId = \Ip\Internal\Languages\Service::addLanguage($title, $abbreviation, $code, $url, $visible, $textDirection, $position = null);
        return $languageId;
    }

    /**
     * Delete a language with specific ID
     *
     * @param $languageId
     */
    public static function deleteLanguage($languageId)
    {
        \Ip\Internal\Languages\Service::delete($languageId);
    }







    /**
     * Update page data
     * @param string $zoneName
     * @param int $pageId
     * @param array $data
     */
    public static function updatePage($zoneName, $pageId, $data)
    {
        \Ip\Internal\Pages\Service::updatePage($pageId, $data);
    }

    /**
     * Add a new page
     *
     * @param int $parentId Parent page ID
     * @param string $title
     * @param array $data
     * @return mixed
     */
    public static function addPage($parentId, $title, $data = array())
    {
        $newPageId = \Ip\Internal\Pages\Service::addPage($parentId, $title, $data );
        return $newPageId;
    }


    /**
     * Get root page id for the specfic language
     *
     * @param string $zoneName
     * @param int $languageId
     * @return int Page ID
     */
    public static function getRootPageId($zoneName, $languageId)
    {
        $rootId = \Ip\Internal\Pages\Service::rootId($zoneName, $languageId);
        return $rootId;
    }

    /**
     * Copy page
     *
     * @param int $pageId Source page ID
     * @param int $destinationParentId Target parent ID
     * @param int $destinationPosition
     * @return int New copied page ID
     */
    public static function copyPage($pageId, $destinationParentId, $destinationPosition)
    {
        $pageId = \Ip\Internal\Pages\Service::copyPage($pageId, $destinationParentId, $destinationPosition);
        return $pageId;
    }

    /**
     * Move a page to a different location on a website tree
     * @param int $pageId Source page ID
     * @param int $destinationParentId Target parent ID
     * @param int $destinationPosition
     */

    public static function movePage($pageId, $destinationParentId, $destinationPosition)
    {
        \Ip\Internal\Pages\Service::movePage($pageId, $destinationParentId, $destinationPosition);
    }

    /**
     * Delete a page
     * @param int $pageId
     */
    public static function deletePage($pageId)
    {
        \Ip\Internal\Pages\Service::deletePage($pageId);
    }


}

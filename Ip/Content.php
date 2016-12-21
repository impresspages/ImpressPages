<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip;


/**
 * Language, page, block and other content.
 * Can be treated as a gateway to framework content.
 *
 */
class Content
{
    /**
     * @var \Ip\Language[]
     */
    protected $languages;
    protected $blockContent;

    /**
     * @var \Ip\Language
     */
    protected $currentLanguage;

    /**
     * @var \Ip\Page
     */
    protected $currentPage;

    protected $currentRevision;

    public function __construct()
    {
    }

    /**
     * Get current language object
     * @return \Ip\Language
     */
    public function getCurrentLanguage()
    {
        return $this->currentLanguage;
    }

    /**
     * @ignore Used only for internal purposes
     */
    public function _setCurrentLanguage($currentLanguage)
    {
        $this->currentLanguage = $currentLanguage;
    }

    /**
     * @param $pageId
     * @return \Ip\Page
     */
    public function getPage($pageId)
    {
        try {
            $page = new \Ip\Page($pageId);
        } catch (\Ip\Exception $e) {
            return null;
        }
        return $page;

    }

    /**
     * @param string $alias
     * @param string|null $languageCode
     * @return \Ip\Page
     */
    public function getPageByAlias($alias, $languageCode = null)
    {
        if ($languageCode === null) {
            $languageCode = ipContent()->getCurrentLanguage()->getCode();
        }

        $row = ipDb()->selectRow(
            'page',
            '*',
            array('alias' => $alias, 'languageCode' => $languageCode, 'isDeleted' => 0)
        );
        if (!$row) {
            return null;
        }

        return new \Ip\Page($row);
    }

    /**
     * Get current page object
     *
     * @return \Ip\Page
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @ignore used only for internal purposes
     */
    public function _setCurrentPage($page)
    {
        $this->currentPage = $page;
        $this->currentRevision = null;
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
     * Get specific language object
     * @param string $code Language code
     * @return null|Language
     */

    public function getLanguageByCode($code)
    {
        foreach ($this->getLanguages() as $language) {
            if ($language->getCode() === $code) {
                return $language;
            }
        }
        return null;
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
            if (!ipConfig()->database()) {
                $this->languages = [];
            } else {
                $languages = \Ip\Internal\Languages\Service::getLanguages();
                $this->languages = array();
                foreach ($languages as $data) {
                    $this->languages[] = \Ip\Internal\Content\Helper::createLanguage($data);
                }
            }
        }
        return $this->languages;
    }

    /**
     * @ignore
     */
    public function _invalidateLanguages()
    {
        $this->languages = null;
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
        if ($this->currentRevision !== null) {
            return $this->currentRevision;
        }

        if (!$this->currentPage) {
            return null;
        }

        $revision = null;
        $pageId = $this->currentPage->getId();

        if (ipRequest()->getQuery('_revision') && ipAdminId()) {
            $revisionId = ipRequest()->getQuery('_revision');
            $revision = \Ip\Internal\Revision::getRevision($revisionId);
            if ($revision['pageId'] != $pageId) {
                $revision = null;
            }
        }

        if (!$revision && ipIsManagementState()) {
            $revision = \Ip\Internal\Revision::getLastRevision($pageId);
            if ($revision['isPublished']) {
                $duplicatedId = \Ip\Internal\Revision::duplicateRevision($revision['revisionId']);
                $revision = \Ip\Internal\Revision::getRevision($duplicatedId);
            }
        }

        if (!$revision) {
            $revision = \Ip\Internal\Revision::getPublishedRevision($this->currentPage->getId());
        }

        $this->currentRevision = $revision;
        return $this->currentRevision;
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
        $breadcrumb = array();
        if (!empty($pages)) {
            array_pop($pages);
            $breadcrumb = $pages;
        }

        $breadcrumb = array_reverse($breadcrumb);
        $breadcrumb = ipFilter('ipBreadcrumb', $breadcrumb);
        return $breadcrumb;
    }

    /**
     * Get a page title
     *
     * @return string Title of the current page
     *
     */
    public function getTitle()
    {
        if ($this->currentPage) {
            return $this->currentPage->getMetaTitle() ? $this->currentPage->getMetaTitle(
            ) : $this->currentPage->getTitle();
        }
        return '';
    }

    /**
     * Get the current page description
     *
     * @return string Description of the current page
     *
     */
    public function getDescription()
    {
        if ($this->currentPage) {
            return $this->currentPage->getDescription();
        }
        return '';
    }

    /**
     * Get the current page keywords
     *
     * @return string Keywords for the current page
     *
     */
    public function getKeywords()
    {
        if ($this->currentPage) {
            return $this->currentPage->getKeywords();
        }
        return '';
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
    public function addLanguage($title, $abbreviation, $code, $url, $visible, $textDirection = 'ltr', $position = null)
    {
        $languageId = \Ip\Internal\Languages\Service::addLanguage(
            $title,
            $abbreviation,
            $code,
            $url,
            $visible,
            $textDirection,
            $position = null
        );
        return $languageId;
    }

    /**
     * Delete a language with specific ID
     *
     * @param $languageId
     */
    public function deleteLanguage($languageId)
    {
        \Ip\Internal\Languages\Service::delete($languageId);
    }


    /**
     * Update page data
     * @param int $pageId
     * @param array $data
     */
    public function updatePage($pageId, $data)
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
    public function addPage($parentId, $title, $data = array())
    {
        $newPageId = \Ip\Internal\Pages\Service::addPage($parentId, $title, $data);
        return $newPageId;
    }

    /**
     * Copy page
     *
     * @param int $pageId Source page ID
     * @param int $destinationParentId Target parent ID
     * @param int $destinationPosition
     * @return int New copied page ID
     */
    public function copyPage($pageId, $destinationParentId, $destinationPosition)
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

    public function movePage($pageId, $destinationParentId, $destinationPosition)
    {
        \Ip\Internal\Pages\Service::movePage($pageId, $destinationParentId, $destinationPosition);
    }

    /**
     * Delete a page
     * @param int $pageId
     */
    public function deletePage($pageId)
    {
        \Ip\Internal\Pages\Service::deletePage($pageId);
    }


    /**
     * Get children
     * @param null $parentId
     * @param null $from
     * @param null $till
     * @param string $orderBy
     * @param string $direction
     * @return Page[]
     */
    public function getChildren(
        $parentId = null,
        $from = null,
        $till = null,
        $orderBy = 'pageOrder',
        $direction = 'ASC'
    ) {
        if ($parentId === null) {
            $parentId = $this->getCurrentPage()->getId();
        }

        $page = $this->getPage($parentId);
        if (!$page) {
            return array();
        }
        return $page->getChildren($from, $till, $orderBy, $direction);
    }

    /**
     * Get menu of currently active or specified page.
     * This method could be used to check if page is in some of menus or not.
     * Eg. if (ipContent()->getPageMenu()->getAlias() == 'menu1') //if current page is one of pages in menu1
     * @param null $pageId
     * @return Page
     */
    public function getPageMenu($pageId = null)
    {
        if ($pageId === null) {
            $page = $this->getCurrentPage();
        } else {
            $page = $this->getPage($pageId);
        }
        if (!$page) {
            return false;
        }
        $rootPage = $page;
        while ($page = $this->getPage($page->getParentId())) {
            $rootPage = $page;
        }
        return $rootPage;
    }


    /**
     * Get menus of the website.
     * Menu list may be different on each language. That's why there is $languageCode parameter.
     * If $languageCode is omitted, current language is used by default.
     *
     * @param string $languageCode
     * @return \Ip\Page[]
     */
    public function getMenus($languageCode = null)
    {
        $result = \Ip\Internal\Pages\Service::getMenus($languageCode);
        $objectArray = array();
        foreach ($result as $menuData)
        {
            $objectArray[] = new \Ip\Page($menuData);
        }
        return $objectArray;
    }

    /**
     * Get menu.
     *
     * @param string $languageCode
     * @param string $alias unique menu identificator within the language
     * @return \Ip\Page
     */
    public static function getMenu($languageCode, $alias)
    {
        $result = \Ip\Internal\Pages\Service::getMenu($languageCode, $alias);
        if ($result) {
            return new \Ip\Page($result);
        }
        return $result;
    }

    /**
     * @param $languageCode
     * @param $alias
     * @param $title
     * @param string $type 'tree' or 'list'
     * @return int
     */
    public function addMenu($languageCode, $alias, $title, $type = 'tree')
    {
        return \Ip\Internal\Pages\Service::createMenu($languageCode, $alias, $title, $type);
    }

    /**
     * @param $id menu id
     */
    public function deleteMenu($languageCode, $alias)
    {
        $menu = $this->getMenu($languageCode, $alias);
        if ($menu) {
            $this->deletePage($menu->getId());
        }
    }

    /**
     * Get Id of the default page in current or specified language
     * @param string $languageCode
     * @return int
     */
    public function getDefaultPageId($languageCode = null)
    {
        if ($languageCode == null) {
            $languageCode = ipContent()->getCurrentLanguage()->getCode();
        }
        $pageId = ipJob('ipDefaultPageId', array('languageCode' => $languageCode));
        return $pageId;
    }

}

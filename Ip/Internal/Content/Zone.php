<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Internal\Content;


class Zone extends \Ip\Zone
{
    var $db;

    function __construct($properties)
    {
        $this->db = new DbFrontend();
        parent::__construct($properties);
    }


    function getPages(
        $languageId = null,
        $parentPageId = null,
        $startFrom = 0,
        $limit = null,
        $includeHidden = false,
        $reverseOrder = false
    ) {


        if ($languageId == null) {
            $languageId = ipContent()->getCurrentLanguage()->getId();
        }

        $urlVars = array();

        if ($parentPageId != null) { //if parent specified
            $parentPages = $this->getBreadCrumb($parentPageId);
            foreach ($parentPages as $key => $page) {
                $urlVars[] = $page->getUrl();
            }
        }

        $breadCrumb = $this->getBreadCrumb();
        $depth = sizeof($urlVars) + 1;
        if (isset($breadCrumb[$depth - 1])) {
            $selectedId = $breadCrumb[$depth - 1]->getId();
        } else {
            $selectedId = null;
        }

        if ($reverseOrder) {
            $dbPages = $this->db->getPages(
                $this->getName(),
                $parentPageId,
                $languageId,
                $this->currentPage ? $this->currentPage->getId() : null,
                $selectedId,
                'desc',
                $startFrom,
                $limit,
                $includeHidden
            );
        } else {
            $dbPages = $this->db->getPages(
                $this->getName(),
                $parentPageId,
                $languageId,
                $this->currentPage ? $this->currentPage->getId() : null,
                $selectedId,
                'asc',
                $startFrom,
                $limit,
                $includeHidden
            );
        }
        $pages = array();
        foreach ($dbPages as $dbPage) {
            $newPage = $this->makePageFromDb($dbPage, sizeof($urlVars) == 1);

            if ($selectedId == $dbPage['id']) {
                $newPage->markAsInCurrentBreadcrumb(1);
            } else {
                $newPage->markAsInCurrentBreadcrumb(0);
            }

            if ($this->currentPage && $this->currentPage->getId() == $dbPage['id']) {
                $newPage->setCurrent(1);
            } else {
                $newPage->setCurrent(0);
            }
            $pages[] = $newPage;
        }

        foreach ($pages as $key => $page) { //link generation optimization.
            if ($pages[$key]->getType() == 'default') {
                $pages[$key]->setLink(
                    \Ip\Internal\Deprecated\Url::generate(
                        $languageId,
                        $this->getName(),
                        array_merge($urlVars, array($page->getUrl())),
                        null
                    )
                );
            }
        }

        return $pages;

    }


    function getPage($pageId)
    {
        $dbPage = $this->db->getPage($pageId);
        if ($dbPage) {
            $dbParentPage = $this->db->getPage($dbPage['parent']);
            $page = $this->makePageFromDb($dbPage, $dbParentPage['parent'] == null);
            return $page;
        } else {
            return false;
        }
    }


    function getFirstPage($parentId, $level)
    {

        $pages = $this->db->getPages(
            $this->getName(),
            $parentId,
            ipContent()->getCurrentLanguage()->getId(),
            null,
            null,
            'asc',
            0,
            null
        );
        foreach ($pages as $page) {
            switch ($page['type']) {
                case 'inactive':
                case 'subpage':
                case 'redirect':
                    $subPage = $this->getFirstPage($page['id'], $level + 1);
                    if ($subPage) {
                        return $subPage;
                    }
                    break;
                case 'default':
                default:
                    return $this->makePageFromDb($page, $level == 1);
                    break;
            }

        }
        return false;
    }

    function findPage($urlVars, $getVars)
    {
        $currentEl = null;

        $elId = $this->db->getRootPageId($this->getName(), ipContent()->getCurrentLanguage()->getId());
        if ($elId) {
            if (sizeof($urlVars) == 0) {
                return $this->getFirstPage($elId, 1);
            } else {
                foreach ($urlVars as $value) {
                    $tmp = $this->db->getPageByUrl($value, $elId);
                    if ($tmp) {
                        $currentEl = $tmp;
                        $elId = $currentEl['id'];
                    } else {
                        return null;
                    }
                }
                return $this->makePageFromDb($currentEl, sizeof($urlVars) == 0);
            }
        } else {
            return false;
        }
    }


    private function makePageFromDb($dbPage, $firstLevel)
    {
        $newPage = new Page($dbPage['id'], $this->getName());
        $newPage->setNavigationTitle($dbPage['navigationTitle']);
        $newPage->setPageTitle($dbPage['page_title']);
        $newPage->setKeywords($dbPage['keywords']);
        $newPage->setDescription($dbPage['description']);
        $newPage->setUrl($dbPage['url']);
        //$newPage->setText($dbPage['cached_text']);
        $newPage->setUpdatedAt($dbPage['updatedAt']);
        $newPage->setCreatedAt($dbPage['createdAt']);
        $newPage->setVisible($dbPage['isVisible']);
        if ($firstLevel) {
            $newPage->setParentId(null);
        } else {
            $newPage->setParentId($dbPage['parentId']);
        }
        $newPage->setType($dbPage['type']);
        $newPage->setRedirectUrl($dbPage['redirect_url']);
        return $newPage;
    }


}

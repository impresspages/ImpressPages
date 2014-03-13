<?php
/**
 * @package ImpressPages
 */

namespace Ip;


class Page
{
    /** int - unique number of element. */
    protected $id;
    /** string - title that will be placed in menu on the link to this page */
    protected $title;

    /**
     * @var string full url relative to language url
     */
    protected $urlPath;

    /**
     * @var string language code. For example 'en'.
     */
    protected $languageCode;

    /** string - meta tag title */
    protected $metaTitle;
    /** string - meta tag keywords */
    protected $keywords;
    /** string - meta tag description */
    protected $description;
    /** string - date when last change in this page was made. MySql timestamp format 'YYYY-MM-DD HH:MM:SS' */
    protected $updatedAt;
    /** string - page creation date in MySql timestamp format 'YYYY-MM-DD HH:MM:SS' */
    protected $createdAt;
    /** int - id of parent Element or null.*/
    protected $parentId;
    /** int - unique string identificator of a page.*/
    protected $alias;

    /** string - element type<br />
     * <br />
     * Available values:<br />
     * default - show content<br />
     * inactive - without link on it<br />
     * subpage - redirect to first subpage<br />
     * redirect - redirect to external page<br />
     * error404
     */
    protected $type;

    /** bool */
    protected $isVisible;

    protected $inBreadcrumb = false;

    /**
     * @param int|array $id
     */
    public function __construct($id)
    {
        if (!is_array($id)) {
            $page = ipDb()->selectRow('page', '*', array('id' => $id));
            if (!$page) {
                throw new \Ip\Exception("Page #{$id} not found.");
            }
        } else {
            $page = $id;
        }

        foreach ($page as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * This function is being executed every time before this page element is being displayed.
     *
     * @param \Ip\Controller $controller
     */
    public function init(\Ip\Controller $controller)
    {
        //by default do nothing. Override to do something
    }

    /**
     * Get page ID
     *
     * @return int Page ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set page ID
     *
     * @ignore
     * @param $id int
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get page navigation title
     *
     * @return string A title for page navigation
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @ignore
     *
     * @param $title string
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get page title
     *
     * @return string Page title
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * @return string Language code
     */
    public function getLanguageCode()
    {
        return $this->languageCode;
    }

    /**
     * @ignore
     * @param $metaTitle string
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;
    }

    /**
     * Get page keywords
     *
     * @return string Page keywords
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set keywords
     *
     * @ignore
     * @param $keywords string
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * Get page description
     *
     * @return string Page description text
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @ignore
     * @param $description string
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get page modification date and time
     *
     * @return string in MySql timestamp format 'YYYY-MM-DD HH:MM:SS'
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set page modification date and time
     *
     * @ignore
     * @param $updatedAt string in MySql timestamp format 'YYYY-MM-DD HH:MM:SS'
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get page creation date and time
     *
     * @return string in MySql timestamp format 'YYYY-MM-DD HH:MM:SS'
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set page creation date and time
     *
     * @param $createdAt string in MySql timestamp format 'YYYY-MM-DD HH:MM:SS'
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @ignore
     * @param $modifyFrequency int represents average amount of days between changes
     */
    public function setModifyFrequency($modifyFrequency)
    {
        $this->modifyFrequency = $modifyFrequency;
    }

    /**
     * @ignore
     * @return float
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @ignore
     * @param $priority float
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * Get parent page ID
     *
     * @return int Parent page ID
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set parent page id
     *
     * @ignore
     * @param $parentId int
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     * Get page URL address
     * @return string Page URL
     */
    public function getLink()
    {
        if (ipGetOption('Config.multilingual')) {
            $language = ipContent()->getLanguageByCode($this->languageCode);
            return ipConfig()->baseUrl() . $language->getUrlPath() . $this->urlPath;
        } else {
            return ipConfig()->baseUrl() . $this->urlPath;
        }
    }

    /**
     * Get the last part of the page URL
     * @return string Page URL
     */
    public function getUrlPath()
    {
        return $this->urlPath;
    }

    /**
     * @ignore
     * @param $url string
     */
    public function setUrlPath($urlPath)
    {
        $this->urlPath = $urlPath;
    }

    /**
     * Check if the page is the currently opened page in the browser
     *
     * @return bool True, if the page is a current page
     */
    public function isCurrent()
    {
        $curPage = ipContent()->getCurrentPage();
        return $curPage && $this->getId() == ipContent()->getCurrentPage()->getId();
    }

    /**
     * Check if the page exists in current breadcrumb
     *
     * @return bool True, if the page is in a current breadcrumb
     */
    public function isInCurrentBreadcrumb()
    {
        if ($this->inBreadcrumb === null) {
            $breadcrumb = ipContent()->getBreadcrumb();
            $ids = array();
            foreach($breadcrumb as $page) {
                $ids[] = $page->getId();
            }
            $this->inBreadcrumb = in_array($this->getId(), $ids);
        }
        return $this->inBreadcrumb;
    }

    /**
     * Get the page type (e.g., default, redirect or other types)
     *
     * @return string Page type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the page type
     * @ignore
     *
     * @param $type string Page type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get page redirect address URL
     *
     * @return string Redirect URL address
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * Set page redirect URL
     *
     * @ignore
     * @param $redirectUrl string Redirect URL address
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * Get page visibility status
     *
     * @return bool Visibility
     */
    public function isVisible()
    {
        return $this->isVisible;
    }

    /**
     * Set page visibility status
     *
     * @ignore
     * @param $visible bool
     */
    public function setIsVisible($visible)
    {
        $this->isVisible = $visible;
    }

    /**
     * Get page disabled status. Disabled means it is not clickable in the interface.
     *
     * @return bool Visibility
     */
    public function isDisabled()
    {
        return $this->isDisabled;
    }

    /**
     * Set page disabled status
     *
     * @ignore
     * @param $disabled bool
     */
    public function setIsDisabled($disabled)
    {
        $this->isDisabled = $disabled;
    }

    /**
     * Get page security status. Sescured means that it can't be visible even by knowing the url of the page.
     *
     * @return bool Visibility
     */
    public function isSecured()
    {
        return $this->isSecured;
    }

    /**
     * Set page secure status
     *
     * @ignore
     * @param $secured bool
     */
    public function setIsSecured($secured)
    {
        $this->isSecured = $secured;
    }

    /**
     * Get page blank status.
     *
     * @return bool Open link in new window
     */
    public function isBlank()
    {
        return $this->isBlank;
    }

    /**
     * Set page blank status
     *
     * @ignore
     * @param $isBlank bool
     */
    public function setIsBlank($isBlank)
    {
        $this->isBlank = $isBlank;
    }


    public static function createList($list)
    {
        $pages = array();
        foreach ($list as $page) {
            $pages[] = new \Ip\Page($page);
        }

        return $pages;
    }

    public function getChildren()
    {
        $list = ipDb()->selectAll('page', 'id', array('parentId' => $this->id, 'isVisible' => 1, 'isDeleted' => 0), 'ORDER BY `pageOrder`');

        return static::createList($list);
    }


    /**
     * Get page alias - unique string identificator of the page
     * @return string Page alias
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set the page alias
     * @ignore
     *
     * @param $type string Page alias
     */
    public function setAlias($alias)
    {
        $this->type = $alias;
    }
}

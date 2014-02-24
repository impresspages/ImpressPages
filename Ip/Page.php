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
    /** string - html version of page content. Used for search and similar tasks. This field can be used like cache. It isn't the content that will be printed out to the site. */
    protected $html;
    /** string - text version of page content. Used for search and similar tasks. This field can be used like cache. It isn't the content that will be printed out to the site. */
    protected $text;
    /** string - date when last change in this page was made. MySql timestamp format 'YYYY-MM-DD HH:MM:SS' */
    protected $updatedAt;
    /** string - page creation date in MySql timestamp format 'YYYY-MM-DD HH:MM:SS' */
    protected $createdAt;
    /** integer - average amount of days between changes */
    protected $modifyFrequency;
    /** float - value from 0 to 1, representing importance of page. 0 - lowest importance, 1 - highest importance. Used in XML sitemap. */
    protected $priority;
    /** int - id of parent Element or null.*/
    protected $parentId;
    /** string - url (including http://) to this page. */
    protected $link;
    /** bool - true if this element is currently active page */
    protected $current;
    /** bool - true if this element is part of current breadcrumb */
    protected $inBreadcrumb;
    /** int - depth of the element (starts at 1) */
    protected $depth;
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
    /** string - redirect URL if element type is "redirect" */
    protected $redirectUrl;

    /** string - zone name of element */
    protected $zoneName;
    /** bool */
    protected $isVisible;

    /** Element - next sibling element */
    protected $nextElement;
    /** Element - previous sibling element */
    protected $previousElement;

    public function __construct($id)
    {
        $this->id = $id;

        $page = ipDb()->selectRow('page', '*', array('id' => $id));

        if (!$page) {
            throw new \Ip\Exception("Page #{$id} not found.");
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
     * @ignore
     * @return string Get page text
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @ignore
     * @param $text string
     */
    public function setText($text)
    {
        $this->text = $text;
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
            return ipConfig()->baseUrl() . $this->languageCode . '/' . $this->urlPath;
        } else {
            return ipConfig()->baseUrl() . $this->urlPath;
        }
    }

    /**
     * @ignore
     * @param $link string
     */
    public function setLink($link)
    {
        $this->link = $link;
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
        return $this->getId() == ipContent()->getCurrentPage()->getId();
    }

    /**
     * Set the page as currently opened in the browser
     *
     * @ignore
     * @param $current bool
     */
    public function setCurrent($current)
    {
        $this->current = $current;
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
     * Set the page as existing in current breadcrumb
     * @ignore
     * @param $selected bool
     */
    public function markAsInCurrentBreadcrumb($inBreadcrumb)
    {
        $this->inBreadcrumb = $inBreadcrumb;
    }

    /**
     * Get page depth level in a menu tree
     *
     * @return int Depth level.
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * Set page depth level in a menu tree
     * @ignore
     *
     * @param $depth int Depth level
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;
    }

    /**
     * Get zone name of the page
     *
     * @return string Zone name
     */
    public function getZoneName()
    {
        return $this->zoneName;
    }

    /**
     * @ignore
     *
     * @param $zoneName string
     */
    public function setZoneName($zoneName)
    {
        $this->zoneName = $zoneName;
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
     *
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

    public static function createList($list)
    {
        $pages = array();
        foreach ($list as $page) {
            $pages[] = new \Ip\Page($page['id']);
        }

        return $pages;
    }
}

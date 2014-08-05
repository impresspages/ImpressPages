<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Menu;


/**
 *
 * Menu item element class. Used as an item in menu slot.
 *
 */
class Item
{

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $pageTitle;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string link target attribute. Eg. _blank
     */
    protected $target;

    /**
     * @var bool true if this menu item is within breadcrumb
     */
    protected $selected;

    /**
     * @var bool true if this menu item represents currently opened page
     */
    protected $current;

    /**
     * @var Item[]
     */
    protected $children;

    /**
     * @var int
     */
    protected $depth;

    /**
     * @var bool
     */
    protected $disabled;

    /**
     * @var bool
     */
    protected $blank;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $alias;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getPageTitle()
    {
        return $this->pageTitle;
    }

    /**
     * @param $pageTitle
     */
    public function setPageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * @return bool
     */
    public function isInCurrentBreadcrumb()
    {
        return (bool)$this->selected;
    }

    /**
     * @param $selected
     */
    public function markAsInCurrentBreadcrumb($selected)
    {
        $this->selected = (bool)$selected;
    }

    /**
     * @return bool
     */
    public function isCurrent()
    {
        return (bool)$this->current;
    }

    /**
     * @param $current
     */
    public function markAsCurrent($current)
    {
        $this->current = (bool)$current;
    }

    /**
     * @return array
     */
    public function getChildren()
    {
        if (!is_array($this->children)) {
            return array();
        }
        return $this->children;
    }

    /**
     * @param $children
     * @throws \Exception
     */
    public function setChildren($children)
    {
        if (!is_array($children)) {
            throw new \Exception("SetChildren expects array of \\Ip\\Menu\\Item");
        }
        $this->children = $children;
    }

    /**
     * @return int
     */
    public function getDepth()
    {
        return (int)$this->depth;
    }

    /**
     * @param $depth
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;
    }

    /**
     * @return bool
     */
    public function getBlank()
    {
        return (bool)$this->blank;
    }

    /**
     * @param $blank
     */
    public function setBlank($blank)
    {
        $this->blank = $blank;
    }

    /**
     * @return bool
     */
    public function getDisabled()
    {
        return (bool)$this->disabled;
    }

    /**
     * @param $disabled
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return bool
     */
    public function isDisabled()
    {
        return $this->getDisabled();
    }

    /**
     * @return bool
     */
    public function isBlank()
    {
        return $this->getBlank();
    }


}

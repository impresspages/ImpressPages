<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\InlineValue\Entity;


class Scope
{
    const SCOPE_PAGE = 1;
    const SCOPE_PARENT_PAGE = 2;
    const SCOPE_LANGUAGE = 3;
    const SCOPE_GLOBAL = 4;

    private $scopeType;
    private $languageId;
    private $pageId;

    /**
     * Set type
     *
     * @param $type
     */
    public function setType($type)
    {
        $this->scopeType = $type;
    }

    /**
     * Set language id
     *
     * @param int $languageId
     */
    public function setLanguageId($languageId)
    {
        $this->languageId = $languageId;
    }

    /**
     * Set page id
     *
     * @param int $pageId
     */
    public function setPageId($pageId)
    {
        $this->pageId = $pageId;
    }

    /**
     * Get type
     *
     * @return
     */
    public function getType()
    {
        return $this->scopeType;
    }

    /**
     * Get language id
     *
     * @return int
     */
    public function getLanguageId()
    {
        return $this->languageId;
    }

    /**
     * Get page id
     *
     * @return int
     */
    public function getPageId()
    {
        return $this->pageId;
    }

}

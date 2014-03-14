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

    public function setType($type)
    {
        $this->scopeType = $type;
    }

    public function setLanguageId($languageId)
    {
        $this->languageId = $languageId;
    }

    public function setPageId($pageId)
    {
        $this->pageId = $pageId;
    }


    public function getType()
    {
        return $this->scopeType;
    }

    public function getLanguageId()
    {
        return $this->languageId;
    }

    public function getPageId()
    {
        return $this->pageId;
    }

}

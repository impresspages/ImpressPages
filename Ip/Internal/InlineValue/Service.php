<?php
/**
 * @package ImpressPages

 *
 */

namespace Ip\Internal\InlineValue;


class Service
{
    const SCOPE_PAGE = 1;
    const SCOPE_PARENT_PAGE = 2;
    const SCOPE_LANGUAGE = 3;
    const SCOPE_GLOBAL = 4;

    private $module;
    private $dao;
    /**
     * @param string $module
     */
    public function __construct($module)
    {
        $this->module = $module;
        $this->dao = new Dao($this->module);
    }

    // GET
    public function getValue($key, $languageId, $pageId)
    {
        return $this->dao->getValue($key, $languageId, $pageId);
    }

    public function getPageValue($key, $languageId, $pageId)
    {
        return $this->dao->getPageValue($key, $languageId, $pageId);
    }

    public function getLanguageValue($key, $languageId)
    {
        return $this->dao->getLanguageValue($key, $languageId);
    }

    public function getGlobalValue($key)
    {
        return $this->dao->getGlobalValue($key);
    }

    /**
     * Last get operation scope
     * @return int
     */
    public function getLastOperationScope()
    {
        return $this->dao->getLastOperationScope();
    }

    // SET
    public function setPageValue($key, $languageId, $pageId, $value)
    {
        return $this->dao->setPageValue($key, $languageId, $pageId, $value);
    }


    public function setLanguageValue($key, $languageId, $value)
    {
        return $this->dao->setLanguageValue($key, $languageId, $value);
    }

    public function setGlobalValue($key, $value)
    {
        return $this->dao->setGlobalValue($key, $value);
    }

    // DELETE
    public function deletePageValue($key, $pageId)
    {
        $this->dao->deletePageValue($key, $pageId);
    }

    public function deleteLanguageValue($key, $languageId)
    {
        $this->dao->deleteLanguageValue($key, $languageId);
    }

    public function deleteGlobalValue($key)
    {
        $this->dao->deleteGlobalValue($key);
    }

}

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

    /**
     * Get value
     *
     * @param string $key
     * @param int $languageId
     * @param int $pageId
     * @return \Ip\Internal\InlineValue\Entity\Scope
     */
    public function getValue($key, $languageId, $pageId)
    {
        return $this->dao->getValue($key, $languageId, $pageId);
    }

    /**
     * Get page value
     *
     * @param string $key
     * @param int $languageId
     * @param int $pageId
     * @return mixed|null
     */
    public function getPageValue($key, $languageId, $pageId)
    {
        return $this->dao->getPageValue($key, $languageId, $pageId);
    }

    /**
     * Get language value
     *
     * @param string $key
     * @param int $languageId
     * @return bool
     */
    public function getLanguageValue($key, $languageId)
    {
        return $this->dao->getLanguageValue($key, $languageId);
    }

    /**
     * Get global value
     *
     * @param string $key
     * @return bool
     */
    public function getGlobalValue($key)
    {
        return $this->dao->getGlobalValue($key);
    }

    /**
     * Get last operation scope
     *
     * @return Entity\Scope
     */
    public function getLastOperationScope()
    {
        return $this->dao->getLastOperationScope();
    }

    /**
     * Set page value
     *
     * @param string $key
     * @param int $languageId
     * @param int $pageId
     * @param string $value
     */
    public function setPageValue($key, $languageId, $pageId, $value)
    {
        $this->dao->setPageValue($key, $languageId, $pageId, $value);
    }

    /**
     * Set language value
     *
     * @param string $key
     * @param int $languageId
     * @param string $value
     */
    public function setLanguageValue($key, $languageId, $value)
    {
        $this->dao->setLanguageValue($key, $languageId, $value);
    }

    /**
     * Get global value
     *
     * @param string $key
     * @param string $value
     */
    public function setGlobalValue($key, $value)
    {
        $this->dao->setGlobalValue($key, $value);
    }

    /**
     * Delete page value
     *
     * @param string $key
     * @param int $pageId
     */
    public function deletePageValue($key, $pageId)
    {
        $this->dao->deletePageValue($key, $pageId);
    }

    /**
     * Delete language value
     *
     * @param string $key
     * @param int $languageId
     */
    public function deleteLanguageValue($key, $languageId)
    {
        $this->dao->deleteLanguageValue($key, $languageId);
    }

    /**
     * Delete global value
     *
     * @param string $key
     */
    public function deleteGlobalValue($key)
    {
        $this->dao->deleteGlobalValue($key);
    }

}

<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\InlineManagement;


class Dao
{

    const MODULE_NAME = 'inline_management';
    const PREFIX_TEXT = 'txt_';
    const PREFIX_IMAGE = 'img_';
    const PREFIX_LOGO = 'logo_';

    public function __construct()
    {
        $this->inlineValueService = new \Ip\Internal\InlineValue\Service(self::MODULE_NAME);
    }


    // GET
    public function getValue($prefix, $key, $languageId, $pageId)
    {
        return $this->inlineValueService->getValue($prefix . $key, $languageId, $pageId);
    }

    public function getPageValue($prefix, $key, $languageId, $pageId)
    {
        return $this->inlineValueService->getPageValue($prefix . $key, $languageId, $pageId);
    }

    public function getLanguageValue($prefix, $key, $languageId)
    {
        return $this->inlineValueService->getLanguageValue($prefix . $key, $languageId);
    }

    public function getGlobalValue($prefix, $key)
    {
        return $this->inlineValueService->getGlobalValue($prefix . $key);
    }

    public function setPageValue($prefix, $key, $languageId, $pageId, $value)
    {
        $this->inlineValueService->setPageValue($prefix . $key, $languageId, $pageId, $value);
    }


    public function setLanguageValue($prefix, $key, $languageId, $value)
    {
        $this->inlineValueService->setLanguageValue($prefix . $key, $languageId, $value);
    }

    public function setGlobalValue($prefix, $key, $value)
    {
        $this->inlineValueService->setGlobalValue($prefix . $key, $value);
    }

    // DELETE
    public function deletePageValue($prefix, $key, $pageId)
    {
        $this->inlineValueService->deletePageValue($prefix . $key, $pageId);
    }

    public function deleteLanguageValue($prefix, $key, $languageId)
    {
        $this->inlineValueService->deleteLanguageValue($prefix . $key, $languageId);
    }

    public function deleteGlobalValue($prefix, $key)
    {
        $this->inlineValueService->deleteGlobalValue($prefix . $key);
    }

    /**
     * Last get operation scope
     * @return \Ip\Internal\InlineValue\Entity\Scope
     */
    public function getLastOperationScope()
    {
        return $this->inlineValueService->getLastOperationScope();
    }

}

<?php
/**
 * @package ImpressPages

 *
 */

namespace Modules\developer\inline_management;


class Dao
{

    const MODULE_NAME = 'inline_management';
    const PREFIX_STRING = 'str_';
    const PREFIX_TEXT = 'txt_';
    const PREFIX_IMAGE = 'img_';
    const PREFIX_LOGO = 'logo_';

    public function __construct()
    {
        $this->inlineValueService = new \Modules\developer\inline_value\Service(self::MODULE_NAME);
    }


    // GET
    public function getValue($prefix, $key, $languageId, $zoneName, $pageId)
    {
        return $this->inlineValueService->getValue($prefix.$key, $languageId, $zoneName, $pageId);
    }

    public function getPageValue($prefix, $key, $languageId, $zoneName, $pageId)
    {
        return $this->inlineValueService->getPageValue($prefix.$key, $languageId, $zoneName, $pageId);
    }

    public function getLanguageValue($prefix, $key, $languageId)
    {
        return $this->inlineValueService->getLanguageValue($prefix.$key, $languageId);
    }

    public function getGlobalValue($prefix, $key)
    {
        return $this->inlineValueService->getGlobalValue($prefix.$key);
    }

    // SET
    public function setPageValue($prefix, $key, $languageId, $zoneName, $pageId, $value)
    {
        return $this->inlineValueService->setPageValue($prefix.$key, $languageId, $zoneName, $pageId, $value);
    }


    public function setLanguageValue($prefix, $key, $languageId, $value)
    {
        return $this->inlineValueService->setLanguageValue($prefix.$key, $languageId, $value);
    }

    public function setGlobalValue($prefix, $key, $value)
    {
        return $this->inlineValueService->setGlobalValue($prefix.$key, $value);
    }

    // DELETE
    public function deletePageValue($prefix, $key, $zoneName, $pageId)
    {
        $this->inlineValueService->deletePageValue($prefix.$key, $zoneName, $pageId);
    }

    public function deleteLanguageValue($prefix, $key, $languageId)
    {
        $this->inlineValueService->deleteLanguageValue($prefix.$key, $languageId);
    }

    public function deleteGlobalValue($prefix, $key)
    {
        $this->inlineValueService->deleteGlobalValue($prefix.$key);
    }

    /**
     * Last get operation scope
     * @return int
     */
    public function getLastOperationScope()
    {
        return $this->inlineValueService->getLastOperationScope();
    }

}
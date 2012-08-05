<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
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

    /**
     * @return Entity\Logo
     */
    public function getValueLogo()
    {
        $logoValue = $this->getValue(self::PREFIX_LOGO);
        $logo = new Entity\Logo($logoValue);
        return $logo;
    }

    public function getValueString($key)
    {
        return $this->getValue(self::PREFIX_STRING.$key);
    }


    public function getValueText($key)
    {
        return $this->getValue(self::PREFIX_TEXT.$key);
    }



    private function getValue($prefixedKey)
    {
        global $site;

        //Find value in breadcrumb
        $zoneName = $site->getCurrentZone()->getName();
        $breadcrumb = $site->getBreadcrumb();
        array_reverse($breadcrumb);

        foreach ($breadcrumb as $key => $element) {
            $value = $this->inlineValueService->getPageValue($prefixedKey, $zoneName, $element->getId());
            if ($value !== false) {
                if ($key == 0) {
                    $type = Value::TYPE_PAGE;
                } else {
                    $type = Value::TYPE_PARENT_PAGE;
                }
                return new Value($type, $value);
            }
        }

        //Find language value
        $value = $this->inlineValueService->getLanguageValue($prefixedKey, $site->getCurrentLanguage()->getId());
        if ($value !== false) {
            return new Value(Value::TYPE_LANGUAGE, $value);
        }

        //Find global value
        $value = $this->inlineValueService->getGlobalValue($prefixedKey);
        if ($value !== false) {
            return new Value(Value::TYPE_GLOBAL, $value);
        }

        return false;
    }

    // SET

    public function setValueLogo(Entity\Logo $value)
    {
        return $this->setValue(self::PREFIX_LOGO, $value->getValueStr());
    }

    public function setValueString($key, Value $value)
    {
        return $this->getValue(self::PREFIX_STRING.$key, $value);
    }


    public function setValueText($key, Value $value)
    {
        return $this->getValue(self::PREFIX_TEXT.$key, $value);
    }


    /**
     * @param $prefixedKey
     * @param Value $value
     */
    private function setValueGlobal($prefixedKey, Value $value)
    {
        switch($value->getType) {
            case Value::TYPE_GLOBAL:
                $this->inlineValueService->setGlobalValue($prefixedKey, $value);
                break;
            case Value::TYPE_LANGUAGE:
                $this->inlineValueService->setLanguageValue($prefixedKey, $languageId, $value);
                break;
            case Value::TYPE_PARENT_PAGE:
                $this->inlineValueService->setPageValue($prefixedKey, $zoneName, $pageId, $value);
                break;
            case Value::TYPE_PAGE:
                $this->inlineValueService->setPageValue($prefixedKey, $zoneName, $pageId, $value);
                break;
        }
    }




}
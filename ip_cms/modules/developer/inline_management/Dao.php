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

    public function __construct()
    {
        $this->inlineValueService = new \Modules\developer\inline_value\Service(self::MODULE_NAME);
    }

    public function getCurrentValueString($key)
    {
        return $this->getCurrentValue(self::PREFIX_STRING.$key);
    }


    public function getCurrentValueText($key)
    {
        return $this->getCurrentValue(self::PREFIX_TEXT.$key);
    }

    public function getCurrentValueGlobal($key)
    {
        return $this->getCurrentValue(self::PREFIX_GLOBAL.$key);
    }

    private function getCurrentValue($prefixedKey)
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

}
<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Internal\Content;


/**
 *
 * Event dispatcher class
 *
 */
class Helper
{

    /**
     * @param $data
     * @return Language
     */
    public static function createLanguage($data)
    {
        $language = new \Ip\Language($data['id'], $data['code'], $data['url'], $data['title'], $data['abbreviation'], $data['isVisible'], $data['textDirection']);
        return $language;
    }

    public static function createZone($zoneData)
    {
        if ($zoneData['associated_module']) {
            $class = '\\Ip\\Internal\\' . $zoneData['associated_module'] . '\\Zone';
            if (class_exists($class)) {
                $zoneObject = new $class($zoneData);
            } else {
                $class = '\\Plugin\\' . $zoneData['associated_module'] . '\\Zone';
                $zoneObject = new $class($zoneData);
            }
        } else {
            $zoneObject = new \Ip\DefaultZone($zoneData);
        }

        $zoneObject->setId($zoneData['id']);
        $zoneObject->setName($zoneData['name']);
        $zoneObject->setLayout($zoneData['template']);
        $zoneObject->setTitle($zoneData['title']);
        $zoneObject->setUrl($zoneData['url']);
        $zoneObject->setKeywords($zoneData['keywords']);
        $zoneObject->setDescription($zoneData['description']);
        $zoneObject->setAssociatedModule($zoneData['associated_module']);
        return $zoneObject;
    }
}

<?php

/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Internal\Pages;




class ModelTree {

    private static $websiteId = 0;

    public static function getWebsites () {
        $answer = array();

        $answer[] = array(
            'id' => self::$websiteId,
            'title' => ipConfig()->baseUrl()
        );

        $remotes = Remotes::getRemotes();

        
        foreach($remotes as $key => $remote) {
            $answer[] = array(
                'id' => $key + 1,
                'title' => $remote['url']
            );
        }

        return $answer;
    }


    public static function getLanguages () {
        $languages = Db::getLanguages();

        $answer = array();

        foreach ($languages as $languageKey => $language) {
            $answer[] = array(
                'id' => $language['id'],
                'title' => $language['d_short']
            );

        }

        return $answer;
    }


    public static function getZones($includeNonManagedZones) {
        $zones = ipContent()->getZones();

        $managedZones = array();
        foreach ($zones as $zone) {
            $managedZones[] = $zone->getName();
        }

        $answer = array();
        foreach ($zones as $zone) {
            if ($includeNonManagedZones || in_array($zone->getName(), $managedZones)) {

                $answer[] = array (
                    'id' =>  $zone->getName(),
                    'title' => $zone->getTitle()
                );
                
            }
        }

        return $answer;
    }


    public static function getZonePages ($languageId, $zoneName) {
        $zone = ipContent()->getZone($zoneName);
        
        if ( ! $zone) {
            trigger_error('Can\'t find zone ' . $zoneName);
            return false;
        }  
        $rootElementId = Db::rootId($zone->getId(), $languageId);


        if($rootElementId == null) { /*try to create*/
            Db::createRootZoneElement($zone->getId(), $languageId);
            $rootElementId = Db::rootId($zone->getId(), $languageId);
            if($rootElementId === false) {	/*fail to create*/
                trigger_error("Can't create root zone element.");
                return false;
            }
        }
        $answer = self::getPages($rootElementId);

        return $answer;
    }



    public static function getPages ($parentId) {
        $answer = array();
        
        $children = Db::pageChildren($parentId);
        
        foreach($children as $childKey => $child) {
            $answer[] = array (
                'id' =>  $child['id'],
                'title' => $child['button_title'],
                'visible' => $child['visible']
            );            
        }
        
        return $answer;
    }

}

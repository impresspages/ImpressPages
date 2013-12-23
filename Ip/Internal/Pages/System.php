<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal\Pages;


class System
{


    function init()
    {

        $dispatcher = ipDispatcher();

        $dispatcher->addEventListener('Ip.addLanguage', __CLASS__ . '::addLanguage');
    }


    public static function addLanguage($data)
    {
        $languageId = $data['id'];
        //todox check if root zone element is being created on demand and remove this code
//
//        $zones = ipContent()->getZones();
//        foreach ($zones as $zone) {
//            if ($zone->getAssociatedModule() == 'Content') {
//                Db::createRootZoneElement($zone->getId(), $languageId);
//            }
//        }


        Model::createZoneParameters($languageId);
    }
}

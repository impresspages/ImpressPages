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

        $dispatcher->addEventListener('Ip.addLanguage', array($this, 'onAddLanguage'));
        $dispatcher->addEventListener('Ip.deleteLanguage', array($this, 'onDeleteLanguage'));
    }


    public function onAddLanguage($data)
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


        Model::createParametersLanguage($languageId);
    }

    public function onDeleteLanguage($data)
    {
        $languageId = $data['id'];
        Model::cleanupLanguage($languageId);
    }
}

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
        $dispatcher->addEventListener('Ip.deleteZone', array($this, 'onDeleteZone'));

    }


    public function onAddLanguage($data)
    {
        $languageId = $data['id'];
        Model::createParametersLanguage($languageId);
    }

    public function onDeleteLanguage($data)
    {
        $languageId = $data['id'];
        Model::cleanupLanguage($languageId);
    }

    public function onDeleteZone($data)
    {
        $zoneId = $data['id'];
        Model::removeZonePages($zoneId);
        ipDb()->delete('zone_to_page', array('zone_id' => $zoneId));
    }
}

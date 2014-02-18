<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal\Pages;


class Event
{

    public static function ipLanguageAdded($data)
    {
        $languageId = $data['id'];

        // TODOXX #ipLanguageAdded_createMenu
    }

    public static function ipBeforeLanguageDeleted($data)
    {
        $languageId = $data['id'];
        Model::cleanupLanguage($languageId);
    }

    public static function ipBeforeZoneDeleted($data)
    {
        $zoneId = $data['id'];
        Model::removeZonePages($zoneId);
        ipDb()->delete('zone_to_page', array('zone_id' => $zoneId));
    }
}

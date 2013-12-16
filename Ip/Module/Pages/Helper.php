<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Module\Pages;





class Helper
{

    public static function languageList()
    {
        $answer = array();
        $languages = ipContent()->getLanguages();
        foreach($languages as $language)
        {
            $answer[] = array(
                'id' => $language->getId(),
                'title' => $language->getTitle(),
                'abbreviation' => $language->getAbbreviation()
            );
        }
        return $answer;
    }

    public static function zoneList()
    {
        $answer = array();
        $zones = ipContent()->getZones();
        foreach($zones as $zone)
        {
            $answer[] = array(
                'name' => $zone->getName(),
                'title' => $zone->getTitle()
            );
        }
        return $answer;
    }

    public static function pagePropertiesForm($zoneName, $pageId)
    {
        $zone = ipContent()->getZone($zoneName);
        $page = $zone->getPage($pageId);

        $form = new \Ip\Form();

        $field = new \Modules\developer\form\Field\Text(
            array(
                'name' => 'firstField', //html "name" attribute
                'label' => 'First field', //field label that will be displayed next to input field
            ));
        $form->addField($field);

        return ;
    }


}
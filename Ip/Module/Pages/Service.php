<?php
/**
 * @package   ImpressPages
 *
 *
 */

namespace Ip\Module\Pages;


//TODOX review
class Service
{
    public static function addZone($title, $zoneName, $associatedModule, $defaultLayout, $associatedGroup = '', $description = '', $url = '') {

        $content = new \Ip\Content();

        if ($content->getZone($zoneName)){
            throw new \Ip\CoreException("Zone '".$zoneName."' already exists.");
        }

        $rowNumber =  self::getLastRowNumber();

        $languages = Db::getLanguages();

        $row = array(
            'name' => $zoneName,
            'template' => $defaultLayout,
            'translation' => $title,
            'associated_module' => $associatedModule,
            'associated_group' => $associatedGroup,
            'row_number' => ++$rowNumber
        );

        $zoneId = ipDb()->insert(ipDb()->tablePrefix() . 'zone', $row);


        foreach($languages as $language){

            $language_id = $language['id'];

            $row = array(
                'visible' => 1
            );

            $element_id = ipDb()->insert(ipDb()->tablePrefix() . 'content_element', $row);

            $row = array(
                'language_id' => $language_id,
                'zone_id' => $zoneId,
                'element_id' => $element_id
            );

            ipDb()->insert(ipDb()->tablePrefix() . 'zone_to_content', $row);

            $row = array(
                'title' => $title,
                'language_id' => $language_id,
                'zone_id' => $zoneId,
                'url' => \Ip\Module\Languages\Db::newUrl($language_id, $url)
            );

            ipDb()->insert(ipDb()->tablePrefix() . 'zone_parameter', $row);
            //TODOX catch zone event and creaet root zone element in content module

        }


        ipContent()->invalidateZones();

        return $zoneId;
    }

    //TODOX move to Content module
    public static function createRootZoneElement($language) {
        $firstLanguage = \Ip\Internal\ContentDb::getFirstLanguage();
        $zones = \Ip\Internal\ContentDb::getZones($firstLanguage['id']);
        foreach($zones as $key => $zone) {
            $sql2 = "insert into `".DB_PREF."zone_parameter` set
        language_id = '".ip_deprecated_mysql_real_escape_string($language)."',
        zone_id = '".$zone['id']."',
        title = '".ip_deprecated_mysql_real_escape_string(Db::newUrl($language, $zone['title']))."',
        url = '".ip_deprecated_mysql_real_escape_string(Db::newUrl($language, $zone['url']))."'";
            $rs2 = ip_deprecated_mysql_query($sql2);
            if(!$rs2)
                trigger_error($sql2." ".ip_deprecated_mysql_error());
        }
    }

    public static function getLastRowNumber()
    {

        ipDb()->tablePrefix();

        $sql = "select MAX(row_number) as max_row from ".ipDb()->tablePrefix()."zone";

        $val= ipDb()->fetchValue($sql);

        return $val;
    }


}
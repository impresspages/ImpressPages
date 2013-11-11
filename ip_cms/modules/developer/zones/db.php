<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Modules\developer\zones;


require_once \Ip\Config::libraryFile('php/text/transliteration.php');

class Db{

    public static function getLanguages(){
        $answer = array();
        $sql = "select * from `".DB_PREF."language` where 1 order by row_number";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs){
            while($lock = ip_deprecated_mysql_fetch_assoc($rs))
            $answer[] = $lock;
        }else{
            trigger_error($sql." ".ip_deprecated_mysql_error());
        }
        return $answer;
    }
    
    public static function addZone($associatedGroup, $associatedModule, $template, $name, $translation) {
        
        $sql = "
            SELECT 
                max(row_number) as max_row_number
            FROM 
                `".DB_PREF."zone`
            WHERE
                1
        ";
        $rs = ip_deprecated_mysql_query($sql);
        if (!$rs) {
            trigger_error($sql.' '.ip_deprecated_mysql_error());
        }
        $lock = ip_deprecated_mysql_fetch_assoc($rs);
        $maxRowNumber = $lock['max_row_number'];
        
        $sql = "
            INSERT INTO 
                `".DB_PREF."zone`
            SET
                `associated_group` = '".ip_deprecated_mysql_real_escape_string($associatedGroup)."',
                `associated_module` = '".ip_deprecated_mysql_real_escape_string($associatedModule)."',
                `translation` = '".ip_deprecated_mysql_real_escape_string($translation)."',
                `template` = '".ip_deprecated_mysql_real_escape_string($template)."',
                `name` = '".ip_deprecated_mysql_real_escape_string($name)."',
                `row_number` = '".($maxRowNumber + 1)."'
        ";
        $rs = ip_deprecated_mysql_query($sql);
        if (!$rs) {
            trigger_error($sql.' '.ip_deprecated_mysql_error());
        } 

        self::afterInsert(ip_deprecated_mysql_insert_id());
    }
    


    public static function getZone($zoneId){
        $sql = "select * from `".DB_PREF."zone` where id = '".$zoneId."'";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs){
            if($lock = ip_deprecated_mysql_fetch_assoc($rs))
            return $lock;
            else
            return false;
        }else{
            trigger_error($sql." ".ip_deprecated_mysql_error());
            return false;
        }
         

    }

    public static function deleteParameters($zoneId){
        $sql = "delete from `".DB_PREF."zone_parameter` where `zone_id` = '".$zoneId."'";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs){
        }else{
            trigger_error($sql." ".ip_deprecated_mysql_error());
            return false;
        }
    }

    public static function createRootzonesElement($zoneId, $translation = ''){
        $languages = Db::getLanguages();
        $zone = Db::getZone($zoneId);

        foreach($languages as $key => $language){
            $sql = "insert into `".DB_PREF."content_element` set `visible` = 1";
            $rs = ip_deprecated_mysql_query($sql);
            if($rs){
                $sql2 = "insert into `".DB_PREF."zone_to_content` set
        `language_id` = '".ip_deprecated_mysql_real_escape_string($language['id'])."',
        `zone_id` = '".ip_deprecated_mysql_real_escape_string($zoneId)."',
        `element_id` = '".ip_deprecated_mysql_insert_id()."'";
                $rs2 = ip_deprecated_mysql_query($sql2);
                if(!$rs2)
                trigger_error($sql2." ".ip_deprecated_mysql_error());

                $sql2 = "insert into `".DB_PREF."zone_parameter` set
        `title` = '".ip_deprecated_mysql_real_escape_string($translation)."',
        `language_id` = '".ip_deprecated_mysql_real_escape_string($language['id'])."',
        `zone_id` = '".$zoneId."',
        `url` = '".ip_deprecated_mysql_real_escape_string(Db::newUrl($language['id'], $zone['translation']))."'";
                $rs2 = ip_deprecated_mysql_query($sql2);
                if(!$rs2)
                trigger_error($sql2." ".ip_deprecated_mysql_error());
            }else{
                trigger_error($sql." ".ip_deprecated_mysql_error());
            }
        }
    }

    public static function newUrl($language, $title){
        $url = mb_strtolower($title);
        $url = \Library\Php\Text\Transliteration::transform($url);
        $url = str_replace(" ", "-", $url);
        $url = str_replace("/", "-", $url);
        $url = str_replace("\\", "-", $url);
        $url = str_replace("\"", "-", $url);
        $url = str_replace("\'", "-", $url);
        $url = str_replace("„", "-", $url);
        $url = str_replace("“", "-", $url);
        $url = str_replace("&", "-", $url);
        $url = str_replace("%", "-", $url);
        $url = str_replace("`", "-", $url);
        $url = str_replace("!", "-", $url);
        $url = str_replace("@", "-", $url);
        $url = str_replace("#", "-", $url);
        $url = str_replace("$", "-", $url);
        $url = str_replace("^", "-", $url);
        $url = str_replace("*", "-", $url);
        $url = str_replace("(", "-", $url);
        $url = str_replace(")", "-", $url);
        $url = str_replace("{", "-", $url);
        $url = str_replace("}", "-", $url);
        $url = str_replace("[", "-", $url);
        $url = str_replace("]", "-", $url);
        $url = str_replace("|", "-", $url);
        $url = str_replace("~", "-", $url);


        $sql = "select url from `".DB_PREF."zone_parameter` where `language_id` = '".ip_deprecated_mysql_real_escape_string($language)."' ";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs){
            $urls = array();
            while($lock = ip_deprecated_mysql_fetch_assoc($rs))
            $urls[$lock['url']] = 1;

            $i = '';
            if(isset($urls[$url])){
                while(isset($urls[$url.$i])){
                    if($i == '')
                    $i = 1;
                    else
                    $i++;
                }
            }
            return $url.$i;
        }else
        trigger_error("Can't get all urls ".$sql." ");
    }

    public static function getDefaultTemplate() {
        $availableTemplates = self::getAvailableTemplates();
        if (in_array('main.php', $availableTemplates)) {
            return 'main.php';
        }
        
        if (count($availableTemplates) == 0) {
            return false;
        } 
        
        return $availableTemplate[0];
    }
    
    /**
     * 
     * Checks if name is not used in other zone. If it is, changes the name and returns changed value.
     * @param unknown_type $name
     */
    public static function getUniqueName($name) {
        global $site;
        $modifiedName = $name;
        $zone = $site->getZone($name);
        $count = 0;
        while ($zone) {
            $count++;
            $modifiedName = $name.$count;
            $zone = $site->getZone($modifiedName); 
        }
        return $modifiedName;
    }
    
    public static function getAvailableTemplates(){
        $answer = array();
        $themeDir = \Ip\Config::themeFile('');
        if (is_dir($themeDir)) {
            $handle = opendir($themeDir);
            if ($handle !== false){
                while (false !== ($file = readdir($handle))) {
                    if (strtolower(substr($file, -4, 4)) == '.php' && is_file($themeDir . '/' . $file) && $file != '..' && $file != '.') {
                        $answer[$file] = $file;
                    }
                }
                return $answer;
            }
        }
    }    
    
    
    public static function afterInsert($id) {
        global $parametersMod;
        global $site;
        
        $zone = Db::getZone($id);
        
        Db::createRootZonesElement($id, $zone['translation']);
        
        if($zone['associated_group'] == 'standard' && $zone['associated_module'] == 'content_management'){
            /* add menu management associated zones */
            $newZonesStr = self::addToAssociatedZones($parametersMod->getValue('Pages.associated_zones'), $zone['name']);
            $parametersMod->setValue('Pages.associated_zones', $newZonesStr);
        
            $newZonesStr = self::addToAssociatedZones($parametersMod->getValue('Search.searchable_zones'), $zone['name']);
            $parametersMod->setValue('Search.searchable_zones', $newZonesStr);
        
            $newZonesStr = self::addToAssociatedZones($parametersMod->getValue('administrator', 'sitemap', 'options', 'associated_zones'), $zone['name']);
            $parametersMod->setValue('administrator', 'sitemap', 'options', 'associated_zones', $newZonesStr);
        }
        $newZonesStr = self::addToAssociatedZones($parametersMod->getValue('Config.xml_sitemap_associated_zones'), $zone['name']);
        $parametersMod->setValue('Config.xml_sitemap_associated_zones', $newZonesStr);
        
        $site->dispatchEvent('developer', 'zones', 'zone_created', array('zone_id'=>$id));
        
    }

    
    public static function addToAssociatedZones($currentValue, $newZone, $depth = null){
        $associatedZonesStr = $currentValue;
        $associatedZones = explode("\n", $associatedZonesStr);
        $found = false;
        foreach($associatedZones as $key => $value){
            if(self::getModuleKey($value) == $newZone)
            $found = true;
        }
        if(!$found){
            if($associatedZonesStr == '')
            $associatedZonesStr = self::makeZoneStr($newZone, $depth);
            else
            $associatedZonesStr .= "\n".self::makeZoneStr($newZone, $depth);
            return $associatedZonesStr;
    
        }
    }
    
    public static function getModuleKey($str){
        $begin = strrpos($str, '[');
        $end =  strrpos($str, ']');
        if($begin !== false && $end === strlen($str) - 1)
        return substr($str, 0, $begin);
        else
        return $str;
    }    
    
    public static function makeZoneStr($zoneName, $depth = null){
        if($depth !== null)
        return $zoneName.'['.$depth.']';
        else
        return $zoneName;
    }    
}




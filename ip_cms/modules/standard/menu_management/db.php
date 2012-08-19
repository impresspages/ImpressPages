<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Modules\standard\menu_management;


if (!defined('FRONTEND')&&!defined('BACKEND')) exit;



class Db {

    /**
     * Get all languages
     * @return array
     */
    public static function getLanguages () {
        require_once (BASE_DIR.MODULE_DIR.'standard/languages/db.php');
        $languages = \Modules\standard\languages\Db::getLanguages();
        return $languages;
    }

    /**
     * Get zones that are associated with menu management
     */
    public static function getZones () {


        global $parametersMod;

        $managedZones = explode("\n",$parametersMod->getValue('standard', 'menu_management', 'options', 'associated_zones'));
        $sqlZonesArray = "'".implode("','",$managedZones)."'";



        $dbZones = array();
        $sql = "select z.name, z.translation, z.id, p.url, p.description, p.keywords, p.title from `".DB_PREF."zone` z, `".DB_PREF."zone_parameter` p where p.zone_id = z.id and z.name in (".$sqlZonesArray.") order by z.row_number  ";
        $rs = mysql_query($sql);
        if($rs){
            while($lock = mysql_fetch_assoc($rs)){
                $dbZones[$lock['name']] = $lock;
            }
        }else trigger_error($sql." ".mysql_error());

        $answer = array();
        foreach($managedZones as $key => &$zone){
            if(isset($dbZones[$zone])){
                $answer[$zone] = $dbZones[$zone];
            }
        }



        return $answer;
    }

    public static function languageByRootElement($element_id){ //returns root element of menu
        $sql = "select mte.language_id from `".DB_PREF."zone_to_content` mte where  mte.element_id = '".(int)$element_id."'";
        $rs = mysql_query($sql);
        if($rs){
            if($lock = mysql_fetch_assoc($rs)){
                return $lock['language_id'];
            }
        }else
    

    
        trigger_error("Can't find zone element ".$sql." ".mysql_error());
    }

    public static function getAutoRssZones() {
        global $parametersMod;
        
        $answer = explode("\n",$parametersMod->getValue('standard', 'menu_management', 'options', 'auto_rss_zones'));
        return $answer;
    }


    /**
     *
     * returns
     * @param int $pageId
     * @param int $languageId
     * @return array root element of content
     */
    public static function rootContentElement($zoneId, $languageId){
        $sql = "select mte.element_id from `".DB_PREF."zone_to_content` mte, `".DB_PREF."language` l where l.id = '".$languageId."' and  mte.language_id = l.id and zone_id = '".$zoneId."' ";
        $rs = mysql_query($sql);
        if ($rs) {
            if ($lock = mysql_fetch_assoc($rs)) {
                return $lock['element_id'];
            } else { //try to create
                self::createRootZoneElement($zoneId, $languageId);
                $rs2 = mysql_query($sql);
                if ($rs2) {
                    if ($lock2 = mysql_fetch_assoc($rs2)) {
                        return $lock2['element_id'];
                    } else { //try to create
                        return false;
                    }
                }
                return false;
            }
        } else {
            trigger_error("Can't find zone element ".$sql." ".mysql_error());
            return false;
        }
    }

    /**
     *
     * Create root zone element
     * @param int $zoneId
     * @param int $languageId
     */
    public static function createRootZoneElement($zoneId, $languageId){
        $sql = "insert into `".DB_PREF."content_element` set visible = 1";
        $rs = mysql_query($sql);
        if($rs){
            $elementId = mysql_insert_id();
            $sql2 = "insert into `".DB_PREF."zone_to_content` set
            language_id = '".mysql_real_escape_string($languageId)."',
            zone_id = '".$zoneId."',
			element_id = '".$elementId."'";
            $rs2 = mysql_query($sql2);
            if(!$rs2) {
                trigger_error($sql2." ".mysql_error());
            }
        }
    }


    /**
     *
     * Get page children
     * @param int $elementId
     * @return array
     */
    public static function pageChildren($parentId){
        $sql = "select * from `".DB_PREF."content_element` where parent= '".$parentId."' order by row_number";
        $rs = mysql_query($sql);
        if($rs){
            $pages = array();
            while($lock = mysql_fetch_assoc($rs)){
                $pages[] = $lock;
            }
            return $pages;
        } else {
            trigger_error("Can't get children ".$sql." ".mysql_error());
        }
    }

    /**
     *
     * Get page
     * @param int $id
     * @return array
     */
    public static function getPage($id){
        $sql = "select * from `".DB_PREF."content_element` where id= '".$id."' ";
        $rs = mysql_query($sql);
        if($rs){
            if($lock = mysql_fetch_assoc($rs)){
                return $lock;
            }
        } else {
            trigger_error("Can't get children ".$sql." ".mysql_error());
        }
        return false;
    }


    /**
     *
     * Update page
     * @param int $pageId
     * @param array $params
     */
    public static function updatePage($zoneName, $pageId, $params){
        global $site;
        $values = array();

        $zone = $site->getZone($zoneName);
        if (!$zone) {
            return;
        }
        
        $oldPage = $zone->getElement($pageId);
        $oldUrl = $oldPage->getLink(true);
        
        if (isset($params['buttonTitle']))
        $values[] = 'button_title = \''.mysql_real_escape_string($params['buttonTitle']).'\'';

        if (isset($params['pageTitle']))
        $values[] =  'page_title = \''.mysql_real_escape_string($params['pageTitle']).'\'';

        if (isset($params['keywords']))
        $values[] =  'keywords = \''.mysql_real_escape_string($params['keywords']).'\'';

        if (isset($params['description']))
        $values[] =  'description = \''.mysql_real_escape_string($params['description']).'\'';

        if (isset($params['url'])){
            if ($params['url'] == '') {
                if (isset($params['pageTitle']) && $params['pageTitle'] != '') {
                    $params['url'] = self::makeUrl($params['pageTitle'], $pageId);
                } else {
                    if (isset($params['buttonTitle']) && $params['buttonTitle'] != '') {
                        $params['url'] = self::makeUrl($params['buttonTitle'], $pageId);
                    } else {
                        $params['url'] = self::makeUrl('page', $pageId);
                    }
                }
            } else {
                $tmpUrl = str_replace("/", "-", $params['url']);
                $i = 1;
                while (!self::availableUrl($tmpUrl, $pageId)) {
                    $tmpUrl = $params['url'].'-'.$i;
                    $i++;
                }
                $params['url'] = $tmpUrl;
            }
            $values[] =  'url= \''.mysql_real_escape_string($params['url']).'\'';
        }

        if (isset($params['createdOn']) && strtotime($params['createdOn']) !== false)
        $values[] =  'created_on = \''.mysql_real_escape_string($params['createdOn']).'\'';

        if (isset($params['lastModified']) && strtotime($params['lastModified']) !== false)
        $values[] =  'last_modified= \''.mysql_real_escape_string($params['lastModified']).'\'';

        if (isset($params['type']))
        $values[] =  'type = \''.mysql_real_escape_string($params['type']).'\'';

        if (isset($params['redirectURL']))
        $values[] =  'redirect_url = \''.mysql_real_escape_string($params['redirectURL']).'\'';

        if (isset($params['visible']))
        $values[] =  'visible = \''.mysql_real_escape_string($params['visible']).'\'';

        if (isset($params['rss']))
        $values[] =  'rss = \''.mysql_real_escape_string($params['rss']).'\'';

        if (isset($params['parentId']))
        $values[] =  'parent = \''.mysql_real_escape_string($params['parentId']).'\'';

        if (isset($params['rowNumber']))
        $values[] =  'row_number = \''.mysql_real_escape_string($params['rowNumber']).'\'';

        if (isset($params['cached_html']))
        $values[] =  '`cached_html` = \''.mysql_real_escape_string($params['cached_html']).'\'';

        if (isset($params['cached_text']))
        $values[] =  '`cached_text` = \''.mysql_real_escape_string($params['cached_text']).'\'';

        if (count($values) == 0) {
            return true; //nothing to update.
        }
        
        $sql = 'UPDATE `'.DB_PREF.'content_element` SET '.implode(', ', $values).' WHERE `id` = '.(int)$pageId.' ';
        $rs = mysql_query($sql);
        if ($rs) {
            
            if(isset($params['url']) && $oldPage->getUrl() != $params['url']){
                $newPage = $zone->getElement($pageId);
                $newUrl = $newPage->getLink(true);
                global $dispatcher;
                $dispatcher->notify(new \Ip\Event\UrlChanged(null, $oldUrl, $newUrl));
            }
            
            
            return true;
        } else {
            trigger_error($sql.' '.mysql_error());
            return false;
        }
    }

    /**
     *
     * Insert new page
     * @param int $parentId
     * @param array $params
     */
    public static function insertPage($parentId, $params){
        $values = '';

        $values .= ' parent = '.(int)$parentId;
        $values .= ', row_number = '.((int)self::getMaxIndex($parentId) + 1);

        if (isset($params['button_title'])) {
            $params['buttonTitle'] = $params['button_title'];
        }
        if (isset($params['page_title'])) {
            $params['pageTitle'] = $params['page_title'];
        }
        if (isset($params['redirect_url'])) {
            $params['redirectURL'] = $params['redirect_url'];
        }

        if (isset($params['buttonTitle']))
        $values .= ', button_title = \''.mysql_real_escape_string($params['buttonTitle']).'\'';

        if (isset($params['pageTitle']))
        $values .= ', page_title = \''.mysql_real_escape_string($params['pageTitle']).'\'';

        if (isset($params['keywords']))
        $values .= ', keywords = \''.mysql_real_escape_string($params['keywords']).'\'';

        if (isset($params['description']))
        $values .= ', description = \''.mysql_real_escape_string($params['description']).'\'';

        if (isset($params['url']))
        $values .= ', url= \''.mysql_real_escape_string($params['url']).'\'';

        if (isset($params['createdOn'])) {
            $values .= ', created_on = \''.mysql_real_escape_string($params['createdOn']).'\'';
        } else {
            $values .= ', created_on = \''.date('Y-m-d').'\'';
        }

        if (isset($params['lastModified'])) {
            $values .= ', last_modified= \''.mysql_real_escape_string($params['lastModified']).'\'';
        } else {
            $values .= ', last_modified= \''.date('Y-m-d').'\'';
        }

        if (isset($params['type']))
        $values .= ', type = \''.mysql_real_escape_string($params['type']).'\'';

        if (isset($params['redirectURL']))
        $values .= ', redirect_url = \''.mysql_real_escape_string($params['redirectURL']).'\'';

        if (isset($params['visible']))
        $values .= ', visible = \''.mysql_real_escape_string((int)$params['visible']).'\'';

        if (isset($params['rss']))
        $values .= ', rss = \''.mysql_real_escape_string((int)$params['rss']).'\'';

        if (isset($params['cached_html']))
        $values .= ', `cached_html` = \''.mysql_real_escape_string($params['cached_html']).'\'';

        if (isset($params['rowNumber']))
        $values .= ', `cached_text` = \''.mysql_real_escape_string($params['cached_text']).'\'';

        
        
        $sql = 'INSERT INTO `'.DB_PREF.'content_element` SET '.$values.' ';
        $rs = mysql_query($sql);
        if ($rs) {
            return mysql_insert_id();
        } else {
            trigger_error($sql.' '.mysql_error());
            return false;
        }
    }


    public static function getMaxIndex($parentId) {
        $sql = "SELECT MAX(`row_number`) AS 'max_row_number' FROM `".DB_PREF."content_element` WHERE `parent` = ".(int)$parentId." ";
        $rs = mysql_query($sql);
        if ($rs) {
            if ($lock = mysql_fetch_assoc($rs)) {
                return $lock['max_row_number'];
            } else {
                return false;
            }
        } else {
            trigger_error($sql.' '.mysql_error());
            return false;
        }
    }




    /**
     *
     * Delete menu element record
     * @param int $id
     */
    public static function deletePage($id) {
        global $globalWorker;
        $sql = "delete from `".DB_PREF."content_element` where id = '".$id."' ";
        if (!mysql_query($sql))
        $globalWorker->set_error("Can't delete element ".$sql." ".mysql_error());

    }


    public static function copyPage($nodeId, $newParentId, $newIndex){
        $sql = "select * from `".DB_PREF."content_element` where `id` = ".(int)$nodeId." ";
        $rs = mysql_query($sql);
        if($rs){
            if($lock = mysql_fetch_assoc($rs)){
                $sql2 = "insert into `".DB_PREF."content_element` set `parent` = ".(int)$newParentId.", `row_number` = ".(int)$newIndex." ";
                 
                foreach($lock as $key => $value){
                    switch($key){
                        case 'button_title':
                            $sql2 .= ", `button_title` = '".mysql_real_escape_string($lock['button_title'])."'";
                            break;
                        case 'id':
                             
                            break;
                        case 'parent':
                             
                            break;
                        case 'row_number':
                            break;
                        case 'url':
                            $sql2 .= ", `url` = '".mysql_real_escape_string(self::ensureUniqueUrl($value))."'";
                            break;
                        default:
                            if($value === null){
                                $sql2 .= ", `".mysql_real_escape_string($key)."` = NULL ";
                            } else {
                                $sql2 .= ", `".mysql_real_escape_string($key)."` = '".mysql_real_escape_string($value)."' ";
                            }
                            break;
                    }
                }
                $rs2 = mysql_query($sql2);
                if($rs2){
                    return mysql_insert_id();
                } else {
                    trigger_error($sql2.' '.mysql_error());
                }
                 
            } else {
                trigger_error("Element does not exist");
            }
        } else {
            trigger_error($sql.' '.mysql_error());
        }
    }


    /**
     * @param string $url
     * @param int $allowed_id
     * @returns bool true if url is available ignoring $allowed_id page.
     */
    public static function availableUrl($url, $allowedId = null){
        if($allowedId)
        $sql = "select url from `".DB_PREF."content_element` where url = '".mysql_real_escape_string($url)."' and id <> '".$allowedId."'";
        else
        $sql = "select url from `".DB_PREF."content_element` where url = '".mysql_real_escape_string($url)."' ";

        $rs = mysql_query($sql);
        if(!$rs)
        trigger_error("Available url check ".$sql." ".mysql_error());

        if(mysql_num_rows($rs) > 0)
        return false;
        else
        return true;
    }
    
    



    /**
     *
     * Create unique URL
     * @param string $url
     * @param int $allowed_id
     */
    public static function makeUrl($url, $allowed_id = null){
        require_once(BASE_DIR.LIBRARY_DIR.'php/text/transliteration.php');
        if($url == '')
        $url = 'page';
        $url = mb_strtolower($url);
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
        $url = str_replace(".", "-", $url);
        $url = str_replace("'", "", $url);
        $url = str_replace("?", "", $url);
        $url = str_replace(":", "", $url);
        $url = str_replace(";", "", $url);

        if($url == ''){
            $url = '-';
        }


        while($url != str_replace("--", "-", $url))
        $url = str_replace("--", "-", $url);

        if(self::availableUrl($url, $allowed_id))
        return $url;

        $i = 1;
        while(!self::availableUrl($url.'-'.$i, $allowed_id)){
            $i++;
        }

        return $url.'-'.$i;
    }
    
    
    
    public static function ensureUniqueUrl($url, $allowedId = null) {
        $url = str_replace("/", "-", $url);
        
        if(self::availableUrl($url, $allowedId))
          return $url;
        
        $i = 1;
        while(!self::availableUrl($url.'-'.$i, $allowedId)) {
          $i++;
        }
        
        return $url.'-'.$i;
    }    

}
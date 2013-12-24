<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Internal\Pages;





class Db {

//    /**
//     * Get all languages
//     * @return array
//     */
//    public static function getLanguages () {
//        $languages = \Ip\Internal\Languages\Db::getLanguages();
//        return $languages;
//    }
//
//    /**
//     * Get zones that are associated with menu management
//     */
//    public static function getZones () {
//
//
//
//        $managedZones = array();
//        $zones = ipContent()->getZones();
//        foreach ($zones as $zone) {
//            $managedZones[] = $zone->getName();
//        }
//        $sqlZonesArray = "'".implode("','",$managedZones)."'";
//
//
//
//        $dbZones = array();
//        $sql = "select z.name, z.translation, z.id, p.url, p.description, p.keywords, p.title from `".DB_PREF."zone` z, `".DB_PREF."zone_parameter` p where p.zone_id = z.id and z.name in (".$sqlZonesArray.") order by z.row_number  ";
//        $rs = ip_deprecated_mysql_query($sql);
//        if ($rs) {
//            while ($lock = ip_deprecated_mysql_fetch_assoc($rs)) {
//                $dbZones[$lock['name']] = $lock;
//            }
//        } else {
//            trigger_error($sql." ".ip_deprecated_mysql_error());
//        }
//
//        $answer = array();
//        foreach ($managedZones as $key => &$zone) {
//            if (isset($dbZones[$zone])) {
//                $answer[$zone] = $dbZones[$zone];
//            }
//        }
//
//
//
//        return $answer;
//    }
//
//    public static function languageByRootElement($element_id){ //returns root element of menu
//        $sql = "select mte.language_id from `".DB_PREF."zone_to_content` mte where  mte.element_id = '".(int)$element_id."'";
//        $rs = ip_deprecated_mysql_query($sql);
//        if($rs){
//            if($lock = ip_deprecated_mysql_fetch_assoc($rs)){
//                return $lock['language_id'];
//            }
//        }else
//
//
//
//        trigger_error("Can't find zone element ".$sql." ".ip_deprecated_mysql_error());
//    }
//
//



    public static function pageInfo($pageId){
        //check when root page id given
        $sql = "
        SELECT
            mte.*
        FROM
            ".ipTable('zone_to_content', 'mte')."
        WHERE
            mte.element_id = :pageId
        ";

        $params = array(
            'pageId' => $pageId
        );
        $answer = ipDb()->fetchRow($sql, $params);
        if ($answer) {
            return $answer;
        }

        //non root page id given
        $voidZone = new \Ip\Internal\Content\Zone(array());
        $breadcrumb = $voidZone->getBreadcrumb($pageId);
        $pageId = $breadcrumb[0]->getId();

        $sql = "
        SELECT
            mte.*
        FROM
            ".ipTable('zone_to_content', 'mte').",
            ".ipTable('content_element', 'page')."
        WHERE
            page.id = :pageId
            AND
            page.parent = mte.element_id
        ";

        $params = array(
            'pageId' => $pageId
        );
        return ipDb()->fetchRow($sql, $params);
    }


    public static function getZoneName($zoneId){
        $sql = "
        SELECT
            `name`
        FROM
            ".ipTable('zone')."
        WHERE
            id = :id";

        $params = array(
            'id' => (int)$zoneId
        );

        return ipDb()->fetchValue($sql, $params);
    }


    /**
     * @param $zoneId
     * @param $languageId
     * @return mixed
     * @throws \Ip\CoreException
     */
    public static function rootId($zoneId, $languageId){
        $sql = "select mte.element_id from `".DB_PREF."zone_to_content` mte, `".DB_PREF."language` l where l.id = '".$languageId."' and  mte.language_id = l.id and zone_id = '".$zoneId."' ";
        $rs = ip_deprecated_mysql_query($sql);
        if ($rs) {
            if ($lock = ip_deprecated_mysql_fetch_assoc($rs)) {
                return $lock['element_id'];
            } else { //try to create
                self::createRootZoneElement($zoneId, $languageId);
                $rs2 = ip_deprecated_mysql_query($sql);
                if ($rs2) {
                    if ($lock2 = ip_deprecated_mysql_fetch_assoc($rs2)) {
                        return $lock2['element_id'];
                    } else {
                        throw new \Ip\CoreException("Failed to create root zone element. Zone: ". $zoneId . ', ' . $languageId);
                    }
                }
                throw new \Ip\CoreException("Failed to create root zone element. Zone: ". $zoneId . ', ' . $languageId);
            }
        } else {
            throw new \Ip\CoreException("Failed to create root zone element. Zone: ". $zoneId . ', ' . $languageId);
        }
    }

    /**
     * @param $zoneId
     * @param $languageId
     * @throws \Ip\CoreException
     */
    protected static function createRootZoneElement($zoneId, $languageId){
        $sql = "insert into `".DB_PREF."content_element` set visible = 1";
        $rs = ip_deprecated_mysql_query($sql);
        if ($rs) {
            $elementId = ip_deprecated_mysql_insert_id();
            $sql2 = "insert into `".DB_PREF."zone_to_content` set
            language_id = '".ip_deprecated_mysql_real_escape_string($languageId)."',
            zone_id = '".$zoneId."',
            element_id = '".$elementId."'";
            $rs2 = ip_deprecated_mysql_query($sql2);
            if (!$rs2) {
                throw new \Ip\CoreException($sql2 . " " . ip_deprecated_mysql_error());
            }
        }
    }

    public static function isChild($pageId, $parentId)
    {
        $page = self::getPage($pageId);
        if (!$page) {
            return FALSE;
        }
        if ($page['parent'] == $parentId) {
            return TRUE;
        }

        if ($page['parent']) {
            return self::isChild($page['parent'], $parentId);
        }

        return FALSE;
    }


    /**
     *
     * Get page children
     * @param int $elementId
     * @return array
     */
    public static function pageChildren($parentId){
        $sql = "select * from `".DB_PREF."content_element` where parent= '".$parentId."' order by row_number";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs){
            $pages = array();
            while($lock = ip_deprecated_mysql_fetch_assoc($rs)){
                $pages[] = $lock;
            }
            return $pages;
        } else {
            trigger_error("Can't get children ".$sql." ".ip_deprecated_mysql_error());
        }
    }

    /**
     *
     * Get page
     * @param int $id
     * @return array
     */
    private static function getPage($id){
        $sql = "select * from `".DB_PREF."content_element` where id= '".(int)$id."' ";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs){
            if($lock = ip_deprecated_mysql_fetch_assoc($rs)){
                return $lock;
            }
        } else {
            trigger_error("Can't get children ".$sql." ".ip_deprecated_mysql_error());
        }
        return false;
    }



    /**
     * @param $zoneName
     * @param $pageId
     * @param $params
     * @return bool
     */
    public static function updatePage($zoneName, $pageId, $params){
        $values = array();

        $zone = ipContent()->getZone($zoneName);
        if (!$zone) {
            throw new \Ip\CoreException("Page doesn't exist");
        }

        $oldPage = $zone->getPage($pageId);
        $oldUrl = $oldPage->getLink(true);

        if (isset($params['navigationTitle']))
        $values[] = 'button_title = \''.ip_deprecated_mysql_real_escape_string($params['navigationTitle']).'\'';

        if (isset($params['pageTitle']))
        $values[] =  'page_title = \''.ip_deprecated_mysql_real_escape_string($params['pageTitle']).'\'';

        if (isset($params['keywords']))
        $values[] =  'keywords = \''.ip_deprecated_mysql_real_escape_string($params['keywords']).'\'';

        if (isset($params['description']))
        $values[] =  'description = \''.ip_deprecated_mysql_real_escape_string($params['description']).'\'';

        if (isset($params['url'])){
            if ($params['url'] == '') {
                if (isset($params['pageTitle']) && $params['pageTitle'] != '') {
                    $params['url'] = self::makeUrl($params['pageTitle'], $pageId);
                } else {
                    if (isset($params['navigationTitle']) && $params['navigationTitle'] != '') {
                        $params['url'] = self::makeUrl($params['navigationTitle'], $pageId);
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
            $values[] =  'url= \''.ip_deprecated_mysql_real_escape_string($params['url']).'\'';
        }

        if (isset($params['createdOn']) && strtotime($params['createdOn']) !== false)
        $values[] =  'created_on = \''.ip_deprecated_mysql_real_escape_string($params['createdOn']).'\'';

        if (isset($params['lastModified']) && strtotime($params['lastModified']) !== false)
        $values[] =  'last_modified= \''.ip_deprecated_mysql_real_escape_string($params['lastModified']).'\'';

        if (isset($params['type']))
        $values[] =  'type = \''.ip_deprecated_mysql_real_escape_string($params['type']).'\'';

        if (isset($params['redirectURL']))
        $values[] =  'redirect_url = \''.ip_deprecated_mysql_real_escape_string($params['redirectURL']).'\'';

        if (isset($params['visible']))
        $values[] =  'visible = \''.ip_deprecated_mysql_real_escape_string($params['visible']).'\'';

        if (isset($params['parentId']))
        $values[] =  'parent = \''.ip_deprecated_mysql_real_escape_string($params['parentId']).'\'';

        if (isset($params['rowNumber']))
        $values[] =  'row_number = \''.ip_deprecated_mysql_real_escape_string($params['rowNumber']).'\'';

        if (isset($params['cached_html']))
        $values[] =  '`cached_html` = \''.ip_deprecated_mysql_real_escape_string($params['cached_html']).'\'';

        if (isset($params['cached_text']))
        $values[] =  '`cached_text` = \''.ip_deprecated_mysql_real_escape_string($params['cached_text']).'\'';

        if (count($values) == 0) {
            return true; //nothing to update.
        }

        $sql = 'UPDATE `'.DB_PREF.'content_element` SET '.implode(', ', $values).' WHERE `id` = '.(int)$pageId.' ';
        $rs = ip_deprecated_mysql_query($sql);
        if (!$rs) {
            trigger_error($sql.' '.ip_deprecated_mysql_error());
            return false;
        }

        if(isset($params['url']) && $oldPage->getUrl() != $params['url']){
            $newPage = $zone->getPage($pageId);
            $newUrl = $newPage->getLink(true);
            ipDispatcher()->notify('site.urlChanged', array('oldUrl' => $oldUrl, 'newUrl' => $newUrl));
        }

        if (!empty($params['layout']) && \Ip\Internal\File\Functions::isFileInDir($params['layout'], ipThemeFile(''))) {
            $layout = $params['layout'] == $zone->getLayout() ? false : $params['layout']; // if default layout - delete layout
            self::changePageLayout($zone->getAssociatedModuleGroup(), $zone->getAssociatedModule(), $pageId, $layout);
        }

        return true;
    }

    /**
     * @param $groupName
     * @param $moduleName
     * @param $pageId
     * @param $newLayout
     * @return bool whether layout was changed or not
     */
    private static function changePageLayout($groupName, $moduleName, $pageId, $newLayout) {
        $dbh = ipDb()->getConnection();

        $sql = 'SELECT `layout`
                FROM `' . DB_PREF . 'page_layout`
                WHERE group_name    = :groupName
                    AND module_name = :moduleName
                    AND `page_id`   = :pageId';
        $q = $dbh->prepare($sql);
        $q->execute(
            array(
                'groupName' => $groupName,
                'moduleName' => $moduleName,
                'pageId' => $pageId,
            )
        );
        $oldLayout = $q->fetchColumn(0);

        $wasLayoutChanged = false;

        if (empty($newLayout)) {
            if ($oldLayout) {
                $sql = 'DELETE FROM `' . DB_PREF . 'page_layout`
                        WHERE `group_name` = :groupName
                            AND `module_name` = :moduleName
                            AND `page_id` = :pageId';
                $q = $dbh->prepare($sql);
                $result = $q->execute(
                    array(
                        'groupName' => $groupName,
                        'moduleName' => $moduleName,
                        'pageId' => $pageId,
                    )
                );
                $wasLayoutChanged = true;
            }
        } elseif ($newLayout != $oldLayout && file_exists(ipThemeFile($newLayout))) {
            if (!$oldLayout) {
                $sql = 'INSERT IGNORE INTO `' . DB_PREF . 'page_layout`
                        (`group_name`, `module_name`, `page_id`, `layout`)
                        VALUES
                        (:groupName, :moduleName, :pageId, :layout)';


                $q = $dbh->prepare($sql);
                $result = $q->execute(
                    array(
                        'groupName' => $groupName,
                        'moduleName' => $moduleName,
                        'pageId' => $pageId,
                        'layout' => $newLayout,
                    )
                );
                $wasLayoutChanged = true;
            } else {
                $sql = 'UPDATE `' . DB_PREF . 'page_layout`
                        SET `layout` = :layout
                        WHERE `group_name` = :groupName
                            AND `module_name` = :moduleName
                            AND `page_id` = :pageId';

                $q = $dbh->prepare($sql);
                $result = $q->execute(
                    array(
                        'groupName' => $groupName,
                        'moduleName' => $moduleName,
                        'pageId' => $pageId,
                        'layout' => $newLayout,
                    )
                );
                $wasLayoutChanged = true;
            }
        }

        return $wasLayoutChanged;
    }

    /**
     *
     * Insert new page
     * @param int $parentId
     * @param array $params
     */
    public static function addPage($parentId, $params)
    {
        $row = array(
            'parent' => $parentId,
            'row_number' => self::getMaxIndex($parentId) + 1,

        );

        // TODOX what is this for?
        if (isset($params['button_title'])) {
            $params['navigationTitle'] = $params['button_title'];
        }
        if (isset($params['page_title'])) {
            $params['pageTitle'] = $params['page_title'];
        }
        if (isset($params['redirect_url'])) {
            $params['redirectURL'] = $params['redirect_url'];
        }

        if (isset($params['navigationTitle'])) {
            $row['button_title'] = $params['navigationTitle'];
        }

        if (isset($params['pageTitle'])) {
            $row['page_title'] = $params['pageTitle'];
        }

        if (isset($params['keywords'])) {
            $row['keywords'] = $params['keywords'];
        }

        if (isset($params['description'])) {
            $row['description'] = $params['description'];
        }

        if (isset($params['url'])) {
            $row['url'] = $params['url'];
        }

        if (isset($params['createdOn'])) {
            $row['createdOn'] = $params['createdOn'];
        } else {
            $row['createdOn'] = date('Y-m-d');
        }

        if (isset($params['lastModified'])) {
            $row['last_modified'] = $params['lastModified'];
        } else {
            $row['last_modified'] = date('Y-m-d');
        }

        if (isset($params['type'])) {
            $row['type'] = $params['type'];
        }

        if (isset($params['redirectURL'])) {
            $row['redirect_url'] = $params['redirectURL'];
        }

        if (isset($params['visible'])) {
            $row['visible'] = (int)$params['visible'];
        }

        if (isset($params['cached_html'])) {
            $row['cached_html'] = $params['cached_html'];
        }

        if (isset($params['cached_text'])) {
            $row['cached_text'] = $params['cached_text'];
        }

        return ipDb()->insert('content_element', $row);
    }

    private static function getMaxIndex($parentId) {
        $rs = ipDb()->select("MAX(`row_number`) AS `max_row_number`", 'content_element', array('parent' => $parentId));
        return $rs ? $rs[0]['max_row_number'] : null;
    }


    /**
     *
     * Delete menu element record
     * @param int $id
     */
    public static function deletePage($id)
    {
        ipDb()->delete('content_element', array('id' => $id));
    }


    public static function copyPage($nodeId, $newParentId, $newIndex){
        $sql = "select * from `".DB_PREF."content_element` where `id` = ".(int)$nodeId." ";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs){
            if($lock = ip_deprecated_mysql_fetch_assoc($rs)){
                $sql2 = "insert into `".DB_PREF."content_element` set `parent` = ".(int)$newParentId.", `row_number` = ".(int)$newIndex." ";

                foreach($lock as $key => $value){
                    switch($key){
                        case 'button_title':
                            $sql2 .= ", `button_title` = '".ip_deprecated_mysql_real_escape_string($lock['button_title'])."'";
                            break;
                        case 'id':

                            break;
                        case 'parent':

                            break;
                        case 'row_number':
                            break;
                        case 'url':
                            $sql2 .= ", `url` = '".ip_deprecated_mysql_real_escape_string(self::ensureUniqueUrl($value))."'";
                            break;
                        default:
                            if($value === null){
                                $sql2 .= ", `".ip_deprecated_mysql_real_escape_string($key)."` = NULL ";
                            } else {
                                $sql2 .= ", `".ip_deprecated_mysql_real_escape_string($key)."` = '".ip_deprecated_mysql_real_escape_string($value)."' ";
                            }
                            break;
                    }
                }
                $rs2 = ip_deprecated_mysql_query($sql2);
                if($rs2){
                    return ip_deprecated_mysql_insert_id();
                } else {
                    trigger_error($sql2.' '.ip_deprecated_mysql_error());
                }

            } else {
                trigger_error("Element does not exist");
            }
        } else {
            trigger_error($sql.' '.ip_deprecated_mysql_error());
        }
    }


    /**
     * @param string $url
     * @param int $allowed_id
     * @returns bool true if url is available ignoring $allowed_id page.
     */
    public static function availableUrl($url, $allowedId = null){

        $rs = ipDb()->select('`id`', 'content_element', array('url' => $url));

        if (!$rs) {
            return true;
        }

        if ($allowedId && $rs[0]['id'] == $allowedId) {
            return true;
        }

        return false;
    }

    /**
     *
     * Create unique URL
     * @param string $url
     * @param int $allowed_id
     */
    public static function makeUrl($url, $allowed_id = null)
    {

        if ($url == '') {
            $url = 'page';
        }

        $url = mb_strtolower($url);
        $url = \Ip\Internal\Text\Transliteration::transform($url);

        $replace = array(
            " " => "-",
            "/" => "-",
            "\\" => "-",
            "\"" => "-",
            "\'" => "-",
            "„" => "-",
            "“" => "-",
            "&" => "-",
            "%" => "-",
            "`" => "-",
            "!" => "-",
            "@" => "-",
            "#" => "-",
            "$" => "-",
            "^" => "-",
            "*" => "-",
            "(" => "-",
            ")" => "-",
            "{" => "-",
            "}" => "-",
            "[" => "-",
            "]" => "-",
            "|" => "-",
            "~" => "-",
            "." => "-",
            "'" => "",
            "?" => "",
            ":" => "",
            ";" => "",
        );
        $url = strtr($url, $replace);

        if ($url == ''){
            $url = '-';
        }

        $url = preg_replace('/-+/', '-', $url);

        if (self::availableUrl($url, $allowed_id)) {
            return $url;
        }

        $i = 1;
        while (!self::availableUrl($url.'-'.$i, $allowed_id)) {
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
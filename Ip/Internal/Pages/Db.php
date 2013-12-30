<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Internal\Pages;





class Db {



    /**
     * TODOX check zone and language url's against this function
     * Beginning of page URL can conflict with CMS system/core folders. This function checks if the folder can be used in URL beginning.
     *
     * @param $folderName
     * @return bool true if URL is reserved for CMS core
     *
     */
    public function usedUrl($folderName)
    {
        $systemDirs = array();
        // TODOX make it smart with overriden paths
        $systemDirs['Plugin'] = 1;
        $systemDirs['Theme'] = 1;
        $systemDirs['File'] = 1;
        $systemDirs['install'] = 1;
        $systemDirs['update'] = 1;
        if(isset($systemDirs[$folderName])){
            return true;
        } else {
            return false;
        }
    }




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
    public static function rootId($zoneId, $languageId)
    {
        $sql = '
            SELECT
                mte.element_id
            FROM ' . ipTable('zone_to_content', 'mte') . ', ' . ipTable('language', 'l') . '
            WHERE l.id = :languageId AND  mte.language_id = l.id AND zone_id = :zoneId';

        $where = array(
            'languageId' => $languageId,
            'zoneId' => $zoneId
        );

        $pageId = ipDb()->fetchValue($sql, $where);
        if (!$pageId) {
            $pageId = self::createRootZoneElement($zoneId, $languageId);
        }

        if (!$pageId) {
            throw new \Ip\CoreException("Failed to create root zone element. Zone: ". $zoneId . ', ' . $languageId);
        }

        return $pageId;
    }

    /**
     * @param $zoneId
     * @param $languageId
     * @throws \Ip\CoreException
     */
    protected static function createRootZoneElement($zoneId, $languageId)
    {
        $pageId = ipDb()->insert('content_element', array('visible' => 1));

        ipDb()->insert('zone_to_content', array(
                'language_id' => $languageId,
                'zone_id' => $zoneId,
                'element_id' => $pageId,
            ));
        return $pageId;
    }


    public static function deleteRootZoneElements($languageId)
    {
        return ipDb()->delete('zone_to_content', array('language_id' => $languageId));
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
     * Get page children
     * @param int $elementId
     * @return array
     */
    public static function pageChildren($parentId)
    {
        return ipDb()->select('*', 'content_element', array('parent' => $parentId), 'ORDER BY `row_number`');
    }

    /**
     *
     * Get page
     * @param int $id
     * @return array
     */
    private static function getPage($id)
    {
        $rs = ipDb()->select('*', 'content_element', array('id' => $id));
        return $rs ? $rs[0] : null;
    }


    /**
     * @param int $language_id
     * @return array all website zones with meta tags for specified language
     */
    public static function getZones($languageId)
    {
        $sql = 'SELECT m.*, p.url, p.description, p.keywords, p.title
                FROM ' . ipTable('zone', 'm') . ', ' . ipTable('zone_parameter', 'p') . '
                WHERE
                    p.zone_id = m.id
                    AND p.language_id = ?
                ORDER BY m.row_number';

        return ipDb()->fetchAll($sql, array($languageId));
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

        if (isset($params['navigationTitle'])) {
            $values['button_title'] = $params['navigationTitle'];
        }

        if (isset($params['pageTitle'])) {
            $values['page_title'] = $params['pageTitle'];
        }

        if (isset($params['keywords'])) {
            $values['keywords'] = $params['keywords'];
        }

        if (isset($params['description'])) {
            $values['description'] = $params['description'];
        }

        if (isset($params['url'])) {
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

            $values['url'] = $params['url'];
        }

        if (isset($params['createdOn']) && strtotime($params['createdOn']) !== false) {
            $values['created_on'] = $params['createdOn'];
        }

        if (isset($params['lastModified']) && strtotime($params['lastModified']) !== false) {
            $values['last_modified'] = $params['lastModified'];
        }

        if (isset($params['type'])) {
            $values['type'] = $params['type'];
        }

        if (isset($params['redirectURL'])) {
            $values['redirect_url'] = $params['redirectURL'];
        }

        if (isset($params['visible'])) {
            $values['visible'] = $params['visible'];
        }

        if (isset($params['parentId'])) {
            $values['parent'] = $params['parentId'];
        }

        if (isset($params['rowNumber'])) {
            $values['row_number'] = $params['rowNumber'];
        }

        if (isset($params['cached_html'])) {
            $values['cached_html'] = $params['cached_html'];
        }

        if (isset($params['cached_text'])) {
            $values['cached_text'] = $params['cached_text'];
        }

        if (count($values) == 0) {
            return true; //nothing to update.
        }

        ipDb()->update('content_element', $values, array('id' => $pageId));

        if (isset($params['url']) && $oldPage->getUrl() != $params['url']) {
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
            $row['created_on'] = $params['createdOn'];
        } else {
            $row['created_on'] = date('Y-m-d');
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


    public static function copyPage($nodeId, $newParentId, $newIndex)
    {
        $db = ipDb();
        $rs = $db->select('*', 'content_element', array('id' => $nodeId));
        if (!$rs) {
            trigger_error("Element does not exist");
        }

        $copy = $rs[0];
        unset($copy['id']);
        $copy['parent'] = $newParentId;
        $copy['row_number'] = $newIndex;
        $copy['url'] = self::ensureUniqueUrl($copy['url']);

        return ipDb()->insert('content_element', $copy);
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
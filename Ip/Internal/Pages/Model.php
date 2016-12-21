<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Pages;


class Model
{
    /**
     * Move page to trash
     *
     * @param int $pageId
     * @param int $deleteType
     */
    public static function moveToTrash($pageId, $deleteType = 1)
    {
        $children = self::getChildren($pageId);
        if ($children) {
            foreach ($children as $child) {
                self::moveToTrash($child['id'], 2);
            }
        }

        ipDb()->update(
            'page',
            array('isDeleted' => $deleteType, 'deletedAt' => date('Y-m-d H:i:s')),
            array('id' => $pageId)
        );
        ipEvent('ipPageMarkedAsDeleted', array('pageId' => $pageId));
    }

    /**
     * Copy page
     *
     * @param int $pageId
     * @param $destinationPageId
     * @param int $position page position in the subtree //TODO implement
     * @internal param int $newParentId
     * @return int
     */
    public static function copyPage($pageId, $destinationPageId, $destinationPosition = null)
    {
        $children = self::getChildren($destinationPageId);

        $newPageOrder = 1;
        if (count($children) > 0) {
            $newPageOrder = $children[0]['pageOrder'] - 1; // Set as first page.
            if ($destinationPosition > 0) {
                if (isset($children[$destinationPosition - 1]) && isset($children[$destinationPosition])) { // New position is in the middle of other pages.
                    $newPageOrder = ($children[$destinationPosition - 1]['pageOrder'] + $children[$destinationPosition]['pageOrder']) / 2; // Average
                } else { // New position is at the end.
                    $newPageOrder = $children[count($children) - 1]['pageOrder'] + 1;
                }
            }
        }

        return self::_copyPageRecursion($pageId, $destinationPageId, $newPageOrder);
    }

    /**
     * Copy page internal recursion
     *
     * @param int $nodeId
     * @param int $destinationPageId
     * @param int $rowNumber
     * @param array $newPages
     * @return int
     */
    private static function _copyPageRecursion($nodeId, $destinationPageId, $rowNumber, $newPages = null)
    {
        // $newPages are the pages that have been copied already and should be skipped to duplicate again. This situation can occur when copying the page to it self.
        if ($newPages == null) {
            $newPages = [];
        }
        $newNodeId = static::copySinglePage($nodeId, $destinationPageId, $rowNumber);
        $newPages[$newNodeId] = 1;
        self::_copyWidgets($nodeId, $newNodeId);

        $children = self::getChildren($nodeId);
        if ($children) {
            foreach ($children as $key => $lock) {
                if (!isset($newPages[$lock['id']])) {
                    self::_copyPageRecursion($lock['id'], $newNodeId, $key, $newPages);
                }
            }
        }

        return $newNodeId;
    }

    /**
     * Copy single page
     *
     * @param int $nodeId
     * @param int $newParentId
     * @param int $newIndex
     * @return bool|string
     */
    public static function copySinglePage($nodeId, $newParentId, $newIndex)
    {
        $copy = ipDb()->selectRow('page', '*', array('id' => $nodeId));
        if (!$copy) {
            trigger_error('Element does not exist');
        }

        $menu = ipContent()->getPageMenu($newParentId);
        if ($menu) {
            $copy['languageCode'] = $menu->getLanguageCode();
        }

        unset($copy['id']);
        $copy['parentId'] = $newParentId;
        $copy['pageOrder'] = $newIndex;
        $copy['urlPath'] = UrlAllocator::allocatePath($copy['languageCode'], $copy['urlPath']);
        $copy['createdAt'] = date('Y-m-d H:i:s');
        $copy['updatedAt'] = date('Y-m-d H:i:s');


        $pageId = ipDb()->insert('page', $copy);

        $eventInfo = ipDb()->selectRow('page', '*', array('id' => $pageId));
        $eventInfo['sourceId'] = $nodeId;

        ipEvent('ipPageDuplicated', $eventInfo);
        return $pageId;
    }

    /**
     * @param int $sourceId
     * @param int $targetId
     */
    private static function _copyWidgets($sourceId, $targetId)
    {
        $oldRevision = \Ip\Internal\Revision::getPublishedRevision($sourceId);
        \Ip\Internal\Revision::duplicateRevision($oldRevision['revisionId'], $targetId, 1);
    }

    /**
     * Get properties of menu
     *
     * @param string $languageCode
     * @param string $alias
     * @return array|null
     */
    public static function getMenu($languageCode, $alias)
    {
        return ipDb()->selectRow(
            'page',
            '*',
            array('languageCode' => $languageCode, 'alias' => $alias, 'isDeleted' => 0)
        );
    }

    /**
     * Get children
     *
     * @param int $parentId
     * @param int $start
     * @param int $limit
     * @return array
     */
    public static function getChildren($parentId, $start = null, $limit = null)
    {
        $sqlEnd = 'ORDER BY `pageOrder`';
        if ($start !== null || $limit !== null) {
            $sqlEnd .= ' LIMIT ' . (int)$start;
        }
        if ($limit !== null) {
            $sqlEnd .= ', ' . (int)$limit;
        }

        return ipDb()->selectAll('page', '*', array('parentId' => $parentId, 'isDeleted' => 0), $sqlEnd);
    }

    public static function getDefaultMenuPagePosition($menuAlias, $whenPageIsSelected, $default)
    {
        $key = 'menu_' . $menuAlias . '_default_position';
        if ($whenPageIsSelected) {
            $key .= '_selected';
        }
        return ipStorage()->get('Pages', $key, $default);
    }

    public static function setDefaultMenuPagePosition($menuAlias, $whenPageIsSelected, $position)
    {
        $key = 'menu_' . $menuAlias . '_default_position';
        if ($whenPageIsSelected) {
            $key .= '_selected';
        }
        ipStorage()->set('Pages', $key, $position);
    }

    /**
     * Get menu list
     *
     * @param string $languageCode
     * @return array
     */
    public static function getMenuList($languageCode = null)
    {
        $where = array('parentId' => 0, 'isDeleted' => 0);
        if ($languageCode !== null) {
            $where['languageCode'] = $languageCode;
        }

        $list = ipDb()->selectAll('page', '*', $where, ' ORDER BY `pageOrder` ');


        return $list;
    }

    /**
     * Get page
     *
     * @param int $pageId
     * @return array|null
     */
    public static function getPage($pageId)
    {
        return ipDb()->selectRow('page', '*', array('id' => $pageId, 'isDeleted' => 0));
    }

    /**
     * Get pages by URL
     *
     * @param string $languageCode
     * @param string $urlPath
     * @return array|null
     */
    public static function getPageByUrl($languageCode, $urlPath)
    {
        $page = ipDb()->selectRow(
            'page',
            '*',
            array('languageCode' => $languageCode, 'urlPath' => $urlPath, 'isDeleted' => 0)
        );

        if ($page) {
            return $page;
        }

        //if lash exists, remove. If there is no slash, add it.
        if (substr($urlPath, -1) == '/') {
            $urlPath = substr($urlPath, 0, -1);
        } else {
            $urlPath .= '/';
        }

        $page = ipDb()->selectRow(
            'page',
            '*',
            array('languageCode' => $languageCode, 'urlPath' => $urlPath, 'isDeleted' => 0)
        );
        return $page;
    }

    /**
     * Get pages by alias
     *
     * @param string $languageCode
     * @param string $alias
     * @return array|null
     */
    public static function getPageByAlias($languageCode, $alias)
    {
        return ipDb()->selectRow(
            'page',
            '*',
            array('languageCode' => $languageCode, 'alias' => $alias, 'isDeleted' => 0)
        );
    }

    /**
     * Get next page order
     *
     * @param array $where
     * @return int
     */
    protected static function getNextPageOrder($where)
    {
        if (empty($where['isDeleted'])) {
            $where['isDeleted'] = 0;
        }
        $nextPageOrder = ipDb()->selectValue('page', 'MAX(`pageOrder`) + 1', $where);

        return $nextPageOrder ? $nextPageOrder : 1;
    }

    /**
     * Change URL path of page
     *
     * @param string $pageId
     * @param string $newUrlPath
     * @return bool|null
     */
    protected static function changePageUrlPath($pageId, $newUrlPath)
    {
        $pageBeforeChange = ipPage($pageId);

        if (ipGetOption('Config.trailingSlash', 1)) {
            if (mb_substr($newUrlPath, -1) != '/') {
                $newUrlPath .= '/';
            }
        } else {
            if (mb_substr($newUrlPath, -1) == '/') {
                $newUrlPath = mb_substr($newUrlPath, 0, -1);
            }
        }

        if ($newUrlPath == $pageBeforeChange->getUrlPath()) {
            return false;
        }

        $allocatedPath = UrlAllocator::allocatePath($pageBeforeChange->getLanguageCode(), $newUrlPath);
        ipDb()->update('page', array('urlPath' => $allocatedPath), array('id' => $pageId));

        $pageAfterChange = ipPage($pageId);

        $oldUrl = $pageBeforeChange->getLink();
        if (substr($oldUrl, -1) == '/') {
            $oldUrl = substr($oldUrl, 0, -1);
        }
        $newUrl = $pageAfterChange->getLink();
        if (substr($newUrl, -1) == '/') {
            $newUrl = substr($newUrl, 0, -1);
        }
        ipEvent(
            'ipUrlChanged',
            array(
                'oldUrl' => $oldUrl,
                'newUrl' => $newUrl,
            )
        );
        return null;
    }

    /**
     * Update properties of page
     *
     * @param int $pageId
     * @param array $properties
     * @return bool
     */
    public static function updatePageProperties($pageId, $properties)
    {
        $update = [];

        if (isset($properties['title'])) {
            $update['title'] = $properties['title'];
        }

        if (isset($properties['metaTitle'])) {
            $update['metaTitle'] = $properties['metaTitle'];
        }

        if (isset($properties['keywords'])) {
            $update['keywords'] = $properties['keywords'];
        }

        if (isset($properties['description'])) {
            $update['description'] = $properties['description'];
        }

        if (isset($properties['createdAt']) && strtotime($properties['createdAt']) !== false) {
            $update['createdAt'] = $properties['createdAt'];
        }

        if (isset($properties['updatedAt']) && strtotime($properties['updatedAt']) !== false) {
            $update['updatedAt'] = $properties['updatedAt'];
        }

        if (isset($properties['type'])) {
            $update['type'] = $properties['type'];
        }

        if (isset($properties['redirectUrl'])) {
            $update['redirectUrl'] = $properties['redirectUrl'];
        }

        if (isset($properties['isDisabled'])) {
            $update['isDisabled'] = $properties['isDisabled'];
        }

        if (isset($properties['isSecured'])) {
            $update['isSecured'] = $properties['isSecured'];
        }

        if (isset($properties['isVisible'])) {
            $update['isVisible'] = $properties['isVisible'];
        }

        if (isset($properties['alias'])) {
            $update['alias'] = $properties['alias'];
        }

        if (isset($properties['isBlank'])) {
            $update['isBlank'] = $properties['isBlank'];
        }

        if (!empty($properties['layout'])) {
            $update['layout'] = $properties['layout'];
            $menu = ipContent()->getPageMenu($pageId);
            if ($menu && $menu->getLayout() == $properties['layout']) {
                $update['layout'] = null;
            }
        }

        if (count($update) != 0) {
            ipDb()->update('page', $update, array('id' => $pageId));
        }

        if (!empty($properties['type'])) {
            $update['type'] = $properties['type'];
        }

        if (isset($properties['urlPath'])) {
            self::changePageUrlPath($pageId, $properties['urlPath']);
        }

        $properties['id'] = $pageId;
        ipEvent('ipPageUpdated', $properties);

        return true;
    }

    /**
     * Is child page?
     *
     * @param int $pageId
     * @param int $parentId
     * @return bool
     */
    public static function isChild($pageId, $parentId)
    {
        $page = self::getPage($pageId);
        if (!$page) {
            return false;
        }
        if ($page['parentId'] == $parentId) {
            return true;
        }

        if ($page['parentId']) {
            return self::isChild($page['parentId'], $parentId);
        }

        return false;
    }

    /**
     * Move page
     *
     * @param $pageId
     * @param int $destinationParentId
     * @param int $destinationPosition
     * @throws \Ip\Exception
     * @internal param int $menuId
     */
    public static function movePage($pageId, $destinationParentId, $destinationPosition)
    {
        if ((int)$pageId === (int)$destinationParentId || static::isChild($destinationParentId, $pageId)) {
            throw new \Ip\Exception("Can't move page inside itself.");
        }

        $parent = ipContent()->getPage($destinationParentId);
        $newParentChildren = self::getChildren($destinationParentId);
        $newPageOrder = 0; // Initial value.

        if (count($newParentChildren) > 0) {
            $newPageOrder = $newParentChildren[0]['pageOrder'] - 1; // Set as first page.
            if ($destinationPosition > 0) {
                if (isset($newParentChildren[$destinationPosition - 1]) && isset($newParentChildren[$destinationPosition])) { // New position is in the middle of other pages.
                    $newPageOrder = ($newParentChildren[$destinationPosition - 1]['pageOrder'] + $newParentChildren[$destinationPosition]['pageOrder']) / 2; // Average
                } else { // New position is at the end.
                    $newPageOrder = $newParentChildren[count($newParentChildren) - 1]['pageOrder'] + 1;
                }
            }
        }

        $update = array(
            'parentId' => $destinationParentId,
            'pageOrder' => $newPageOrder,
            'languageCode' => $parent->getLanguageCode()
        );

        $eventData = array(
            'pageId' => $pageId,
            'destinationParentId' => $destinationParentId,
            'destinationPosition' => $destinationPosition
        );
        ipEvent('ipBeforePageMoved', $eventData);
        ipDb()->update('page', $update, array('id' => $pageId));
        $children = self::getChildren($pageId);
        if ($children) {
            foreach ($children as $child) {
                ipDb()->update('page', array('languageCode' => $update['languageCode']), array('id' => $child['id']));
            }
        }

        ipEvent('ipPageMoved', $eventData);
    }

    /**
     * Update properties of menu
     *
     * @param int $menuId
     * @param string $alias
     * @param string $title
     * @param string $layout
     * @param string $type
     */
    public static function updateMenu($menuId, $alias, $title, $layout, $type)
    {
        $properties = array(
            'alias' => $alias,
            'title' => $title,
            'layout' => $layout,
            'type' => $type
        );

        self::updatePageProperties($menuId, $properties);
    }

    /**
     * @param $languageCode
     * @param $alias
     * @param $title
     * @param string $type
     * @return int
     */
    public static function createMenu($languageCode, $alias, $title, $type = 'tree')
    {
        $data = [];
        $data['languageCode'] = $languageCode;

        if (empty($alias)) {
            $alias = \Ip\Internal\Text\Specialchars::url($title);
        }

        $data['alias'] = static::allocateUniqueAlias($languageCode, $alias);
        $data['title'] = $title;
        $data['type'] = $type;

        $data['parentId'] = 0;
        $data['pageOrder'] = static::getNextPageOrder(
            array('languageCode' => $languageCode, 'parentId' => $data['parentId'])
        );
        $data['isVisible'] = 1;

        return self::addPage(0, $data);
    }

    /**
     * Allocate unique alias
     *
     * @param string $languageCode
     * @param string $alias
     * @return string
     */
    protected static function allocateUniqueAlias($languageCode, $alias)
    {
        $condition = array('languageCode' => $languageCode, 'alias' => $alias, 'isDeleted' => 0);

        $exists = ipDb()->selectValue('page', 'id', $condition);
        if (!$exists) {
            return $alias;
        }

        $i = 2;
        while (ipDb()->selectValue(
            'page',
            'id',
            array('languageCode' => $languageCode, 'alias' => $alias . '-' . $i, 'isDeleted' => 0)
        )) {
            $i++;
        }

        return $alias . '-' . $i;
    }

    /**
     * Insert new page
     *
     * @param int $parentId
     * @param array $params
     * @return int
     */
    public static function addPage($parentId, $params)
    {
        $pageOrderCondition = array(
            'parentId' => $parentId,
        );
        if (!empty($params['languageCode'])) {
            $pageOrderCondition['languageCode'] = $params['languageCode'];
        }

        $row = array(
            'parentId' => $parentId,
            'pageOrder' => self::getNextPageOrder($pageOrderCondition),
        );

        $fields = array(
            'title',
            'metaTitle',
            'languageCode',
            'keywords',
            'description',
            'urlPath',
            'createdAt',
            'updatedAt',
            'type',
            'isVisible',
            'alias'
        );

        foreach ($fields as $column) {
            if (array_key_exists($column, $params)) {
                $row[$column] = $params[$column];
            }
        }

        if (!empty($row['urlPath']) && ipGetOption('Config.trailingSlash', 1) && substr($row['urlPath'], -1) != '/') {
            $row['urlPath'] .= '/';
        }

        if (empty($row['createdAt'])) {
            $row['createdAt'] = date('Y-m-d H:i:s');
        }

        if (empty($row['updatedAt'])) {
            $row['updatedAt'] = date('Y-m-d H:i:s');
        }

        $pageId = ipDb()->insert('page', $row);

        $row['id'] = $pageId;

        ipEvent('ipPageAdded', $row);

        return $pageId;
    }

    /**
     * Change menu order
     *
     * @param int $menuId
     * @param int $newIndex
     */
    public static function changeMenuOrder($menuId, $newIndex)
    {
        $menu = static::getPage($menuId);

        $menus = static::getMenuList($menu['languageCode']);

        $newPriority = null;

        if ($newIndex <= 0) {
            $newPriority = $menus[0]['pageOrder'] - 20;
        } elseif ($newIndex > count($menus) - 1) {
            $lastMenu = end($menus);
            $newPriority = $lastMenu['pageOrder'] + 20;
        } else {
            $newPriority = ($menus[$newIndex - 1]['pageOrder'] + $menus[$newIndex]['pageOrder']) / 2;
        }

        ipDb()->update('page', array('pageOrder' => $newPriority), array('id' => $menuId));
    }

    /**
     * Update URL
     *
     * @param string $oldUrl
     * @param string $newUrl
     */
    public static function updateUrl($oldUrl, $newUrl)
    {

        $old = parse_url($oldUrl);
        $new = parse_url($newUrl);

        $oldPart = $old['host'] . rtrim($old['path'], '/');
        $newPart = $new['host'] . rtrim($new['path'], '/');

        $replaces = array(
            'http://' . $oldPart => 'http://' . $newPart,
            'http://' . $oldPart . '/' => 'http://' . $newPart . '/',
            'https://' . $oldPart => 'https://' . $newPart,
            'https://' . $oldPart . '/' => 'https://' . $newPart . '/',
        );

        if ($newUrl == ipConfig()->baseUrl()) {
            //the whole website URL has changed
            $table = ipTable('page');
            $sql = "
            UPDATE
              $table
            SET
              `redirectUrl` = REPLACE(`redirectUrl`, :search, :replace)
            WHERE
            1
            ";
            foreach ($replaces as $search => $replace) {
                ipDb()->execute($sql, array('search' => $search, 'replace' => $replace));
            }
        } else {
            //single page URL has changed
            foreach ($replaces as $search => $replace) {
                ipDb()->update('page', array('redirectUrl' => $replace), array('redirectUrl' => $search));
            }

        }
    }

    /**
     * Removes deleted page and its children from the trash.
     *
     * Does not remove page if page is not deleted.
     * @param int $pageId
     * @return int Number of pages deleted.
     */
    public static function removeDeletedPage($pageId)
    {
        $canBeDeleted = ipDb()->selectValue('page', 'id', array('id' => $pageId, 'isDeleted' => 1));
        if (!$canBeDeleted) {
            return false;
        }

        return static::_removeDeletedPage($pageId);
    }

    /**
     * We assume page is safe to delete.
     *
     * @param int $pageId
     * @return int Count of deleted pages.
     */
    protected static function _removeDeletedPage($pageId)
    {
        $deletedPageCount = 0;
        $children = ipDb()->selectAll('page', array('id', 'isDeleted'), array('parentId' => $pageId));
        foreach ($children as $child) {
            if ($child['isDeleted']) {
                $deletedPageCount += static::_removeDeletedPage($child['id']);
            } else {
                // This should never happen!
                ipLog()->error(
                    'Page.pageHasDeletedParent: page {pageId}, parent set to null',
                    array('pageId' => $child['id'])
                );
                ipDb()->update('page', array('parentId' => null), array('id' => $child['id']));
            }
        }

        ipEvent('ipBeforePageRemoved', array('pageId' => $pageId));
        $count = ipDb()->delete('page', array('id' => $pageId));
        ipPageStorage($pageId)->removeAll();
        ipEvent('ipPageRemoved', array('pageId' => $pageId));

        $deletedPageCount += (int)$count;
        return $deletedPageCount;
    }

    /**
     * Recorvery deleted page and its children from the trash.
     *
     * Does not recovery page if page is not deleted.
     * @param int $pageId
     * @return int Number of pages recorvered.
     */
    public static function recoveryDeletedPage($pageId)
    {
        $canBeRecovery = ipDb()->selectValue('page', 'id', array('id' => $pageId, 'isDeleted' => 1));
        if (!$canBeRecovery) {
            return false;
        }

        ipDb()->update('page', array('isDeleted' => 0), array('id' => $pageId));

        return 1;
    }

    /**
     * Trash size
     *
     * @return int Number of trash pages.
     */
    public static function trashSize()
    {
        $table = ipTable('page');
        $sql = "SELECT COUNT(*)
                FROM $table
                WHERE `isDeleted` > 0";

        return ipDb()->fetchValue($sql);
    }

}

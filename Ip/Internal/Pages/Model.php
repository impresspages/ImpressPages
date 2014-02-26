<?php

/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Internal\Pages;


class Model
{
    public static function moveToTrash($pageId)
    {
        $children = self::getChildren($pageId);
        if ($children) {
            foreach ($children as $child) {
                self::moveToTrash($child['id']);
            }
        }

        ipDb()->update('page', array('isDeleted' => 1, 'deletedAt' => date('Y-m-d H:i:s')), array('id' => $pageId));
        ipEvent('ipPageMarkedAsDeleted', array('pageId' => $pageId));
    }

    /**
     *
     * Copy page
     * @param int $pageId
     * @param int $newParentId
     * @param int $position page position in the subtree //TODO implement
     */
    public static function copyPage($pageId, $destinationPageId, $position = null)
    {

        $children = self::getChildren($destinationPageId);

        if (!empty($children)) {
            $rowNumber = $children[count($children) - 1]['pageOrder'] + 1;
        } else {
            $rowNumber = 0;
        }


        return self::_copyPageRecursion($pageId, $destinationPageId, $rowNumber);

    }

    /**
     *
     * Copy page internal recursion
     * @param unknown_type $nodeId
     * @param unknown_type $destinationPageId
     * @param unknown_type $newIndex
     * @param unknown_type $newPages
     */
    private static function _copyPageRecursion(
        $nodeId,
        $destinationPageId,
        $rowNumber,
        $newPages = null
    ) {
        //$newPages are the pages that have been copied already and should be skipped to duplicate again. This situation can occur when copying the page to it self
        if ($newPages == null) {
            $newPages = array();
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

    public static function copySinglePage($nodeId, $newParentId, $newIndex)
    {
        $copy = ipDb()->selectRow('page', '*', array('id' => $nodeId));
        if (!$copy) {
            trigger_error("Element does not exist");
        }

        unset($copy['id']);
        $copy['parentId'] = $newParentId;
        $copy['pageOrder'] = $newIndex;
        $copy['urlPath'] = UrlAllocator::allocatePath($copy['languageCode'], $copy['urlPath']);
        //TODOX ensure unique alias

        return ipDb()->insert('page', $copy);
    }

    private static function _copyWidgets($sourceId, $targetId)
    {
        $oldRevision = \Ip\Internal\Revision::getPublishedRevision($sourceId);
        \Ip\Internal\Revision::duplicateRevision($oldRevision['revisionId'], $targetId, 1);
    }

    public static function getMenu($languageCode, $alias)
    {
        return ipDb()->selectRow('page', '*', array('languageCode' => $languageCode, 'alias' => $alias, 'isDeleted' => 0));
    }


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

    public static function getMenuList($languageCode)
    {
        $list = ipDb()->selectAll(
            'page',
            '*',
            array('languageCode' => $languageCode, 'parentId' => 0),
            ' ORDER BY `pageOrder` '
        );

        foreach ($list as &$menu) {
            $menu['menuType'] = ipPageStorage($menu['id'])->get('menuType', 'tree');
        }

        return $list;
    }

    public static function getPage($pageId)
    {
        return ipDb()->selectRow('page', '*', array('id' => $pageId, 'isDeleted' => 0));
    }

    public static function getPageByUrl($languageCode, $urlPath)
    {
        return ipDb()->selectRow('page', '*', array('languageCode' => $languageCode, 'urlPath' => $urlPath, 'isDeleted' => 0));
    }

    protected static function getNextPageOrder($where)
    {
        if (empty($where['isDeleted'])) {
            $where['isDeleted'] = 0;
        }
        $nextPageOrder = ipDb()->selectValue('page', 'MAX(`pageOrder`) + 1', $where);
        return $nextPageOrder ? $nextPageOrder : 1;
    }

    public static function changePageUrlPath($pageId, $newUrlPath)
    {
        $pageBeforeChange = ipPage($pageId);

        if ($newUrlPath == $pageBeforeChange->getUrlPath()) {
            return false;
        }

        $allocatedPath = UrlAllocator::allocatePath($pageBeforeChange->getLanguageCode(), $newUrlPath);
        ipDb()->update('page', array('urlPath' => $allocatedPath), array('id' => $pageId));

        $pageAfterChange = ipPage($pageId);

        ipEvent(
            'ipUrlChanged',
            array(
                'oldUrl' => $pageBeforeChange->getLink(),
                'newUrl' => $pageAfterChange->getLink(),
            )
        );
    }

    /**
     * @param $pageId
     * @param $properties
     * @return bool
     */
    public static function updatePageProperties($pageId, $properties)
    {
        $update = array();

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

        if (isset($properties['redirectURL'])) {
            $update['redirectUrl'] = $properties['redirectURL'];
        }

        if (isset($properties['isVisible'])) {
            $update['isVisible'] = $properties['isVisible'];
        }

        if (count($update) == 0) {
            return true; //nothing to update.
        }

        ipDb()->update('page', $update, array('id' => $pageId));

        if (!empty($properties['layout'])) {
            ipPageStorage($pageId)->set('layout', $properties['layout']);
        }

        return true;
    }

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

    public static function movePage($pageId, $destinationParentId, $destinationPosition)
    {
        if ((int)$pageId === (int)$destinationParentId || static::isChild($destinationParentId, $pageId)) {
            throw new \Ip\Exception(__("Can't move page inside itself.", 'ipAdmin', false));
        }

        $newParentChildren = self::getChildren($destinationParentId);
        $newPageOrder = 0; //initial value

        if (count($newParentChildren) > 0) {
            $newPageOrder = $newParentChildren[0]['pageOrder'] - 1; //set as first page
            if ($destinationPosition > 0) {
                if (isset($newParentChildren[$destinationPosition - 1]) && isset($newParentChildren[$destinationPosition])) { //new position is in the middle of other pages
                    $newPageOrder = ($newParentChildren[$destinationPosition - 1]['pageOrder'] + $newParentChildren[$destinationPosition]['pageOrder']) / 2; //average
                } else { //new position is at the end
                    $newPageOrder = $newParentChildren[count($newParentChildren) - 1]['pageOrder'] + 1;
                }
            }
        }

        $update = array(
            'parentId' => $destinationParentId,
            'pageOrder' => $newPageOrder
        );

        ipDb()->update('page', $update, array('id' => $pageId));
    }

    public static function updateMenu($menuId, $alias, $title, $layout, $type)
    {
        $update = array(
            'alias' => $alias,
            'title' => $title,
        );

        ipDb()->update('page', $update, array('id' => $menuId));
        ipPageStorage($menuId)->set('layout', $layout);
        ipPageStorage($menuId)->set('menuType', $type);
    }

    public static function createMenu($languageCode, $alias, $title)
    {
        $data = array();
        $data['languageCode'] = $languageCode;
        $data['alias'] = $alias;
        $data['title'] = $title;

        $data['parentId'] = 0;
        $data['pageOrder'] = static::getNextPageOrder(
            array('languageCode' => $languageCode, 'parentId' => $data['parentId'])
        );
        $data['isVisible'] = 1;

        $menuId = ipDb()->insert('page', $data);

        return $menuId ? $alias : null;
    }


    /**
     *
     * Insert new page
     * @param int $parentId
     * @param array $params
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
            'isVisible'
        );

        foreach ($fields as $column) {
            if (array_key_exists($column, $params)) {
                $row[$column] = $params[$column];
            }
        }

        if (empty($row['createdAt'])) {
            $row['createdAt'] = date('Y-m-d H:i:s');
        }

        if (empty($row['updatedAt'])) {
            $row['updatedAt'] = date('Y-m-d H:i:s');
        }


        return ipDb()->insert('page', $row);
    }

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

    public static function deleteTrashPages()
    {
        $daysToKeepInTrash = (int)ipGetOption('ipDaysToKeepInTrash', 7);

        if (empty($daysToKeepInTrash)) {
            return;
        }

        $deletedBefore = date('Y-m-d H:i:s', strtotime("-$daysToKeepInTrash days"));
        $deletedPages = ipDb()->selectColumn('page', 'id', array('isDeleted' => 1), " AND `deletedAt` < '$deletedBefore'");

        foreach ($deletedPages as $pageId) {
            static::deleteForever($pageId);
        }
    }

    public static function deleteForever($pageId)
    {
        ipDb()->delete('page', array('id' => $pageId));
        ipPageStorage($pageId)->removeAll();
        ipEvent('ipPageDeleted', array('pageId' => $pageId));
    }

}

<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Pages;


class Service
{
    /**
     * @param $pageId
     * @return array
     */
    public static function getPage($pageId)
    {
        return Model::getPage($pageId);
    }

    /**
     * @param string $languageCode
     * @param string $urlPath
     */
    public static function getPageByUrl($languageCode, $urlPath)
    {
        return Model::getPageByUrl($languageCode, $urlPath);
    }

    /**
     * @param string $languageCode
     * @param string $alias
     */
    public static function getMenu($languageCode, $alias)
    {
        return Model::getMenu($languageCode, $alias);
    }

    /**
     * @param string $languageCode
     */
    public static function getMenus($languageCode)
    {
        return Model::getMenuList($languageCode);
    }

    /**
     * @param int $pageId
     * @param array $data
     */
    public static function updatePage($pageId, $data)
    {
        Model::updatePageProperties($pageId, $data);
    }

    /**
     * @param int $parentId
     * @param string $title
     * @param array $data
     * @return int page id
     * @throws \Ip\Exception
     */
    public static function addPage($parentId, $title, $data = array())
    {
        $data['title'] = $title;

        if (!isset($data['createdAt'])) {
            $data['createdAt'] = date("Y-m-d H:i:s");
        }
        if (!isset($data['updatedAt'])) {
            $data['updatedAt'] = date("Y-m-d H:i:s");
        }
        if (!isset($data['isVisible'])) {
            $data['isVisible'] = !ipGetOption('Pages.hideNewPages');
        }

        if (!isset($data['languageCode'])) {
            $data['languageCode'] = ipDb()->selectValue('page', 'languageCode', array('id' => $parentId));
            if (empty($data['languageCode'])) {
                $data['languageCode'] = ipDb()->selectValue('page', 'languageCode', array('alias' => $parentId));
            }
            if (empty($data['languageCode'])) {
                throw new \Ip\Exception('Page languageCode should be set if parent is absent');
            }
        }

        if (!isset($data['urlPath'])) {
            $dataForPath = $data;
            $dataForPath['parentId'] = $parentId;
            $data['urlPath'] = UrlAllocator::allocatePathForNewPage($dataForPath);
        }

        $newPageId = Model::addPage($parentId, $data);

        return $newPageId;
    }

    /**
     * @param int $pageId
     * @param int $destinationParentId
     * @param int $destinationPosition
     */
    public static function copyPage($pageId, $destinationParentId, $destinationPosition)
    {
        return Model::copyPage($pageId, $destinationParentId, $destinationPosition);
    }

    /**
     * @param int $pageId
     * @param int $destinationParentId
     * @param int $destinationPosition
     */
    public static function movePage($pageId, $destinationParentId, $destinationPosition)
    {
        Model::movePage($pageId, $destinationParentId, $destinationPosition);
    }

    /**
     * @param int $pageId
     */
    public static function deletePage($pageId)
    {
        Model::moveToTrash($pageId);
    }

    /**
     * Removes pages that were deleted before given time
     *
     * @param string $timestamp in mysql format
     * @return int count of deleted pages
     */
    public static function removeDeletedBefore($timestamp)
    {
        $table = ipTable('page');

        $pages = ipDb()->fetchAll("SELECT `id` FROM $table WHERE `isDeleted` = 1 AND `deletedAt` < ?", array($timestamp));

        foreach ($pages as $page) {
            static::removeDeletedPage($page['id']);
        }
    }

    /**
     * Remove all deleted pages
     *
     * @return int count of deleted pages
     */
    public static function emptyTrash()
    {
        $pages = ipDb()->selectAll('page', 'id', array('isDeleted' => 1));

        $deleted = 0;

        foreach ($pages as $page) {
            $deleted += static::removeDeletedPage($page['id']);
        }

        return $deleted;
    }

    /**
     * Removes deleted page and its children from the trash
     *
     * Does not remove page if it is not deleted.
     * @param int $pageId
     * @return int number of pages deleted
     */
    public static function removeDeletedPage($pageId)
    {
        return Model::removeDeletedPage($pageId);
    }

    /**
     * Recovery all deleted pages
     *
     * @return int count of recovered pages
     */
    public static function recoveryTrash()
    {
        $pages = ipDb()->selectAll('page', 'id', array('isDeleted' => 1));

        $recovered = 0;

        foreach ($pages as $page) {
            $recovered += static::recoveryDeletedPage($page['id']);
        }

        return $recovered;
    }

    /**
     * Recovery deleted page and its children from the trash
     *
     * Does not recovery page if it is not deleted.
     * @param int $pageId
     * @return int number of pages recovered
     */
    public static function recoveryDeletedPage($pageId)
    {
        return Model::recoveryDeletedPage($pageId);
    }

    /**
     * @param int $pageId
     * @param int $start
     * @param int $limit
     */
    public static function getChildren($pageId, $start = null, $limit = null)
    {
        return Model::getChildren($pageId, $start, $limit);
    }

    /**
     * @param int $menuId
     * @param string $alias
     * @param string $title
     * @param string $layout
     * @param string $type
     */
    public static function updateMenu($menuId, $alias, $title, $layout, $type)
    {
        Model::updateMenu($menuId, $alias, $title, $layout, $type);
    }

    /**
     * @param string $languageCode
     * @param string $alias
     * @param string $title
     */
    public static function createMenu($languageCode, $alias, $title)
    {
        return Model::createMenu($languageCode, $alias, $title);
    }

    public static function trashSize()
    {
        return Model::trashSize();
    }

}

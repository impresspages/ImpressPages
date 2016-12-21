<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Pages;


class Service
{
    /**
     * Get page
     *
     * @param int $pageId
     * @return array
     */
    public static function getPage($pageId)
    {
        return Model::getPage($pageId);
    }

    /**
     * Get page by URL
     *
     * @param string $languageCode
     * @param string $urlPath
     * @return \Ip\Page
     */
    public static function getPageByUrl($languageCode, $urlPath)
    {
        return Model::getPageByUrl($languageCode, $urlPath);
    }

    /**
     * Get menu
     *
     * @param string $languageCode
     * @param string $alias
     * @return array|null
     */
    public static function getMenu($languageCode, $alias)
    {
        return Model::getMenu($languageCode, $alias);
    }

    /**
     * Get menus
     *
     * @param string $languageCode
     * @return array
     */
    public static function getMenus($languageCode)
    {
        return Model::getMenuList($languageCode);
    }

    /**
     * Update properties of page
     *
     * @param int $pageId
     * @param array $data
     */
    public static function updatePage($pageId, $data)
    {
        Model::updatePageProperties($pageId, $data);
    }

    /**
     * Insert new page
     *
     * @param int $parentId
     * @param string $title
     * @param array $data
     * @return int page id
     * @throws \Ip\Exception
     */
    public static function addPage($parentId, $title, $data = [])
    {
        $data['title'] = $title;

        if (!isset($data['createdAt'])) {
            $data['createdAt'] = date('Y-m-d H:i:s');
        }
        if (!isset($data['updatedAt'])) {
            $data['updatedAt'] = date('Y-m-d H:i:s');
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
     * Copy page
     *
     * @param int $pageId
     * @param int $destinationParentId
     * @param int $destinationPosition
     * @return int
     */
    public static function copyPage($pageId, $destinationParentId, $destinationPosition)
    {
        return Model::copyPage($pageId, $destinationParentId, $destinationPosition);
    }

    /**
     * Move page
     *
     * @param int $pageId
     * @param int $destinationParentId
     * @param int $destinationPosition
     */
    public static function movePage($pageId, $destinationParentId, $destinationPosition)
    {
        Model::movePage($pageId, $destinationParentId, $destinationPosition);
    }

    /**
     * Delete page
     *
     * @param int $pageId
     */
    public static function deletePage($pageId)
    {
        Model::moveToTrash($pageId);
    }

    /**
     * Removes pages that were deleted before given time
     *
     * @param string $timestamp in mysql format.
     * @return int Count of deleted pages.
     */
    public static function removeDeletedBefore($timestamp)
    {
        $table = ipTable('page');

        $pages = ipDb()->fetchAll(
            "SELECT `id` FROM $table WHERE `isDeleted` = 1 AND `deletedAt` < ?",
            array($timestamp)
        );

        foreach ($pages as $page) {
            static::removeDeletedPage($page['id']);
        }
    }

    /**
     * Remove selected deleted pages
     *
     * @param $pages
     * @return int Count of deleted pages.
     */
    public static function emptyTrash($pages)
    {
        $deleted = 0;

        foreach ($pages as $page) {
            $deleted += static::removeDeletedPage($page);
        }

        return $deleted;
    }

    /**
     * Removes selected page and its children from the trash
     *
     * Does not remove page if it is not deleted.
     * @param int $pageId
     * @return int Number of pages deleted.
     */
    public static function removeDeletedPage($pageId)
    {
        return Model::removeDeletedPage($pageId);
    }

    /**
     * Recovery selected deleted pages
     *
     * @param $pages
     * @return int Count of recovered pages.
     */
    public static function recoveryTrash($pages)
    {
        $recovered = 0;

        foreach ($pages as $page) {
            $recovered += static::recoveryDeletedPage($page);
        }

        return $recovered;
    }

    /**
     * Recovery deleted page and its children from the trash
     *
     * Does not recovery page if it is not deleted.
     * @param int $pageId
     * @return int Number of pages recovered.
     */
    public static function recoveryDeletedPage($pageId)
    {
        return Model::recoveryDeletedPage($pageId);
    }

    /**
     * Get children
     *
     * @param int $pageId
     * @param int $start
     * @param int $limit
     * @return array
     */
    public static function getChildren($pageId, $start = null, $limit = null)
    {
        return Model::getChildren($pageId, $start, $limit);
    }

    /**
     * Update menu
     *
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
     * @param $languageCode
     * @param $alias
     * @param $title
     * @param string $type
     * @return string
     */
    public static function createMenu($languageCode, $alias, $title, $type = 'tree')
    {
        return Model::createMenu($languageCode, $alias, $title, $type);
    }

    /**
     * Trash Size
     *
     * @return int Number of deleted pages.
     */
    public static function trashSize()
    {
        return Model::trashSize();
    }

}

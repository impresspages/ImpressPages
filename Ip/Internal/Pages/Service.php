<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Internal\Pages;





class Service
{
    public static function getPage($pageId)
    {
        return Model::getPage($pageId);
    }

    public static function getMenu($languageCode, $alias)
    {
        return Model::getMenu($languageCode, $alias);
    }


    public static function getMenus($languageCode)
    {
        return Model::getMenus($languageCode);
    }





    /**
     * @param int $pageId
     * @param array $data
     */
    public static function updatePage($pageId, $data)
    {
        Model::updatePageProperties($pageId, $data);
    }

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

    public static function copyPage($pageId, $destinationParentId, $destinationPosition)
    {
        return Model::copyPage($pageId, $destinationParentId, $destinationPosition);
    }


    public static function movePage($pageId, $destinationParentId, $destinationPosition)
    {
        Model::movePage($pageId, $destinationParentId, $destinationPosition);
    }

    public static function deletePage($pageId)
    {
        Model::deletePage($pageId);
    }

    public static function getChildren($pageId, $start = null, $limit = null)
    {
        return Model::getChildren($pageId, $start, $limit);
    }

    public static function updateMenu($menuId, $alias, $title, $layout, $type)
    {
        Model::updateMenu($menuId, $alias, $title, $layout, $type);
    }

    public static function createMenu($languageCode, $alias, $title)
    {
        return Model::createMenu($languageCode, $alias, $title);
    }




}

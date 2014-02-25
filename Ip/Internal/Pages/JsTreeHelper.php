<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Pages;


class JsTreeHelper
{

    public static function getPageTree($languageCode, $parentId)
    {
        $answer = self::getList($languageCode, $parentId);
        return $answer;
    }

    /**
     * @param $languageCode
     * @param \Ip\Page[] $pages
     * @return array
     */
    protected static function getList ($languageCode, $parentId)
    {
        $pages = ipDb()->selectAll('page', '*', array('parentId' => $parentId), 'ORDER BY `pageOrder`');

        $answer = array();

        //generate jsTree response array
        foreach ($pages as $page) {

            $pageData = array();

            $pageData['state'] = 'closed';

            $jsTreeId = 'page_' . $page['id'];

            if (!empty($_SESSION['Pages.nodeOpen'][$jsTreeId])) {
                $pageData['state'] = 'open';
            }

            $children = self::getList($languageCode, $page['id']);
            if (count($children) === 0) {
                $pageData['children'] = false;
                $pageData['state'] = 'leaf';
            }
            $pageData['children'] = $children;


            if ($page['isVisible']) {
                $icon = '';
            } else {
                $icon = ipFileUrl('Ip/Internal/Pages/assets/img/file_hidden.png');
            }

            $pageData['attr'] = array('id' => $jsTreeId, 'rel' => 'page', 'languageId' => $languageCode, 'pageId' => $page['id']);
            $pageData['data'] = array ('title' => $page['title'] . '', 'icon' => $icon); //transform null into empty string. Null break JStree into infinite loop
            $answer[] = $pageData;
        }

        return $answer;
    }

    protected static function _jsTreeId($languageId, $pageId)
    {
        return 'page_' . $languageId . '_'. $pageId;
    }


}

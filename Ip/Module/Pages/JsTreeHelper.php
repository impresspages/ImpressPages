<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Module\Pages;


class JsTreeHelper
{

    public static function getPageTree($languageId, $zoneName)
    {
        $answer = self::getList($languageId, $zoneName, null);
        return $answer;
    }

    /**
     * @param $languageId
     * @param $zoneName
     * @param \Ip\Page[] $pages
     * @return array
     */
    protected static function getList ($languageId, $zoneName, $parentId = null)
    {
        $zone = ipContent()->getzone($zoneName);
        $pages = $zone->getPages($languageId, $parentId);

        $answer = array();

        //generate jsTree response array
        foreach ($pages as $page) {

            $pageData = array();

            $state = 'closed';

            $jsTreeId = self::_jsTreeId($languageId, $zoneName, $page->getId());
            if (!empty($_SESSION['Pages.nodeOpen'][$jsTreeId])) {
                $state = 'open';
                $children = self::getList($languageId, $zoneName, $page->getId());
                if (count($children) === 0) {
                    $children = false;
                    $state = 'leaf';
                }
                $pageData['children'] = $children;
            }

            if ($page['visible']) {
                $icon = '';
            } else {
                $icon = ipFileUrl('Ip/Module/Pages/img/file_hidden.png');
            }




            $answer[] = array (
                'attr' => array('id' => $jsTreeId, 'rel' => 'page', 'languageId' => $languageId, 'zoneName' => $zoneName, 'pageId' => $page->getId()),
                'data' => array ('title' => $page['title'] . '', 'icon' => $icon), //transform null into empty string. Null break JStree into infinite loop
                'state' => $state,
                'children' => $children
            );
        }

        return $answer;
    }

    protected static function _jsTreeId($languageId, $zoneName, $pageId)
    {
        return 'page_' . $languageId . '_' . $zoneName . '_' . $pageId;
    }


}
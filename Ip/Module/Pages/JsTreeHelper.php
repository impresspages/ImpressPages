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

            $pageData['state'] = 'closed';

            $jsTreeId = self::_jsTreeId($languageId, $zoneName, $page->getId());
            if (!empty($_SESSION['Pages.nodeOpen'][$jsTreeId])) {
                $pageData['state'] = 'open';
            }

            $children = self::getList($languageId, $zoneName, $page->getId());
            if (count($children) === 0) {
                $pageData['children'] = false;
                $pageData['state'] = 'leaf';
            }
            $pageData['children'] = $children;


            if ($page->isVisible()) {
                $icon = '';
            } else {
                $icon = ipFileUrl('Ip/Module/Pages/img/file_hidden.png');
            }


            $pageData['attr'] = array('id' => $jsTreeId, 'rel' => 'page', 'languageId' => $languageId, 'zoneName' => $zoneName, 'pageId' => $page->getId());
            $pageData['data'] = array ('title' => $page->getButtonTitle() . '', 'icon' => $icon); //transform null into empty string. Null break JStree into infinite loop
            $answer[] = $pageData;
        }

        return $answer;
    }

    protected static function _jsTreeId($languageId, $zoneName, $pageId)
    {
        return 'page_' . $languageId . '_' . $zoneName . '_' . $pageId;
    }


}
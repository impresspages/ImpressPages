<?php


namespace Ip\Internal\Content;


class Job
{
    /**
     * @param $info
     * @return array|null
     * @throws \Ip\Exception
     */
    public static function ipRouteAction_70($info)
    {
        if ($info['relativeUri'] == '') {
            $pageId = ipJob('ipDefaultPageId');
        } else {
            $languageCode = ipContent()->getCurrentLanguage()->getCode();
            $pageId = ipDb()->selectValue('page', 'id',
                array(
                    'urlPath' => $info['relativeUri'],
                    'languageCode' => $languageCode,
                    'isVisible' => 1,
                )
            );
        }



        if (!$pageId) {
            return null;
        }

        $result['page'] = new \Ip\Page($pageId, 'page');
        $result['plugin'] = 'Content';
        $result['controller'] = 'PublicController';
        $result['action'] = 'index';
        $result['urlParts'] = isset($urlParts[1]) ? explode('/', $urlParts[1]) : array();

        return $result;
    }

    public static function ipDefaultPageId_70()
    {
        $languageCode = ipContent()->getCurrentLanguage()->getCode();
        $defaultPageId = ipGetOption('Config.defaultPageId_' . $languageCode, null);

        if ($defaultPageId) {
            return $defaultPageId;
        }


        $menus = \Ip\Internal\Pages\Service::getMenus($languageCode);


        foreach($menus as $menu) {
            $pages = \Ip\Internal\Pages\Service::getChildren($menu['id'], 0, 1);
            if (!empty($pages[0]['id'])) {
                return $pages[0]['id'];
            }
        }


    }
}

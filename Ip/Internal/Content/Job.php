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
        $languageCode = ipCurrentPage()->getLanguage()->getCode();
        $pageId = ipDb()->selectValue('page', 'id',
            array(
                'url' => $info['relativeUri'],
                'languageCode' => $languageCode,
                'isVisible' => 1,
            )
        );

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
} 

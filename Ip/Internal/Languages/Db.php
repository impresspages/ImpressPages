<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Internal\Languages;

class Db
{


    public static function getLanguageById($id)
    {
        return ipDb()->selectRow('language', '*', array('id' => $id));
    }

    public static function newUrl($preferredUrl)
    {
        $suffix = '';
        $url = ipDb()->selectAll('language', 'id', array('url' => $preferredUrl . $suffix));
        if (empty($url)) {
            return $preferredUrl;
        }

        while (!empty($url)) {
            $suffix++;
            $url = ipDb()->selectAll('language', 'id', array('url' => $preferredUrl . $suffix));
        }

        return $preferredUrl . $suffix;
    }


}


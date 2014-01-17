<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Internal\Languages;

class Db {


    public static function getLanguageById($id) {
        $result = ipDb()->selectAll('*', 'language', array('id' => $id), 'LIMIT 1');
        if (isset($result[0])) {
            return $result[0];
        }
        return false;
    }



    
    public static function newUrl($preferredUrl) {
        $suffix = '';
        $url = ipDb()->selectAll('id', 'language', array('url' => $preferredUrl . $suffix));
        if (empty($url)) {
            return $preferredUrl;
        }

        while(!empty($url)) {
            $suffix++;
            $url = ipDb()->selectAll('id', 'language', array('url' => $preferredUrl . $suffix));
        }

        return $preferredUrl . $suffix;
    }




}


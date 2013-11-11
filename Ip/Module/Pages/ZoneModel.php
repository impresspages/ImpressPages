<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Module\Pages;

class ZoneModel{

    public static function updateZone($languageId, $zoneId, $data) {
        $site = \Ip\ServiceLocator::getSite();
        $dispatcher = \Ip\ServiceLocator::getDispatcher();

        $condition = array(
            'language_id' => $languageId,
            'zone_id' => $zoneId
        );

        $parameter = ZoneModel::getParameter($zoneId, $languageId);

        if (ZoneModel::isUrlUsed($languageId, $data['url'], $parameter['id'])) {
            throw new DuplicateUrlException($data['url']);
        }


        $parameter = self::getParameter($zoneId, $languageId);
        $originalUrl = $parameter['url'];

        \Ip\Db::update(DB_PREF . 'zone_parameter', $data, $condition);

        $newUrl = $data['url'];

        $language = $site->getLanguageById($languageId);

        if ($originalUrl != $newUrl){
            $fullOldUrl = self::makeUrl($language->getUrl(), $originalUrl);
            $fullNewUrl = self::makeUrl($language->getUrl(), $newUrl);
            $dispatcher->notify(new \Ip\Event\UrlChanged(null, $fullOldUrl, $fullNewUrl));
        }
    }


    private static function makeUrl($languageUrl, $zoneUrl){
        $parametersMod = \Ip\ServiceLocator::getParametersMod();
        if ($parametersMod->getValue('Config.multilingual')) {
            $answer = \Ip\Config::baseUrl(urlencode($languageUrl).'/'.urlencode($zoneUrl).'/');
        } else {
            $answer = \Ip\Config::baseUrl(urlencode($zoneUrl).'/');
        }
        return $answer;
    }


    public static function getParameter($zoneId, $languageId){
        $sql = "select * from `".DB_PREF."zone_parameter` where `zone_id` = ".(int)$zoneId." and `language_id` = ".(int)$languageId." ";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs){
            $answer = false;
            if($lock = ip_deprecated_mysql_fetch_assoc($rs)){
                $answer = $lock;
            }
            return $answer;
        } else {
            trigger_error($sql." ".ip_deprecated_mysql_error());
        }
        return false;
    }



    public static function isUrlUsed($languageId, $url, $allowedId = null){
        $sql = '
        SELECT
            1
        FROM
            ' . DB_PREF . 'zone_parameter
        WHERE
            url = :url
            and
            language_id = :languageId
            and
            id != :allowedId';

        $params = array(
            'url' => $url,
            'languageId' => (int) $languageId,
            'allowedId' => (int) $allowedId
        );

        $result = \Ip\Db::fetchRow($sql, $params);
        return $result;
    }


}


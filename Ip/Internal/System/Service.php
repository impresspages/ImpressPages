<?php
/**
 * @package   ImpressPages
 */


/**
 * Created by PhpStorm.
 * User: maskas
 * Date: 6/6/14
 * Time: 4:48 PM
 */

namespace Ip\Internal\System;


class Service
{

    public static function updateLinks()
    {
        $model = Model::instance();
        $oldUrl = $model->getOldUrl();
        $newUrl = $model->getNewUrl();

        $httpExpression = '/^((http|https):\/\/)/i';

        if ($oldUrl != $newUrl && preg_match($httpExpression, $oldUrl) && preg_match($httpExpression, $newUrl)) {
            $eventData = array(
                'oldUrl' => $oldUrl,
                'newUrl' => $newUrl
            );
            ipEvent('ipUrlChanged', $eventData);
            ipStorage()->set('Ip', 'cachedBaseUrl', $newUrl);
        }

    }

    public static function urlHasChanged()
    {
        $model = Model::instance();
        $oldUrl = $model->getOldUrl();
        $newUrl = $model->getNewUrl();
        if ($oldUrl != $newUrl) {
            return true;
        } else {
            return false;
        }
    }

    public static function clearCache()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipEvent('ipCacheClear');
    }

}

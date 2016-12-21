<?php
/**
 * Created by PhpStorm.
 * User: maskas
 * Date: 16.12.4
 * Time: 16.52
 */

namespace Ip\Internal\Install;


use Ip\Language;

class Job
{

    public static function ipRouteAction_0($info)
    {
        if (!ipConfig()->isEmpty()) {
            return null;
        }

        $route = [
            'plugin' => 'Install',
            'controller' => 'PublicController',
            'action' => 'index'
        ];

        if (!empty(ipRequest()->getRequest('pa'))) {
            $actionParts = explode('.', ipRequest()->getRequest('pa'));
            if (!empty($actionParts[1])) {
                $route['action'] = $actionParts[1];
            }
        }
        return $route;
    }

    public static function ipRouteLanguage_0($info)
    {
        if (!ipConfig()->isEmpty()) {
            return null;
        }

        $code = 'en';
        if (!empty($_SESSION['installationLanguage'])) {
            $code = $_SESSION['installationLanguage'];
        }
        if (!empty(ipRequest()->getQuery('lang'))) {
            $code = ipRequest()->getQuery('lang');
        }
        $_SESSION['installationLanguage'] = $code;
        return [
            'relativeUri' => '',
            'language' => new Language(null, $code, $code, $code, $code, '1', 'ltr')
            ];
    }
}
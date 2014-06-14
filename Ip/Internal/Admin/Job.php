<?php


namespace Ip\Internal\Admin;


class Job
{
    public static function ipAdminLoginPrevent($data)
    {
        if (empty($data['username'])) {
            return 'Missing login data'; //in theory should never happen
        }

        $ip = ipRequest()->getServer('REMOTE_ADDR');

        $antiBruteForce = SecurityModel::instance();
        $failedLogins = $antiBruteForce->failedLoginCount($data['username'], $ip);
        if ($failedLogins > ipGetOption('Admin.allowFailedLogins', 20)) {
            return __('You have exceeded failed login attempts.', 'Ip-admin', false);
        }
        return null;
    }

    public static function ipRouteAction_5($info)
    {
        if (Model::isLoginPage($info['request'])) {
            return array(
                'plugin' => 'Admin',
                'controller' => 'SiteController',
                'action' => 'login',
            );
        }
        return null;
    }

}

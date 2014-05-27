<?php

/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Internal\System;


class Model
{

    protected static $instance;

    protected function __construct()
    {

    }

    protected function __clone()
    {

    }

    /**
     * Get singleton instance
     *
     * @return Model
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new Model();
        }

        return self::$instance;
    }

    public function getOldUrl()
    {
        return ipStorage()->get('Ip', 'cachedBaseUrl');
    }

    /**
     * @return string
     */
    public function getNewUrl()
    {
        return ipConfig()->baseUrl();
    }

    /**
     * @return string
     */
    public function getImpressPagesAPIUrl()
    {
        if (ipConfig()->get('testMode')) {
            return 'http://test.service.impresspages.org';
        } else {
            return 'http://service.impresspages.org';
        }
    }

    /**
     * @return array|string
     */
    public static function getIpNotifications()
    {
        if (!function_exists('curl_init')) {
            return array();
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, \Ip\Internal\System\Model::instance()->getImpressPagesAPIUrl());
        curl_setopt($ch, CURLOPT_POST, 1);

        $postFields = 'module_group=service&module_name=communication&action=getInfo&version=1&afterLogin=';
        $postFields .= '&systemVersion=' . \Ip\ServiceLocator::storage()->get('Ip', 'version');

        $plugins = \Ip\Internal\Plugins\Model::getActivePlugins();
        foreach ($plugins as $plugin) {
            $postFields .= '&plugins[' . $plugin['name'] . ']=' . $plugin['version'];
        }

        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_REFERER, ipConfig()->baseUrl());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $answer = curl_exec($ch);
        $notices = json_decode($answer);

        if (!is_array($notices)) { // json decode error or wrong answer
            ipLog()->error('System.updateCheckInvalidResponse',
                array(
                    'curl_error' => curl_error($ch),
                    'response' => $answer
                ));
            return array();
        }

        return $notices;
    }

    public static function sendUsageStatistics($data = array())
    {
        if (!function_exists('curl_init')) {
            return;
        }

        if (!isset($data['action'])) { $data['action'] = 'Ping.default'; }
        if (!isset($data['data'])) { $data['data'] = array(); }
        if (!isset($data['websiteId'])) { $data['websiteId'] = ipStorage()->get('Ip', 'websiteId'); }
        if (!isset($data['websiteUrl'])) { $data['websiteUrl'] = ipConfig()->baseUrl(); }
        if (!isset($data['version'])) { $data['version'] = \Ip\Application::getVersion(); }
        if (!isset($data['doSupport'])) { $data['doSupport'] = ipStorage()->get('Ip', 'getImpressPagesSupport'); }
        if (!isset($data['administrators'])) {
            $administrators = \Ip\Internal\Administrators\Model::getAll();
            $adminCollection = array();
            foreach ($administrators as $admin) {
                $permissions = \Ip\Internal\AdminPermissionsModel::getUserPermissions($admin['id']);
                $adminCollection[] = array(
                    'id' => $admin['id'],
                    'email' => $admin['email'],
                    'permissions' => $permissions
                );
            }
            $data['administrators'] = $adminCollection;
        }

        $postFields = 'data=' . urlencode(serialize($data));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://local.impresspages.org/?pa=UsageStatistics.getData');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
//        curl_setopt($ch, CURLOPT_REFERER, ipConfig()->baseUrl());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $answer = curl_exec($ch);
    }
}

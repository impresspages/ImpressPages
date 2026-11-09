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
        return ipConfig()->get('serviceUrl', 'http://service.impresspages.org/');
    }

    /**
     * @return array|string
     */
    public static function getIpNotifications()
    {
        if (!function_exists('curl_init')) {
            return [];
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, \Ip\Internal\System\Model::instance()->getImpressPagesAPIUrl());
        curl_setopt($ch, CURLOPT_POST, 1);

        $postFields = 'module_group=service&module_name=communication&action=getInfo&version=1&afterLogin=';
        $postFields .= '&systemVersion=' . \Ip\ServiceLocator::storage()->get('Ip', 'version');
        $postFields .= '&phpVersion=' . phpversion();
        $postFields .= '&bootstrapType=' . self::bootstrapType();

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
            ipLog()->error(
                'System.updateCheckInvalidResponse',
                array(
                    'curl_error' => curl_error($ch),
                    'response' => $answer
                )
            );
            return [];
        }

        return $notices;
    }

    private static function bootstrapType() {
        if (!is_file('index.php')) {
            return 'unknown';
        }
        $indexContent = file_get_contents('index.php');
        if (strpos($indexContent, 'Ip/script/run.php') !== false) {
            return 'Ip/script/run.php';
        }
        return 'unknown';
    }

    public static function sendUsageStatistics($data = [], $timeout = 3)
    {
        // The impresspages.org statistics service is gone; kept as a no-op for plugin compatibility.
    }
}

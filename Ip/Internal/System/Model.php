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
        if (!function_exists('curl_init')) {
            return;
        }

        if (!isset($data['action'])) {
            $data['action'] = 'Ping.default';
        }
        if (!isset($data['php'])) {
            $data['php'] = phpversion();
        }
        if (!isset($data['db'])) {
            $data['db'] = null;
            // todo: make a db type/version check stable to work during install and later on
//            if (class_exists('PDO')) {
//                $pdo = ipDb()->getConnection();
//                if ($pdo) {
//                    $data['db'] = $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);
//                }
//            }
        }
        if (!isset($data['developmentEnvironment'])) {
            $data['developmentEnvironment'] = ipConfig()->get('developmentEnvironment');
        }
        if (!isset($data['showErrors'])) {
            $data['showErrors'] = ipConfig()->get('showErrors');
        }
        if (!isset($data['debugMode'])) {
            $data['debugMode'] = ipConfig()->get('debugMode');
        }
        if (!isset($data['timezone'])) {
            $data['timezone'] = ipConfig()->get('timezone');
        }
        if (!isset($data['data'])) {
            $data['data'] = [];
        }
        if (!isset($data['websiteId'])) {
            $data['websiteId'] = ipStorage()->get('Ip', 'websiteId');
        }
        if (!isset($data['websiteUrl'])) {
            $data['websiteUrl'] = ipConfig()->baseUrl();
        }
        if (!isset($data['version'])) {
            $data['version'] = \Ip\Application::getVersion();
        }
        if (!isset($data['locale'])) {
            $data['locale'] = \Ip\ServiceLocator::translator()->getAdminLocale();
        }
        if (!isset($data['doSupport'])) {
            $data['doSupport'] = ipStorage()->get('Ip', 'getImpressPagesSupport');
        }
        if (!isset($data['administrators'])) {
            $administrators = \Ip\Internal\Administrators\Model::getAll();
            $adminCollection = [];
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
        if (!isset($data['themes'])) {
            $data['themes'] = array(
                'active' => ipConfig()->theme(),
                'all' => \Ip\Internal\Design\Model::instance()->getAvailableThemes()
            );
        }
        if (!isset($data['plugins'])) {
            $plugins = \Ip\Internal\Plugins\Model::getAllPluginNames();
            $activePlugins = \Ip\Internal\Plugins\Service::getActivePluginNames();
            $pluginCollection = [];
            foreach ($plugins as $pluginName) {
                $pluginCollection[] = array(
                    'name' => $pluginName,
                    'active' => in_array($pluginName, $activePlugins) ? true : false
                );
            }
            $data['plugins'] = $pluginCollection;
        }
        if (!isset($data['languages'])) {
            $data['languages'] = ipContent()->getLanguages();
        }
        if (!isset($data['pages'])) {
            $result = [];
            try {
                $table = ipTable('page');
                $sql = "
                    SELECT
                        `languageCode` AS `language`, COUNT( 1 ) AS `quantity`
                    FROM
                        $table
                    GROUP BY
                        `languageCode`
                ";
                $result = ipDb()->fetchAll($sql);
            } catch (\Exception $e) {
                // Do nothing.
            }
            $data['pages'] = $result;
        }

        $postFields = 'data=' . urlencode(serialize($data));

        // Use sockets instead of CURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, ipConfig()->get('usageStatisticsUrl', 'http://service.impresspages.org/stats'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
//        curl_setopt($ch, CURLOPT_REFERER, ipConfig()->baseUrl());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_exec($ch);
    }
}

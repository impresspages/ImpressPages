<?php
namespace Ip\Internal\Plugins;


class Model{

    public static function getModules()
    {
        return array(
            "Content",
            "Pages",
            "Admin",
            "Design",
            "Plugins",
            "Log",
            "Email",
            "Config",
            "Breadcrumb",
            "Repository",
            "Upload",
            "InlineManagement",
            "Languages",
            "Wizard",
            "Form",
            "Cron",
            "Ip",
            "Translations",
            "System",
        );
    }

    public static function activatePlugin($pluginName)
    {
        $activePlugins = self::getActivePlugins();
        if (in_array($pluginName, $activePlugins)) {
            //already activated
            return true;
        }

        $pluginRecord = self::getPluginRecord($pluginName);

        $config = Model::getPluginConfig($pluginName);

        if (!$config) {
            // TODOXX Plugin dir #141
            // throw new \Ip\Exception(BASE_DIR . PLUGIN_DIR . $pluginName . "/Setup/plugin.json doesn't exist", \Ip\Exception::PLUGIN_SETUP);
        }

        if (empty($config['name']) || $config['name'] !== $pluginName) {
            // TODOXX Plugin dir #141
            // throw new \Ip\Exception('Plugin name setting in ' . BASE_DIR . PLUGIN_DIR . "Setup/plugin.json doesn't match the folder name.", \Ip\Exception::PLUGIN_SETUP);
        }

        if (empty($config['version'])) {
            // TODOXX Plugin dir #141
            // throw new \Ip\Exception('Missing plugin version number in ' . BASE_DIR . PLUGIN_DIR . "Setup/plugin.json file.", \Ip\Exception::PLUGIN_SETUP);
        }

        if (!empty($pluginRecord['version']) && (float) $pluginRecord['version'] > (float) $config['version']) {
            throw new \Ip\Exception\Plugin\Setup("You can't downgrade the plugin. Please remove the plugin completely and reinstall if you want to use older version.");
        }

        $workerClass = 'Plugin\\' . $pluginName . '\\Setup\\Worker';
        if (method_exists($workerClass, 'activate')) {
            $worker = new $workerClass($config['version']);
            $worker->activate();
        }


        $dbh = ipDb()->getConnection();
        $sql = '
        INSERT INTO
            ' . ipTable('plugin') . '
        SET
            `name` = :pluginName,
            `active` = 1,
            `version` = :version
        ON DUPLICATE KEY UPDATE
            `active` = 1,
            `version` = :version
        ';

        $params = array (
            'pluginName' => $pluginName,
            'version' => $config['version']
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);

        ipLog()->info('Ip.pluginActivated: {plugin} {version} activated.', array('plugin' => $pluginName, 'version' => $config['version']));

        ipDispatcher()->notify('ipPluginActivated', array(
                'name' => $pluginName,
                'version' => $config['version'],
            ));
    }

    public static function deactivatePlugin($pluginName)
    {
        $activePlugins = self::getActivePlugins();
        if (!in_array($pluginName, $activePlugins)) {
            //already deactivated
            return true;
        }

        $pluginRecord = self::getPluginRecord($pluginName);
        if (!$pluginRecord) {
            //already deactivated
            return true;
        }

        $workerClass = 'Plugin\\' . $pluginName . '\\Setup\\Worker';
        if (method_exists($workerClass, 'activate')) {
            $worker = new $workerClass($pluginRecord['version']);
            $worker->deactivate();
        }

        $sql = '
        UPDATE
            ' . ipTable('plugin') . '
        SET
            `active` = 0
        WHERE
            `name` = ?
        ';

        ipDb()->execute($sql, array($pluginName));

        ipLog()->info('Ip.pluginDeactivated: {plugin} {version} deactivated.', array('plugin' => $pluginName, 'version' => $pluginRecord['version']));

        // TODO remove plugin event listeners
        ipDispatcher()->notify('ipPluginDeactivated', array(
                'name' => $pluginName,
                'version' => $pluginRecord['version'],
            ));
    }

    public static function removePlugin($pluginName)
    {
        $activePlugins = self::getActivePlugins();
        if (in_array($pluginName, $activePlugins)) {
            throw new \Ip\Exception\Plugin\Setup('Please deactivate the plugin before removing it.');

        }

        $pluginRecord = self::getPluginRecord($pluginName);
        if ($pluginRecord) {
            $version = $pluginRecord['version'];
        } else {
            $version = null;
        }

        $workerClass = 'Plugin\\' . $pluginName . '\\Setup\\Worker';
        if (method_exists($workerClass, 'remove')) {
            $worker = new $workerClass($version);
            $worker->remove();
        }

        $dbh = ipDb()->getConnection();
        $sql = '
        DELETE FROM
            ' . ipTable('plugin') . '
        WHERE
            `name` = :pluginName
        ';

        $params = array (
            'pluginName' => $pluginName
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);

        $pluginDir = ipFile('Plugin/' . $pluginName);
        try {
            $result = Helper::removeDir($pluginDir);
            if (!$result) {
                throw new \Ip\Exception\Plugin\Setup('Can\'t remove folder ' . $pluginDir);
            }
        } catch (\Ip\PhpException $e) {
            throw new \Ip\Exception\Plugin\Setup('Can\'t remove folder ' . $pluginDir);
        }

        ipLog()->info('Ip.pluginRemoved: {plugin} {version} removed.', array('plugin' => $pluginName, 'version' => $version));

        ipDispatcher()->notify('ipPluginRemoved', array(
                'name' => $pluginName,
                'version' => $version,
            ));

    }

    protected static function getPluginRecord($pluginName)
    {
        $dbh = ipDb()->getConnection();
        $sql = '
            SELECT
                *
            FROM
                ' . ipTable('plugin') . '
            WHERE
                `name` = :pluginName
        ';

        $params = array (
            'pluginName' => $pluginName
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);
        $row = $q->fetch();
        return $row;
    }

    public static function getAllPlugins()
    {
        $answer = array();
        $pluginDir = ipFile('Plugin/');
        $files = scandir($pluginDir);
        if (!$files) {
            return array();
        }
        foreach ($files as $file) {
            if (in_array($file, array('.', '..')) || !is_dir($pluginDir . $file)) {
                continue;
            }
            $answer[] = $file;
        }

        //TODO add filter for plugins in other directories
        return $answer;
    }

    public static function getActivePlugins()
    {
        $dbh = ipDb()->getConnection();
        $sql = '
            SELECT
                `name`
            FROM
                ' . ipTable('plugin') . '
            WHERE
                `active`
        ';

        $params = array ();
        $q = $dbh->prepare($sql);
        $q->execute($params);
        $data = $q->fetchAll(\PDO::FETCH_COLUMN); //fetch all rows as an array
        return $data;
    }

    public static function getPluginConfig($pluginName)
    {
        $configFile = ipFile('Plugin/' . $pluginName . '/Setup/plugin.json' );
        if (!is_file($configFile)) {
            return array();
        }

        $configJson = file_get_contents($configFile);

        $config = Helper::jsonCleanDecode($configJson, true);
        if ($config) {
            return $config;
        } else {
            return array();
        }
    }
}


<?php
namespace Ip\Module\Plugins;


class Model{

    public static function getModules()
    {
        return array(
            "Content",
            "Pages",
            "Admin",
            "Design",
            "Plugins",
            "System",
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
            "Translations"
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
            // TODOX Plugin dir
            // throw new \Ip\CoreException(BASE_DIR . PLUGIN_DIR . $pluginName . "/Setup/plugin.json doesn't exist", \Ip\CoreException::PLUGIN_SETUP);
        }

        if (empty($config['name']) || $config['name'] !== $pluginName) {
            // TODOX Plugin dir
            // throw new \Ip\CoreException('Plugin name setting in ' . BASE_DIR . PLUGIN_DIR . "Setup/plugin.json doesn't match the folder name.", \Ip\CoreException::PLUGIN_SETUP);
        }

        if (empty($config['version'])) {
            // TODOX Plugin dir
            // throw new \Ip\CoreException('Missing plugin version number in ' . BASE_DIR . PLUGIN_DIR . "Setup/plugin.json file.", \Ip\CoreException::PLUGIN_SETUP);
        }

        if (!empty($pluginRecord['version']) && (float) $pluginRecord['version'] > (float) $config['version']) {
            throw new \Ip\CoreException("You can't downgrade the plugin. Please remove the plugin completely and reinstall if you want to use older version.", \Ip\CoreException::PLUGIN_SETUP);
        }

        $workerClass = 'Plugin\\' . $pluginName . '\\Setup\\Worker';
        if (method_exists($workerClass, 'activate')) {
            $worker = new $workerClass($config['version']);
            $worker->activate();
        }


        $dbh = ipDb()->getConnection();
        $sql = '
        INSERT INTO
            `'.DB_PREF.'plugin`
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

        ipLog()->info('Plugin.activate: {plugin} {version} activated.', array('plugin' => $pluginName, 'version' => $config['version']));

        ipDispatcher()->notify('Plugin.activate', array(
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
            `'.DB_PREF.'plugin`
        SET
            `active` = 0
        WHERE
            `name` = ?
        ';

        ipDb()->execute($sql, array($pluginName));

        ipLog()->info('Plugin.deactivate: {plugin} {version} deactivated.', array('plugin' => $pluginName, 'version' => $pluginRecord['version']));

        // TODOX remove plugin event listeners
        ipDispatcher()->notify('Plugin.deactivate', array(
                'name' => $pluginName,
                'version' => $pluginRecord['version'],
            ));
    }

    public static function removePlugin($pluginName)
    {
        $activePlugins = self::getActivePlugins();
        if (in_array($pluginName, $activePlugins)) {
            throw new \Ip\CoreException('Please deactivate the plugin before removing it.', \Ip\CoreException::PLUGIN_SETUP);

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
            `'.DB_PREF.'plugin`
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
                throw new \Ip\CoreException('Can\'t remove folder ' . $pluginDir, \Ip\CoreException::PLUGIN_SETUP);
            }
        } catch (\Ip\PhpException $e) {
            throw new \Ip\CoreException('Can\'t remove folder ' . $pluginDir, \Ip\CoreException::PLUGIN_SETUP);
        }

        ipLog()->info('Plugin.remove: {plugin} {version} removed.', array('plugin' => $pluginName, 'version' => $version));

        ipDispatcher()->notify('Plugin.remove', array(
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
                `'.DB_PREF.'plugin`
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

        // TODOX add filter for plugins in other directories
        return $answer;
    }

    public static function getActivePlugins()
    {
        $dbh = ipDb()->getConnection();
        $sql = '
            SELECT
                `name`
            FROM
                `'.DB_PREF.'plugin`
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


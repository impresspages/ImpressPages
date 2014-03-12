<?php
/**
 * @package   ImpressPages
 */



namespace Ip\Internal\Update;



class Model {

    public static function migrationsAvailable()
    {
        $curDbVersion = ipStorage()->get('Ip', 'dbVersion');
        return $curDbVersion < ipApplication()->getDbVersion();
    }

    public static function runMigrations()
    {
        $curDbVersion = ipStorage()->get('Ip', 'dbVersion');
        for($i = $curDbVersion + 1; $i <= ipApplication()->getDbVersion(); $i++) {
            $migrationMethod = 'update_' . $i;
            if (method_exists(__NAMESPACE__ . '\Migration', $migrationMethod)) {
                Migration::$migrationMethod();
            }
            ipStorage()->set('Ip', 'dbVersion', $i);
        }
    }
}

<?php
/**
 * @package   ImpressPages
 */



namespace Ip\Internal\Update;



class Model {
    const DB_VERSION = 2;

    public static function migrationsAvailable()
    {
        $curDbVersion = ipStorage()->get('Ip', 'dbVersion');
        return $curDbVersion < self::DB_VERSION;
    }

    public static function runMigrations()
    {
        $curDbVersion = ipStorage()->get('Ip', 'dbVersion');
        for($i = $curDbVersion + 1; $i <= self::DB_VERSION; $i++) {
            $migrationMethod = 'update_' . $i;
            if (method_exists(__NAMESPACE__ . '\Migration', $migrationMethod)) {
                Migration::$migrationMethod();
            }
            ipStorage()->set('Ip', 'dbVersion', $i);
        }
    }
}

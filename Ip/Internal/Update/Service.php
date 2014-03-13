<?php
/**
 * @package   ImpressPages
 */




namespace Ip\Internal\Update;


class Service {
    public static function migrationsAvailable()
    {
        return Model::migrationsAvailable();
    }
}

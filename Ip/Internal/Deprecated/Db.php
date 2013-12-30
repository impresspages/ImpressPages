<?php
/**
 * @package ImpressPages
 *
 *
 */


namespace Ip\Internal\Deprecated;

/**
 * Main db class
 * Connects to database, provide some general functions.
 * @package ImpressPages
 */
class Db
{




    public static function addPermissions($userId, $moduleId)
    {
        ipDb()->insert('user_to_mod', array(
                'userId' => $userId,
                'moduleId' => $moduleId,
            ));
    }

    public static function getAllUsers()
    {
        return ipDb()->select('*', 'user');
    }

    //end parameters

}
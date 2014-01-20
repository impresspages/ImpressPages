<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Internal\Administrators;




class Service
{
    public static function add($username, $email, $password)
    {
        Model::addAdministrator($username, $email, $password);
    }



    public static function getByUsername($username)
    {
        return Model::getByUsername($username);
    }

    public static function getByEmail($email)
    {
        return Model::getByEmail($email);
    }

    public static function checkPassword($userId, $password)
    {
        return Model::checkPassword($userId, $password);
    }


    public static function resetPassword($userId)
    {
        Model::resetPassword($userId);
    }

}

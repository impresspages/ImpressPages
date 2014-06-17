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
        return Model::addAdministrator($username, $email, $password);
    }

    public static function get($userId)
    {
        return Model::get($userId);
    }

    public static function getByUsername($username)
    {
        return Model::getByUsername($username);
    }


    public static function getById($id)
    {
        return Model::getById($id);
    }

    public static function getByEmail($email)
    {
        return Model::getByEmail($email);
    }

    public static function checkPassword($userId, $password)
    {
        return Model::checkPassword($userId, $password);
    }

    public static function sendResetPasswordLink($userId)
    {
        Model::sendResetPasswordLink($userId);
    }

    public static function setUserPassword($userId, $password)
    {
        Model::setUserPassword($userId, $password);
    }

    public static function update($userId, $username, $email, $password)
    {
        Model::update($userId, $username, $email, $password);
    }

    public static function resetPassword($userId, $secret, $password)
    {
        Model::resetPassword($userId, $secret, $password);
    }


    public static function getAll()
    {
        return Model::getAll();
    }

    public static function delete($id)
    {
        Model::delete($id);
    }

}

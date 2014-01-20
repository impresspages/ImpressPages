<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Internal\Administrators;




class Service
{
    public static function addAdministrator($username, $email, $password)
    {
        Model::addAdministrator($username, $email, $password);
    }

}

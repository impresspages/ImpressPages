<?php
namespace Ip\Internal\Administrators;


class Model{

    public static function get($id)
    {
        return ipDb()->selectRow('*', 'administrator', array('id' => $id));
    }

    public static function getAll()
    {
        return ipDb()->selectAll('*', 'administrator', array(), 'ORDER BY `row_number` desc');
    }

    public static function addAdministrator($username, $email, $password)
    {
        $data = array(
            'name' => $username,
            'e_mail' => $email,
            'pass' => self::passwordHash($password)
        );
        ipDb()->insert('administrator', $data);
    }


    private static function passwordHash($password)
    {
        $stretching = ipGetOption('Admin.passwordStretchingIterations', 8);
        $hasher = new PasswordHash($stretching, FALSE);
        return $hasher->HashPassword($password);
    }

    private static function checkHash($password, $storedHash)
    {
        $hasher = new PasswordHash(8, FALSE);
        $hasher->CheckPassword($password, $storedHash);
        return $hasher->HashPassword($password);
    }

}

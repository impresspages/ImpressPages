<?php
namespace Ip\Internal\Administrators;


class Model{

    public static function get($id)
    {
        return ipDb()->selectRow('*', 'administrator', array('id' => $id));
    }

    public static function getAll()
    {
        return ipDb()->selectAll('*', 'administrator', array(), 'ORDER BY `id` desc');
    }


    public static function getByUsername($username)
    {
        return ipDb()->selectRow('*', 'administrator', array('username' => $username));
    }

    public static function getByEmail($email)
    {
        return ipDb()->selectRow('*', 'administrator', array('email' => $email));
    }

    public static function addAdministrator($username, $email, $password)
    {
        $data = array(
            'username' => $username,
            'email' => $email,
            'hash' => self::passwordHash($password)
        );
        ipDb()->insert('administrator', $data);
    }

    public static function resetPassword($userId)
    {
        $user = self::get($userId);
        if (!$user) {
            throw new \Ip\Exception("User doesn't exist");
        }


        $contentData = array (
            'link' => '//TODOX.com'
        );

        $content = ipView('view/passwordResetContent.php', $contentData)->render();

        $emailData = array (
            'content' => $content
        );

        $email = ipView('view/passwordResetEmail.php', $emailData);

        $from = ipGetOption('Config.websiteEmail');
        $fromName = ipGetOption('Config.websiteTitle');
        $subject = __('Password reset instructions', 'ipAdmin', FALSE);
        $to = $user['email'];
        $toName = $user['username'];
        ipSendEmail($from, $fromName, $to, $toName, $subject, $email);

    }

    public static function checkPassword($userId, $password)
    {
        $user = self::get($userId);
        return self::checkHash($password, $user['hash']);
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
        return $hasher->CheckPassword($password, $storedHash);
    }

}

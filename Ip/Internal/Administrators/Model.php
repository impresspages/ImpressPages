<?php
namespace Ip\Internal\Administrators;


class Model{

    public static function get($id)
    {
        return ipDb()->selectRow('administrator', '*', array('id' => $id));
    }

    public static function getAll()
    {
        return ipDb()->selectAll('administrator', '*', array(), 'ORDER BY `id` ASC');
    }

    public static function delete($id)
    {
        ipDb()->delete('administrator', array('id' => $id));
    }

    public static function getByUsername($username)
    {
        return ipDb()->selectRow('administrator', '*', array('username' => $username));
    }

    public static function getByEmail($email)
    {
        return ipDb()->selectRow('administrator', '*', array('email' => $email));
    }

    public static function addAdministrator($username, $email, $password)
    {
        $data = array(
            'username' => $username,
            'email' => $email,
            'hash' => self::passwordHash($password)
        );
        return ipDb()->insert('administrator', $data);
    }

    public static function sendResetPasswordLink($userId)
    {
        $user = self::get($userId);
        if (!$user) {
            throw new \Ip\Exception("User doesn't exist");
        }

        $urlData = array(
            'sa' => 'Admin.passwordReset',
            'id' => $userId,
            'secret' => self::generatePasswordResetSecret($userId)
        );

        $contentData = array (
            'link' => ipActionUrl($urlData)
        );
        $content = ipView('view/passwordResetContent.php', $contentData)->render();

        $emailData = array (
            'content' => $content
        );

        $email = ipEmailTemplate($emailData);

        $from = ipGetOption('Config.websiteEmail');
        $fromName = ipGetOption('Config.websiteTitle');
        $subject = __('Password reset instructions', 'Ip-admin', FALSE);
        $to = $user['email'];
        $toName = $user['username'];
        ipSendEmail($from, $fromName, $to, $toName, $subject, $email);

    }


    public static function setUserPassword($userId, $password)
    {
        ipDb()->update('administrator', array('hash' => self::passwordHash($password)), array('id' => $userId));
    }

    public static function update($userId, $username, $email, $password)
    {
        $data = array(
            'email' => $email,
            'username' => $username
        );

        if ($password) {
            $data['hash'] = self::passwordHash($password);
        }

        ipDb()->update('administrator', $data, array('id' => $userId));
    }


    public static function resetPassword($userId, $secret, $password)
    {
        $user = self::get($userId);
        if (!$user) {
            throw new \Ip\Exception(__('User doesn\'t exist', 'Ip-admin', FALSE));
        }

        if (empty($user['resetSecret']) || $user['resetTime'] < time() - ipGetOption('Config.passwordResetLinkExpire', 60 * 60 * 24)) {
            throw new \Ip\Exception(__('Invalid password reset link', 'Ip-admin', FALSE));
        }

        if ($user['resetSecret'] != $secret) {
            throw new \Ip\Exception(__('Password reset link has expired', 'Ip-admin', FALSE));
        }

        ipDb()->update('administrator', array('hash' => self::passwordHash($password)), array('id' => $userId));
    }

    private static function generatePasswordResetSecret($userId)
    {
        $secret = md5(ipConfig()->get('sessionName') . uniqid());
        $data = array(
            'resetSecret' => $secret,
            'resetTime' => time()
        );
        ipDb()->update('administrator', $data, array('id' => $userId));
        return $secret;
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

<?php
namespace Ip\Internal\Admin;


class SecurityModel
{
    protected function __construct()
    {

    }

    /**
     * @return SecurityModel
     */
    public static function instance()
    {
        return new SecurityModel();
    }


    public function registerFailedLogin($username, $ip)
    {
        $failedLogin = array(
            'username' => $username,
            'ip' => $ip,
            'time' => time()
        );
        $failedLogins = $this->failedLogins();
        $failedLogins[] = $failedLogin;
        $this->setFailedLogins($failedLogins);
    }

    public function failedLoginCount($username, $ip)
    {
        $allFailedLogins = $this->failedLogins();

        $count = 0;
        foreach ($allFailedLogins as $login) {
            if ($login['time'] > time() - 60 * 60 && $login['username'] == $username && $login['ip'] == $ip) {
                $count++;
            }
        }

        return $count;
    }


    /**
     * @return array
     */
    private function failedLogins()
    {
        $failedLogins = ipStorage()->get('Admin', 'failedLogins', []);
        return $failedLogins;
    }

    public function cleanup()
    {
        $failedLogins = $this->failedLogins();
        $filtered = [];
        foreach ($failedLogins as $login) {
            if ($login['time'] > time() - 60 * 60) {
                $filtered[] = $login;
            }
        }
        $this->setFailedLogins($filtered);
    }

    private function setFailedLogins($failedLogins)
    {
        ipStorage()->set('Admin', 'failedLogins', $failedLogins);
    }


}

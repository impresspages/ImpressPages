<?php

/**
 * @package ImpressPages
 *
 *
 */

namespace PhpUnit\Helper\User;

class Admin
{
    protected $session;
    protected $installation;

    public function __construct($session, $installation)
    {
        $this->session = $session;
        $this->installation = $installation;
    }

    public function login()
    {
        $session = $this->session;
        $installation = $this->installation;
        $installationUrl = $installation->getInstallationUrl();

        $session->visit($installationUrl . 'admin');

        $loginPage = $session->getPage();

        $loginButton = $loginPage->find('css', '.ipsLoginButton');

        $loginField = $loginPage->find('css', '.form-control[name=login]');
        $loginField->setValue($installation->getAdminLogin());

        $passwordField = $loginPage->find('css', '.form-control[name=password]');
        $passwordField->setValue($installation->getAdminPass());

        $loginButton->click();

        $session->wait(10000,"typeof $ !== 'undefined' && $('.ipActionPublish').size() > 0");
    }

    public function logout()
    {
        $session = $this->session;
        $installation = $this->installation;
        $installationUrl = $installation->getInstallationUrl();
        $session->visit($installationUrl);
        $page = $session->getPage();

        $logoutButton = $page->find('css', '.ipsAdminLogout');
        $logoutButton->click();


        $session->wait(10000,"typeof $ !== 'undefined' && $('.ipActionPublish').size() == 0 && $('.ipsLoginButton').size() > 0 ");
    }
}
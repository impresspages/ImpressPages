<?php

/**
 * @package ImpressPages
 *
 *
 */

namespace PhpUnit\Helper\User;

class Admin
{
    /**
     * @var \Behat\Mink\Session
     */
    protected $session;

    /**
     * @var \PhpUnit\Helper\Installation
     */
    protected $installation;

    /**
     * @param \Behat\Mink\Session $session
     * @param \PhpUnit\Helper\Installation $installation
     */
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
        if (!$loginField) {
            //* TODOX remove
            var_export($loginPage->getContent());
            echo __FILE__ . ':' . (__LINE__ - 2);
            exit();
            //*/
        }
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


    public function addWidget($widgetName, $block = 'main')
    {
        $session = $this->session;
        $page = $session->getPage();
        $widgetButton = $page->find('css', '#ipAdminWidgetButton-' . $widgetName);
        $block = $page->find('css', '#ipBlock-' . $block);
        $widgetButton->dragTo($block);
    }
}

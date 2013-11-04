<?php
/**
 * @package   ImpressPages
 */

namespace PhpUnit\Helper\Mink;


class Html
{
    /**
     * @param string $html
     * @return \Behat\Mink\Element\DocumentElement
     */
    public static function getPage($html)
    {
        $client = new \PhpUnit\Helper\Mink\MockClient();
        $driver = new \PhpUnit\Helper\Mink\InternalDriver($client);
        $session = new \Behat\Mink\Session($driver);
        $session->start();

        $client->setResponse($html);
        $session->visit('http://localhost/');

        return $session->getPage();
    }
} 
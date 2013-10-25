<?php
/**
 * @package   ImpressPages
 */

namespace PhpUnit\Helper\Mink;

use \Behat\Mink\Driver\BrowserKitDriver;

class InternalDriver extends BrowserKitDriver
{

    public function __construct(InternalClient $client = null)
    {
        parent::__construct($client ?: new InternalClient());
    }

    /**
     * Returns last response headers.
     *
     * @return array
     */
    public function getResponseHeaders()
    {
        return $this->getClient()->getResponse()->getHeaders();
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return $this->getClient()->getResponse()->getStatus();
    }

}
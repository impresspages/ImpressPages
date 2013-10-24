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


} 
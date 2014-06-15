<?php
/**
 * @package ImpressPages
 */

namespace Ip;

class SetupWorker
{
    protected $lastVersion = null;

    /**
     * @param double $lastVersion version number of this plugin when it had been initialized for last time. Or nul if it is fresh install
     */
    final public function __construct($lastVersion)
    {
        $this->lastVersion = $lastVersion;
    }

    public function activate()
    {

    }

    public function deactivate()
    {

    }

    public function remove()
    {

    }

}

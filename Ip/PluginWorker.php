<?php
/**
 * @package   ImpressPages
 */


/**
 * Created by PhpStorm.
 * User: maskas
 * Date: 4/8/14
 * Time: 9:18 AM
 */

namespace Ip;


class PluginWorker {

    protected $recentVersion;

    public function __construct($recentVersion)
    {
        $this->recentVersion = $recentVersion;
    }

    /**
     * Get the version of the plugin that was the last time activated.
     * Useful to find out which database migrations have to be done.
     * @return string
     */
    public function getRecentVersion()
    {
        return $this->recentVersion;
    }
}

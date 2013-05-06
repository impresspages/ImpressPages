<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace IpUpdate\Library\Model;


class Environment
{


    protected static $instance;

    protected function __construct()
    {

    }

    protected function __clone()
    {

    }

    /**
     * Get singleton instance
     * @return Environment
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new Environment();
        }

        return self::$instance;
    }


    /**
     * @param string $oldUrl
     * @return bool true on success
     */
    public function getImpressPagesAPIUrl()
    {
        if ($this->getTestMode()) {
            return 'http://test.service.impresspages.org';
        } else {
            return 'http://service.impresspages.org';
        }

    }


    public function getTestMode()
    {
        if (defined('IUL_TESTMODE') && IUL_TESTMODE) {
            return true;
        } else {
            return false;
        }
    }

}
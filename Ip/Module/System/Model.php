<?php

/**
 * @package   ImpressPages
 *
 *
 */


namespace Ip\Module\System;


class Model
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
     * @return Model
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new Model();
        }

        return self::$instance;
    }


    /**
     * @param string $oldUrl
     * @return bool true on success
     */
    public function getImpressPagesAPIUrl()
    {
        if (ipConfig()->getRaw('TEST_MODE')) {
            return 'http://test.service.impresspages.org';
        } else {
            return 'http://service.impresspages.org';
        }

    }
}

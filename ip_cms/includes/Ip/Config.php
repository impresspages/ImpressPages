<?php
/**
 * @package ImpressPages
 */

namespace Ip;


class Config
{
    public function __construct()
    {

    }



    public function getCoreModuleUrl()
    {
        return BASE_URL.INCLUDE_DIR . 'Ip/Module/';
    }
}
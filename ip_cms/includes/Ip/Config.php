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
        return BASE_URL.'ip_cms/includes/Ip/Module/';
    }
}
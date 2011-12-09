<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Ip;


if (!defined('CMS')) exit;

/**
 *
 * Event dispatcher class
 *
 */
class Controller{

    public function allowAction($action){
        return true;
    }
}
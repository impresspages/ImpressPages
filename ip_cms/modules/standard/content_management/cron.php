<?php

/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management;

if (!defined('CMS'))
exit;


require_once(__DIR__ . "/model.php");

class Cron {

    function execute($options) {
        if ($options->firstTimeThisDay) {
            Model::deleteUnusedWidgets();
        }
        
    }
    
}
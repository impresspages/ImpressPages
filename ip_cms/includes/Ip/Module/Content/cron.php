<?php

/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Module\Content;

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
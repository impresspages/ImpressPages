<?php

/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Module\Content;




class Cron {

    function execute($options) {
        if ($options->firstTimeThisDay) {
            Model::deleteUnusedWidgets();
        }
        
    }
    
}
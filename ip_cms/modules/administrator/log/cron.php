<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Modules\administrator\log;

if (!defined('FRONTEND')&&!defined('BACKEND')) exit;


require_once (__DIR__.'/db.php');


class Cron{
    var $db;
    function __construct(){
        $this->db = new Db();
    }

    function execute($options){
        global $parametersMod;
        if($options->firstTimeThisMonth)
        $this->db->deleteOldLogs($parametersMod->getValue('administrator', 'log', 'parameters', 'log_size_in_days'));
    }

}




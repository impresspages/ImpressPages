<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Modules\administrator\email_queue;
if (!defined('FRONTEND')&&!defined('BACKEND')) exit;


require_once (__DIR__.'/db.php');
require_once (__DIR__.'/module.php');


class Cron{
    var $db;
    function __construct(){
        $this->db = new Db();
    }

    function execute($options){
        global $parametersMod;
        if($options->firstTimeThisMonth)
        $this->db->deleteOld(720);

        if($options->firstTimeThisHour){
            $queue = new Module();
            $queue->send();
        }

    }

}
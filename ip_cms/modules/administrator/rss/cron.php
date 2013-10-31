<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Modules\administrator\rss;

require_once (__DIR__.'/db.php');


class Cron{
    var $db;
    function __construct(){
        $this->db = new Db();
    }

    function execute($options){
        global $parametersMod;
        if($options->firstTimeThisMonth)
        $this->db->deleteOldRss();
    }

}





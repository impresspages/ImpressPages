<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Module\Rss;



class Cron{
    var $db;
    function __construct(){
        $this->db = new Db();
    }

    function execute($options){
        if ($options->firstTimeThisMonth) {
            $this->db->deleteOldRss();
        }
    }

}





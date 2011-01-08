<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2011 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */
namespace Modules\administrator\rss;
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
			$this->db->deleteOldRss();
	}

}





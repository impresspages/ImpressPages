<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\community\newsletter_subscribers;

if (!defined('FRONTEND')&&!defined('BACKEND')) exit;



class Cron{
	var $db;

	
	function execute($options){
		if($options->firstTimeThisMonth){
			$sql = "delete from `".DB_PREF."m_community_newsletter_subscribers` where 
			not `verified` and 1 < TIMESTAMPDIFF(MONTH,`created_on`,NOW())";
			$rs = mysql_query($sql);
			if(!$rs)
				trigger_error($sql." ".mysql_error());
		}
	}

}




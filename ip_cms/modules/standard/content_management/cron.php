<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */
 
namespace Modules\standard\content_management;  

if (!defined('FRONTEND')&&!defined('BACKEND')) exit;


class Cron{
	
	function execute(){
		$sql = "update `".DB_PREF."content_element` set 
		modify_track3 = modify_track2,
		modify_track2 = modify_track1,
		modify_track1 = last_modified,
		 modify_frequency = (
			(UNIX_TIMESTAMP(modify_track2) - UNIX_TIMESTAMP(modify_track3)) + (UNIX_TIMESTAMP(modify_track1) - UNIX_TIMESTAMP(modify_track2))
			)/2
		
		where
		not(modify_track1 <=> last_modified)
		";
		$rs = mysql_query($sql);
		if(!$rs)
			trigger_error($sql." ".mysql_error());
			
			
		$sql = "update `".DB_PREF."content_element` set 
		modify_track3 = modify_track2,
		modify_track2 = modify_track1,
		modify_track1 = last_modified,
		 modify_frequency = (
			(UNIX_TIMESTAMP(modify_track2) - UNIX_TIMESTAMP(modify_track3)) + (UNIX_TIMESTAMP() - UNIX_TIMESTAMP(modify_track2))
			)/2
		
		where
		UNIX_TIMESTAMP(modify_track1) - UNIX_TIMESTAMP(modify_track2) < UNIX_TIMESTAMP() - UNIX_TIMESTAMP(modify_track1)
		";
		$rs = mysql_query($sql);
		if(!$rs)
			trigger_error($sql." ".mysql_error());			
	}

}





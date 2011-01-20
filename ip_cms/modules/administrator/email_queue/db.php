<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
namespace Modules\administrator\email_queue; 
if (!defined('FRONTEND')&&!defined('BACKEND')) exit;

class Db{

  public static function getEmail($id){
		$sql = "SELECT * FROM `".DB_PREF."m_administrator_email_queue` 
		WHERE `id` = ".(int)$id." limit 1";
		$rs = mysql_query($sql);
		if ($rs) {
		  if($lock = mysql_fetch_assoc($rs))
		    return $lock;
		  else
			  return false;
		} else {
			trigger_error($sql." ".mysql_error());
			return false;
		}
  }

	public static function addEmail($from, $fromName, $to, $toName, $subject, $email, $immediate, $html, $filesStr, $fileNamesStr, $mimeTypesStr){
		if($immediate)
			$immediate = 1;
		else 
			$immediate = 0;

		$sql = "insert into `".DB_PREF."m_administrator_email_queue` set 
		`from` = '".mysql_real_escape_string($from)."', `from_name` = '".mysql_real_escape_string($fromName)."', `to` = '".mysql_real_escape_string($to)."',
     `to_name` = '".mysql_real_escape_string($toName)."', `subject` = '".mysql_real_escape_string($subject)."', 
     `email` = '".mysql_real_escape_string($email)."', `immediate` = ".$immediate.", `html` = ".$html.",
     `files` = '".mysql_real_escape_string($filesStr)."', `file_names` = '".mysql_real_escape_string($fileNamesStr)."', `file_mime_types` = '".mysql_real_escape_string($mimeTypesStr)."'";
		$rs = mysql_query($sql);
		if(!$rs){
			trigger_error($sql." ".mysql_error());
			return false;
		}else
			return true;
	}
  
	public static function lock($count, $key){
		$sql = "update `".DB_PREF."m_administrator_email_queue` set 
		`lock` = '".mysql_real_escape_string($key)."', `locked_on` = NOW() 
		where `lock` is NULL and send is NULL order by
		immediate desc, id asc limit ".$count;
		$rs = mysql_query($sql);
		if(!$rs){
			trigger_error($sql." ".mysql_error());
			return false;
		}else
			return mysql_affected_rows();
	}

	public static function lockOnlyImmediate($count, $key){
		$sql = "update `".DB_PREF."m_administrator_email_queue` set 
		`lock` = '".mysql_real_escape_string($key)."', `locked_on` = NOW() 
		where `immediate` and `lock` is NULL and `send` is NULL order by
		`id` asc limit ".$count;
		$rs = mysql_query($sql);
		if(!$rs){
			trigger_error($sql." ".mysql_error());
			return false;
		}else
			return mysql_affected_rows();
	}
	
	public static function unlock($key){
		$sql = "update `".DB_PREF."m_administrator_email_queue` set 
		`send` = NOW(), `lock` = NULL, `locked_on` = NULL 
		where `lock` = '".mysql_real_escape_string($key)."'";
		$rs = mysql_query($sql);
		if(!$rs){
			trigger_error($sql." ".mysql_error());
			return false;
		}else
			return mysql_affected_rows();
	}

	public static function getLocked($key){
		$sql = "select * from `".DB_PREF."m_administrator_email_queue`  
		where `lock` = '".mysql_real_escape_string($key)."'";
		$rs = mysql_query($sql);
		if(!$rs){
			trigger_error($sql." ".mysql_error());
			return false;
		}else{
			$answer = array();
			while($lock = mysql_fetch_assoc($rs))
				$answer[] = $lock;
			
			return $answer;
		}
	}

	public static function markSend($key){
		$sql = "update `".DB_PREF."m_administrator_email_queue` set 
		`send` = NOW() where `lock` = '".mysql_real_escape_string($key)."'";
		$rs = mysql_query($sql);
		if(!$rs){
			return false;
			trigger_error($sql." ".mysql_error());
		}else
			return mysql_affected_rows();
	}
  
	
	public static function delteOldSent($hours){
		$sql = "delete from `".DB_PREF."m_administrator_email_queue` where 
		`send` is not NULL and ".((int)$hours)." < TIMESTAMPDIFF(HOUR,`send`,NOW())";
		$rs = mysql_query($sql);
		if(!$rs){
			trigger_error($sql." ".mysql_error());
			return false;
		}else
			return mysql_affected_rows();
	}
	
	
	/*apparently there were some errors if exists old locked records. */
	public static function deleteOld($hours){
		$sql = "delete from `".DB_PREF."m_administrator_email_queue` where 
		(`lock` is not NULL and ".((int)$hours)." < TIMESTAMPDIFF(HOUR,`locked_on`,NOW()))
		or
		(`send` is not NULL and ".((int)$hours)." < TIMESTAMPDIFF(HOUR,`send`,NOW()))
		
		";
		$rs = mysql_query($sql);
		if(!$rs){
			trigger_error($sql." ".mysql_error());
			return false;
		}else
			return mysql_affected_rows();
	}
	
	public static function sentOrLockedCount($minutes){
		$sql = "select count(*) as `sent` from `".DB_PREF."m_administrator_email_queue` where 
		(`send` is not NULL and ".((int)$minutes)." > TIMESTAMPDIFF(MINUTE,`send`,NOW()))
		or
		(`lock` is not NULL and send is null) ";
		$rs = mysql_query($sql);
		if(!$rs){
			trigger_error($sql." ".mysql_error());
			return false;
		}else{
			if($lock = mysql_fetch_assoc($rs)){
				return $lock['sent'];
			}else
				return false;
		}
	}
	
}

   
   

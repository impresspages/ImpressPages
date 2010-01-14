<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */
namespace Modules\community\newsletter;
if (!defined('FRONTEND')&&!defined('BACKEND')) exit;

	class Db{
	
		public static function getSubscriber($id){
			$sql = "select * from ".DB_PREF."m_community_newsletter_subscribers where id  = ".mysql_real_escape_string($id)." ";
			$rs = mysql_query($sql);
			if($rs){
				if($lock = mysql_fetch_assoc($rs))
					return $lock;
				else
					return false;
			}else{
				trigger_error($sql." ".mysql_error());
				return false;
			}
		}	
	
		public static function getRecord($id){
			$sql = "select * from ".DB_PREF."m_community_newsletter where id  = ".mysql_real_escape_string($id)." ";
			$rs = mysql_query($sql);
			if($rs){
				if($lock = mysql_fetch_assoc($rs))
					return $lock;
				else
					return false;
			}else{
				trigger_error($sql." ".mysql_error());
				return false;
			}
		}
		
		
		public static function getSubscriberByEmail($email, $languageId){
			$sql = "select * from ".DB_PREF."m_community_newsletter_subscribers where language_id = ".mysql_real_escape_string($languageId)." and  email  = '".mysql_real_escape_string($email)."' ";
			$rs = mysql_query($sql);
			if($rs){
				if($lock = mysql_fetch_assoc($rs))
					return $lock;
				else
					return false;
			}else{
				trigger_error($sql." ".mysql_error());
				return false;
			}
		}

		
		public static function getSubscribers($languageId){
			$sql = "select * from ".DB_PREF."m_community_newsletter_subscribers where language_id  = ".mysql_real_escape_string($languageId)." and `verified`";
			$rs = mysql_query($sql);
			if($rs){
				$answer = array();
				while($lock = mysql_fetch_assoc($rs))
					$answer[] = $lock;
				return $answer;
			}else{
				trigger_error($sql." ".mysql_error());
				return false;
			}
		}
		
		
		public static function getParLang($moduleId, $languageId, $gname, $pname){
      $sql = "select g.name as g_name, p.name as p_name, w.translation from ".DB_PREF."parameter_group g, ".DB_PREF."parameter p, ".DB_PREF."par_lang_wysiwyg w where
      g.name = '".mysql_real_escape_string($gname)."' and p.name = '".mysql_real_escape_string($pname)."' and 
			g.module_id = '".$moduleId."' and p.group_id = g.id and w.parameter_id = p.id and w.language_id =  '".$languageId."'
       ";
      $rs = mysql_query($sql);
			if($rs){
				if($lock = mysql_fetch_assoc($rs))
					return $lock['translation'];
				else{
					trigger_error($sql." Can't find parameter");
					return false;
				}
			}else{
				trigger_error($sql." ".mysql_error());
				return false;
			}
		}
		
		
		public static function subscribed($email, $languageId){
			$sql = "select * from ".DB_PREF."m_community_newsletter_subscribers where language_id = ".mysql_real_escape_string($languageId)." and email = '".mysql_real_escape_string($email)."' and verified";
			$rs = mysql_query($sql);
			if($rs){
				if(mysql_num_rows($rs) > 0)
					return true;
				else
					return false;
			}else	
				trigger_error($sql." ".mysql_error());
		}
		
		public static function registeredAndNotActivated($email, $languageId){
			$sql = "select * from ".DB_PREF."m_community_newsletter_subscribers where language_id = ".mysql_real_escape_string($languageId)." and email = '".mysql_real_escape_string($email)."' and not verified";
			$rs = mysql_query($sql);
			if($rs){
				if(mysql_num_rows($rs) > 0)
					return true;
				else
					return false;
			}else	
				trigger_error($sql." ".mysql_error());
		}
		
		public static function unsubscribe($email, $languageId, $id = null, $code = null){
		  $tmpWhere = '';
		  if ($id !== null) {
		    $tmpWhere .= ' and `id` = '.(int)$id.' ';
		  }

		  if ($code !== null) {
		    $tmpWhere .= ' and `verification_code` = \''.mysql_real_escape_string($code).'\' ';
		  }
		  
			$sql = "delete from ".DB_PREF."m_community_newsletter_subscribers where language_id = ".mysql_real_escape_string($languageId)." and email = '".mysql_real_escape_string($email)."' ".$tmpWhere."";
			$rs = mysql_query($sql);
			if($rs){
				return true;
			}else{
				trigger_error($sql." ".mysql_error());
				return false;
			}
		}
		
		public static function subscribe($email, $languageId){
			$code = md5(uniqid(rand(), true));
			$sql = "insert into ".DB_PREF."m_community_newsletter_subscribers set `language_id` = ".mysql_real_escape_string($languageId).", `email` = '".mysql_real_escape_string($email)."', `verification_code` = '".mysql_real_escape_string($code)."'";
			$rs = mysql_query($sql);
			if(!$rs)
				trigger_error($sql." ".mysql_error());
			else return 1;
		}
		
		public static function confirm($id,  $code, $languageId){
			$sql = "update ".DB_PREF."m_community_newsletter_subscribers set `verified` = 1 where `language_id` = ".mysql_real_escape_string($languageId)." and `id` = ".mysql_real_escape_string($id)." and `verification_code` = '".mysql_real_escape_string($code)."'";
			$rs = mysql_query($sql);
			if($rs){
				if(mysql_affected_rows() == 1)
					return 1;
				if(mysql_affected_rows() > 1)
					trigger_error($sql." Affected more than one row.");
			}else
				trigger_error($sql." ".mysql_error());
			
      
			$sql = "select * from ".DB_PREF."m_community_newsletter_subscribers where `language_id` = ".mysql_real_escape_string($languageId)." and `id` = ".mysql_real_escape_string($id)." and `verification_code` = '".mysql_real_escape_string($code)."'";
			$rs = mysql_query($sql);
			if($rs){
        if(mysql_num_rows($rs) == 1)
          return 1;
        else
          return 0;
      }else return 0;
		}
		
	}

		
   

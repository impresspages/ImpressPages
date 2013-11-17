<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Module\Email;

class Db{

    public static function getEmail($id){
        $sql = "SELECT * FROM `".DB_PREF."m_administrator_email_queue`
		WHERE `id` = ".(int)$id." limit 1";
        $rs = ip_deprecated_mysql_query($sql);
        if ($rs) {
            if($lock = ip_deprecated_mysql_fetch_assoc($rs))
            return $lock;
            else
            return false;
        } else {
            trigger_error($sql." ".ip_deprecated_mysql_error());
            return false;
        }
    }

    public static function addEmail($from, $fromName, $to, $toName, $subject, $email, $immediate, $html, $filesStr, $fileNamesStr, $mimeTypesStr){
        if($immediate)
        $immediate = 1;
        else
        $immediate = 0;

        $sql = "insert into `".DB_PREF."m_administrator_email_queue` set
		`from` = '".ip_deprecated_mysql_real_escape_string($from)."', `from_name` = '".ip_deprecated_mysql_real_escape_string($fromName)."', `to` = '".ip_deprecated_mysql_real_escape_string($to)."',
     `to_name` = '".ip_deprecated_mysql_real_escape_string($toName)."', `subject` = '".ip_deprecated_mysql_real_escape_string($subject)."',
     `email` = '".ip_deprecated_mysql_real_escape_string($email)."', `immediate` = ".(int)$immediate.", `html` = ".(int)$html.",
     `files` = '".ip_deprecated_mysql_real_escape_string($filesStr)."', `file_names` = '".ip_deprecated_mysql_real_escape_string($fileNamesStr)."', `file_mime_types` = '".ip_deprecated_mysql_real_escape_string($mimeTypesStr)."'";
        $rs = ip_deprecated_mysql_query($sql);
        if(!$rs){
            trigger_error($sql." ".ip_deprecated_mysql_error());
            return false;
        }else
        return true;
    }

    public static function lock($count, $key){
        $sql = "update `".DB_PREF."m_administrator_email_queue` set
		`lock` = '".ip_deprecated_mysql_real_escape_string($key)."', `locked_on` = NOW()
		where `lock` is NULL and send is NULL order by
		immediate desc, id asc limit ".$count;
        $rs = ip_deprecated_mysql_query($sql);
        if(!$rs){
            trigger_error($sql." ".ip_deprecated_mysql_error());
            return false;
        }else
        return ip_deprecated_mysql_affected_rows();
    }

    public static function lockOnlyImmediate($count, $key){
        $sql = "update `".DB_PREF."m_administrator_email_queue` set
		`lock` = '".ip_deprecated_mysql_real_escape_string($key)."', `locked_on` = NOW()
		where `immediate` and `lock` is NULL and `send` is NULL order by
		`id` asc limit ".$count;
        $rs = ip_deprecated_mysql_query($sql);
        if(!$rs){
            trigger_error($sql." ".ip_deprecated_mysql_error());
            return false;
        }else
        return ip_deprecated_mysql_affected_rows();
    }

    public static function unlock($key){
        $sql = "update `".DB_PREF."m_administrator_email_queue` set
		`send` = NOW(), `lock` = NULL, `locked_on` = NULL 
		where `lock` = '".ip_deprecated_mysql_real_escape_string($key)."'";
        $rs = ip_deprecated_mysql_query($sql);
        if(!$rs){
            trigger_error($sql." ".ip_deprecated_mysql_error());
            return false;
        }else
        return ip_deprecated_mysql_affected_rows();
    }

    public static function unlockOne($id){
        $sql = "update `".DB_PREF."m_administrator_email_queue` set
		`send` = NOW(), `lock` = NULL, `locked_on` = NULL 
		where `id` = '".(int)$id."'";
        $rs = ip_deprecated_mysql_query($sql);
        if(!$rs){
            trigger_error($sql." ".ip_deprecated_mysql_error());
            return false;
        }else
        return ip_deprecated_mysql_affected_rows();
    }


    public static function getLocked($key){
        $sql = "select * from `".DB_PREF."m_administrator_email_queue`
		where `lock` = '".ip_deprecated_mysql_real_escape_string($key)."'";
        $rs = ip_deprecated_mysql_query($sql);
        if(!$rs){
            trigger_error($sql." ".ip_deprecated_mysql_error());
            return false;
        }else{
            $answer = array();
            while($lock = ip_deprecated_mysql_fetch_assoc($rs))
            $answer[] = $lock;
             
            return $answer;
        }
    }

    public static function markSend($key){
        $sql = "update `".DB_PREF."m_administrator_email_queue` set
		`send` = NOW() where `lock` = '".ip_deprecated_mysql_real_escape_string($key)."'";
        $rs = ip_deprecated_mysql_query($sql);
        if(!$rs){
            return false;
            trigger_error($sql." ".ip_deprecated_mysql_error());
        }else
        return ip_deprecated_mysql_affected_rows();
    }


    public static function delteOldSent($hours){
        $sql = "delete from `".DB_PREF."m_administrator_email_queue` where
		`send` is not NULL and ".((int)$hours)." < TIMESTAMPDIFF(HOUR,`send`,NOW())";
        $rs = ip_deprecated_mysql_query($sql);
        if(!$rs){
            trigger_error($sql." ".ip_deprecated_mysql_error());
            return false;
        }else
        return ip_deprecated_mysql_affected_rows();
    }


    /*apparently there were some errors if exists old locked records. */
    public static function deleteOld($hours){
        $sql = "delete from `".DB_PREF."m_administrator_email_queue` where
		(`lock` is not NULL and ".((int)$hours)." < TIMESTAMPDIFF(HOUR,`locked_on`,NOW()))
		or
		(`send` is not NULL and ".((int)$hours)." < TIMESTAMPDIFF(HOUR,`send`,NOW()))
		
		";
        $rs = ip_deprecated_mysql_query($sql);
        if(!$rs){
            trigger_error($sql." ".ip_deprecated_mysql_error());
            return false;
        }else
        return ip_deprecated_mysql_affected_rows();
    }

    public static function sentOrLockedCount($minutes){
        $sql = "select count(*) as `sent` from `".DB_PREF."m_administrator_email_queue` where
		(`send` is not NULL and ".((int)$minutes)." > TIMESTAMPDIFF(MINUTE,`send`,NOW()))
		or
		(`lock` is not NULL and send is null) ";
        $rs = ip_deprecated_mysql_query($sql);
        if(!$rs){
            trigger_error($sql." ".ip_deprecated_mysql_error());
            return false;
        }else{
            if($lock = ip_deprecated_mysql_fetch_assoc($rs)){
                return $lock['sent'];
            }else
            return false;
        }
    }

}




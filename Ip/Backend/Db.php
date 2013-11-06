<?php
/**
 * @package ImpressPages
 *
 *
 */


namespace Ip\Backend;

class Db {



    public static function incorrectLoginCount($userName) {
        /*
         0 - success
         1 - incorrect login
         2 - suspended account
         */
        $answer = 0;
        $sql = "select * from  `".DB_PREF."log` where `value_int` = 1 and `module` = 'system' and `value_str` = '".ip_deprecated_mysql_real_escape_string($userName)."' and `name` = 'backend login incorrect' and 60 > TIMESTAMPDIFF(MINUTE,`time`,NOW()) order by `time` desc";
        $rs = ip_deprecated_mysql_query($sql);
        if(!$rs)
        trigger_error($sql." ".ip_deprecated_mysql_error());
        else {
            $lock = ip_deprecated_mysql_fetch_assoc($rs);
            while($lock && $lock['value_int'] != 0) {
                $answer++;
                $lock = ip_deprecated_mysql_fetch_assoc($rs);
            }
        }
        return $answer;
    }

    public static function log($module, $name, $valueStr = null, $valueInt= null, $valueFloat= null) {
        if($valueInt === null)
        $valueInt = 'NULL';
        else
        $valueInt = "'".ip_deprecated_mysql_real_escape_string($valueInt)."'";

        if($valueFloat === null)
        $valueFloat = 'NULL';
        else
        $valueFloat = "'".ip_deprecated_mysql_real_escape_string($valueFloat)."'";

        $sql = "insert into `".DB_PREF."log` set `module` = '".ip_deprecated_mysql_real_escape_string($module)."', `name` = '".ip_deprecated_mysql_real_escape_string($name)."', `value_str`='".ip_deprecated_mysql_real_escape_string($valueStr)."', `value_int`=".$valueInt.", `value_float`=".$valueFloat."  ";
        $rs = ip_deprecated_mysql_query($sql);
        if(!$rs) {
            echo $sql." ".ip_deprecated_mysql_error(); //can't use standard error handling because of infinite loop danger
        }
    }


    public static function modules($managed = null, $userId = null) {
        global $cms;
        $groups = array();
        $sql = "select g.name as g_name, g.id, g.translation from `".DB_PREF."module_group` g
  	where 1 order by row_number";

        $rs = ip_deprecated_mysql_query($sql);
        if($rs) {
            while($lock = ip_deprecated_mysql_fetch_assoc($rs)) {
                if(!isset($groups[$lock['translation']])) { //if exist two groups with the same translation this array can be identified already
                    $groups[$lock['translation']] = array();
                }

                if ($managed === null) {
                    $managedSql = '';
                } else {
                    if ($managed) {
                        $managedSql = ' and managed ';
                    } else {
                        $managedSql = ' and not managed ';
                    }
                }

                if($userId === null) {
                    $sql2 = "select m.name as m_name, m.id, m.translation, core from
          `".DB_PREF."module` m where m.group_id = '".$lock['id']."' ".$managedSql."
           order by row_number";
                }else {
                    $sql2 = "select m.name as m_name, m.id, m.translation, core from
          `".DB_PREF."user_to_mod` um,`".DB_PREF."module` m where
          um.user_id = '".ip_deprecated_mysql_real_escape_string($userId)."' and um.module_id = m.id and
          m.group_id = '".$lock['id']."' ".$managedSql." order by row_number";
                }
                $rs2 = ip_deprecated_mysql_query($sql2);
                if($rs2) {
                    while($lock2 = ip_deprecated_mysql_fetch_assoc($rs2)) {
                        $lock2['g_name'] =  $lock['g_name'];
                        $lock2['g_id'] =  $lock['id'];
                        $groups[$lock['translation']][] = $lock2;
                    }
                    if(sizeof($groups[$lock['translation']]) == 0) {
                        unset($groups[$lock['translation']]);
                    }
                }else trigger_error($sql." ".ip_deprecated_mysql_error());
            }
        }else trigger_error($sql." ".ip_deprecated_mysql_error());
        return $groups;
    }

    //return true if user have permission to use the module
    public static function allowedModule($moduleId, $userId) {
        $sql = "select * from `".DB_PREF."user_to_mod` where `user_id`='".ip_deprecated_mysql_real_escape_string($userId)."' and `module_id`='".ip_deprecated_mysql_real_escape_string($moduleId)."' ";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs) {
            if(ip_deprecated_mysql_num_rows($rs) > 0)
            return true;
            else
            return false;
        }else {
            trigger_error($sql." ".ip_deprecated_mysql_error());
        }
        return false;
    }

    public static function firstAllowedModule($userId) {
        $sql = "select m.id, g.name as g_name, m.name as m_name, m.core  from `".DB_PREF."user_to_mod` utm, `".DB_PREF."module` m, `".DB_PREF."module_group` g
  	where m.id = utm.module_id and m.group_id = g.id and utm.user_id='".ip_deprecated_mysql_real_escape_string($userId)."'
  	order by g.row_number asc, m.row_number asc";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs) {
            if($lock = ip_deprecated_mysql_fetch_assoc($rs))
            return $lock;
        }else {
            trigger_error($sql." ".ip_deprecated_mysql_error());
        }
        return false;
    }


    public static  function userId($name, $pass) {
        $answer = false;
        $sql = "select id from `".DB_PREF."user` where `name` = '".ip_deprecated_mysql_real_escape_string($name)."' and `pass`='".md5($pass)."' and not blocked ";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs) {
            if($lock = ip_deprecated_mysql_fetch_assoc($rs))
            $answer = $lock['id'];
        }else trigger_error($sql." ".ip_deprecated_mysql_error());
        return $answer;
    }


}

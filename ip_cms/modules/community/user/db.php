<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Modules\community\user;

if (!defined('CMS')) exit;
/**
 * database class for user management
 * @package ImpressPages
 */
class Db {

    /**
     * @param int id of record
     * @return array requested record
     */
    public static function userById($id) {
        $sql = "select * from ".DB_PREF."m_community_user where id  = '".mysql_real_escape_string($id)."' ";

        $rs = mysql_query($sql);
        if ($rs) {
            if($lock = mysql_fetch_assoc($rs)) {
                return $lock;
            } else {
                return false;
            }
        } else {
            trigger_error($sql." ".mysql_error());
            return false;
        }
    }

    /**
     * @param string email of user
     * @return array requested record
     */
    public static function userByEmail($email) {
        $sql = "select * from ".DB_PREF."m_community_user where verified and email = '".mysql_real_escape_string($email)."' ";

        $rs = mysql_query($sql);
        if($rs) {
            if($lock = mysql_fetch_assoc($rs))
            return $lock;
            else
            return false;
        }else {
            trigger_error($sql." ".mysql_error());
            exit;
        }

    }

    /**
     * @param string login
     * @return array requested record
     */
    public static function userByLogin($login) {
        $sql = "select * from ".DB_PREF."m_community_user where  verified and `login` = '".mysql_real_escape_string($login)."' ";

        $rs = mysql_query($sql);
        if($rs) {
            if($lock = mysql_fetch_assoc($rs))
            return $lock;
            else
            return false;
        }else {
            trigger_error($sql." ".mysql_error());
            exit;
        }

    }

    /**
     * @param int id of user
     * @param string new password
     * @return bool true on success
     */
    public static function updatePassword($userId, $newPassword) {
        $sql = "update ".DB_PREF."m_community_user set password = '".md5($newPassword)."' where id = '".mysql_real_escape_string($userId)."'";
        $rs = mysql_query($sql);
        if($rs && mysql_affected_rows() == 1)
        return true;
        else
        return false;
    }

    /**
     * marks user as verified
     * @param int id of user
     * @return bool true on success
     */
    public static function verify($userId) {
        $sql = "update ".DB_PREF."m_community_user set verified = 1 where id = '".mysql_real_escape_string($userId)."'";
        $rs = mysql_query($sql);
        if($rs && mysql_affected_rows() == 1)
        return true;
        else
        return false;
    }

    /**
     * verifies email change
     * @param int user id
     * @return bool true on success
     */
    public static function verifyNewEmail($userId) {
        $sql = "update ".DB_PREF."m_community_user set email = new_email, new_email = null where id = '".(int)$userId."'";
        $rs = mysql_query($sql);
        if($rs && mysql_affected_rows() == 1)
        return true;
        else
        return false;
    }

    /**
     * verifies password reset
     * @param int user id
     * @return bool true on success
     */
    public static function verifyNewPassword($userId) {
        $sql = "update ".DB_PREF."m_community_user set password = new_password, new_password = null where id = '".(int)$userId."'";
        $rs = mysql_query($sql);
        if($rs && mysql_affected_rows() == 1)
        return true;
        else
        return false;
    }

    /**
     * write last login timestamp
     * @param int user id
     * @return bool true on success
     */
    public static function loginTimestamp($userId) {
        $sql = "update ".DB_PREF."m_community_user set last_login = CURRENT_TIMESTAMP where id = '".mysql_real_escape_string($userId)."'";
        $rs = mysql_query($sql);
        if($rs && mysql_affected_rows() == 1)
        return true;
        else
        return false;
    }

    /**
     * delete all users, that was not used for a last x months
     * @param int $months
     * @return array Deleted records
     *
     **/
    public static function deleteOutdatedUsers($months) {
        if($months == '')
        $months = 0;
        $sql = "select * from ".DB_PREF."m_community_user where date_add(`last_login`, interval ".(int)$months." month) < current_timestamp() ";
        $rs = mysql_query($sql);
        $answer = array();
        if($rs) {
            while($lock = mysql_fetch_assoc($rs)) {
                $answer[] = $lock;
            }

            $sql2 = "delete from ".DB_PREF."m_community_user where date_add(`last_login`, interval ".(int)$months." month) < current_timestamp() ";
            $rs2 = mysql_query($sql2);
            if(!$rs2)
            trigger_error($sql2." ".mysql_error());
            else
            return $answer;
        }else
        trigger_error($sql." ".mysql_error());
    }

    /**
     * find users that should be warnded about registration expiration
     * @param int how old records should be warned (in months). 12 mean that will be returned the users, that was not logged in for twelve months
     * @param int warn before x days
     * @param int warn every x days
     * @return array users that should be warned
     *
     */
    public static function getUsersToWarn($outdatedWithin, $warnBefore, $warnEvery) {
        $sql = "
      select *, date_add(last_login, interval ".(int)$outdatedWithin." month) as valid_until from ".DB_PREF."m_community_user 
      where date_add(`last_login`, interval ".(int)$outdatedWithin." month) < date_add(now(), interval ".(int)$warnBefore." day) and 
      (`warned_on` is null or date_add(`warned_on`, interval ".(int)$warnEvery." day) < now())";
        $rs = mysql_query($sql);
        if($rs) {
            $answer = array();
            while($lock = mysql_fetch_assoc($rs))
            $answer[] = $lock;
            return $answer;
        }else
        trigger_error($sql." ".mysql_error());

    }

    /**
     * marks users as warned about account expiration
     * @param array ids of users, that should be marked as warned
     * @retrun void
     **/
    public static function setWarned($userIds) {
        if(sizeof($userIds) > 0) {
            $ids = implode(',', $userIds);
            $sql = "update ".DB_PREF."m_community_user set `warned_on` = current_timestamp() where `id` in (".implode(',',$userIds).")";
            $rs = mysql_query($sql);
            if(!$rs)
            trigger_error($sql." ".mysql_error());
        }
    }



    /**
     * marks user as logged in. It helps from expiration :)
     * @param $id id of user, that should be marked as logged in
     * @retrun bool true on success
     **/
    public static function renewRegistration($id) {
        $sql = "update ".DB_PREF."m_community_user set `last_login` = current_timestamp() where id = '".mysql_real_escape_string($id)."' ";
        $rs = mysql_query($sql);
        if(!$rs) {
            trigger_error($sql);
            return false;
        } else {
            return true;
        }
    }


}




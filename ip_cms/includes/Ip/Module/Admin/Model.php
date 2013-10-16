<?php
namespace Ip\Module\Admin;


class Model{


    protected function __construct()
    {

    }

    /**
    * @return Model
    */
    public static function instance()
    {
        return new Model();
    }

    public function getAdminMenuItems()
    {
        $answer = array();


        $oldCmsInterface = new OldCmsInterface();
        $moduleGroups = $this->getOldModules(true, $this->getUserId());

        foreach($moduleGroups as $groupKey => $group) {
            $newItem = new \Ip\Menu\Item();
            $newItem->setTitle($groupKey);


            $children = array();
            foreach($group as $module) {
                $moduleItem = new \Ip\Menu\Item();
                $moduleItem->setTitle($module['translation']);
                $moduleItem->setUrl($oldCmsInterface->generateUrl($module['id']));
                $children[] = $moduleItem;
            }
            $newItem->setChildren($children);
            $answer[] = $newItem;

        }

        return $answer;
    }

    public static function setSafeMode($value)
    {
        $_SESSION['module']['admin']['safemode'] = (bool) $value;
    }

    public static function isSafeMode()
    {
        if (isset($_SESSION['module']['admin']['safemode'])) {
            return (bool) $_SESSION['module']['admin']['safemode'];
        }
        return false;
    }

    protected function getOldModules($managed = null, $userId = null) {
        global $cms;
        $groups = array();
        $sql = "select g.name as g_name, g.id, g.translation from `".DB_PREF."module_group` g
  	where 1 order by row_number";

        $rs = mysql_query($sql);
        if($rs) {
            while($lock = mysql_fetch_assoc($rs)) {
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
          um.user_id = '".mysql_real_escape_string($userId)."' and um.module_id = m.id and
          m.group_id = '".$lock['id']."' ".$managedSql." order by row_number";
                }
                $rs2 = mysql_query($sql2);
                if($rs2) {
                    while($lock2 = mysql_fetch_assoc($rs2)) {
                        $lock2['g_name'] =  $lock['g_name'];
                        $lock2['g_id'] =  $lock['id'];
                        $groups[$lock['translation']][] = $lock2;
                    }
                    if(sizeof($groups[$lock['translation']]) == 0) {
                        unset($groups[$lock['translation']]);
                    }
                }else trigger_error($sql." ".mysql_error());
            }
        }else trigger_error($sql." ".mysql_error());
        return $groups;
    }

    public function getUserId(){
        if(isset($_SESSION['backend_session']['user_id']))
            return $_SESSION['backend_session']['user_id'];
        else
            return false;
    }

    public function login($username, $pass)
    {
        $log = \Ip\ServiceLocator::getLog();
        $parametersMod = \Ip\ServiceLocator::getParametersMod();
        if($this->incorrectLoginCount($username.'('.$_SERVER['REMOTE_ADDR'].')') > 2) {
            $this->loginError = $parametersMod->getValue('standard', 'configuration', 'system_translations', 'login_suspended');
            $log->log('system', 'backend login suspended', $username.'('.$_SERVER['REMOTE_ADDR'].')', 2);
        }else {
            $id = $this->userId($username, $pass);
            if($id !== false) {
                $_SESSION['backend_session']['user_id'] = $id;
                $log->log('system', 'backend login', $username.' ('.$_SERVER['REMOTE_ADDR'].')', 0);
                return true;
            } else {
                $this->loginError = $parametersMod->getValue('standard', 'configuration', 'system_translations', 'login_incorrect');
                $log->log('system', 'backend login incorrect', $username.'('.$_SERVER['REMOTE_ADDR'].')', 1);
                return false;
            }
        }
    }

    public function logout()
    {
        if(isset($_SESSION['backend_session']))
            unset($_SESSION['backend_session']);
    }

    protected function incorrectLoginCount($userName)
    {
        /*
         0 - success
         1 - incorrect login
         2 - suspended account
         */
        $answer = 0;
        $sql = "select * from  `".DB_PREF."log` where `value_int` = 1 and `module` = 'system' and `value_str` = '".mysql_real_escape_string($userName)."' and `name` = 'backend login incorrect' and 60 > TIMESTAMPDIFF(MINUTE,`time`,NOW()) order by `time` desc";
        $rs = mysql_query($sql);
        if(!$rs)
            trigger_error($sql." ".mysql_error());
        else {
            $lock = mysql_fetch_assoc($rs);
            while($lock && $lock['value_int'] != 0) {
                $answer++;
                $lock = mysql_fetch_assoc($rs);
            }
        }
        return $answer;
    }
    protected  function userId($name, $pass) {
        $answer = false;
        $sql = "select id from `".DB_PREF."user` where `name` = '".mysql_real_escape_string($name)."' and `pass`='".md5($pass)."' and not blocked ";
        $rs = mysql_query($sql);
        if($rs) {
            if($lock = mysql_fetch_assoc($rs))
                $answer = $lock['id'];
        }else trigger_error($sql." ".mysql_error());
        return $answer;
    }
}
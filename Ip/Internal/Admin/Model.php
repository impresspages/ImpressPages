<?php
namespace Ip\Internal\Admin;


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


        $modules = \Ip\Internal\Plugins\Model::getModules();
        foreach($modules as $module) {
            $controllerClass = 'Ip\\Internal\\'.$module.'\\AdminController';
            if (!class_exists($controllerClass) || !method_exists($controllerClass, 'index')) {
                continue;
            }
            $moduleItem = new \Ip\Menu\Item();
            $moduleItem->setTitle($module);
            $moduleItem->setUrl(\Ip\Internal\Deprecated\Url::generate(null, null, null, array('aa' => $module.'.index')));
            $moduleItem->setUrl(ipActionUrl(array('aa' => $module . '.index')));
            $answer[] = $moduleItem;
        }


        $plugins = \Ip\Internal\Plugins\Model::getActivePlugins();

        foreach($plugins as $plugin) {
            $controllerClass = '\\Plugin\\' . $plugin . '\\AdminController';
            if (!class_exists($controllerClass) || !method_exists($controllerClass, 'index')) {
                continue;
            }
            $moduleItem = new \Ip\Menu\Item();
            $moduleItem->setTitle($plugin);
            $moduleItem->setUrl(\Ip\Internal\Deprecated\Url::generate(null, null, null, array('aa' => $plugin.'.index')));
            $moduleItem->setUrl(ipActionUrl(array('aa' => $plugin . '.index')));
            $answer[] = $moduleItem;
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
          um.userId = '".ip_deprecated_mysql_real_escape_string($userId)."' and um.module_id = m.id and
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

    public function getUserId(){
        if(isset($_SESSION['backend_session']['userId']))
            return $_SESSION['backend_session']['userId'];
        else
            return false;
    }

    public function login($username, $password)
    {
        $ip = ipRequest()->getServer('REMOTE_ADDR');

        // TODO use events for that
        if($this->incorrectLoginCount($username.'('.$ip.')') > 2) {
            $this->loginError = __('Your login suspended for one hour.', 'ipAdmin');
            ipLog()->warning('Admin.loginSuspended: {username} from {ip}', array('username' => $username, 'ip' => $ip));
        } else {
            $id = $this->userId($username, $password);
            if($id !== false) {
                $_SESSION['backend_session']['userId'] = $id;
                \Ip\ServiceLocator::dispatcher()->notify('Admin.login', array('userId' => $id));

                ipLog()->info('Admin.loggedIn: {username} from {ip}', array('username' => $username, 'ip' => $ip));
                return true;
            } else {
                $this->loginError = __('Incorrect name or password', 'ipAdmin');
                ipLog()->info('Admin.incorrectLogin: {username} from {ip}', array('username' => $username, 'ip' => $ip));
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
        return 0;

        // TODO do it through storage and not here
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
    protected  function userId($name, $pass) {
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
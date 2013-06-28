<?php
/**
 * @package   ImpressPages
 *
 *
 */

namespace IpUpdate\Library\Migration\To2_3;


class Script extends \IpUpdate\Library\Migration\General{

    private $conn;
    private $dbPref;

    public function process($cf)
    {
        
        $db = new \IpUpdate\Library\Model\Db();
        $conn = $db->connect($cf, \IpUpdate\Library\Model\Db::DRIVER_MYSQL);
        $this->conn = $conn;
        $this->dbPref = $cf['DB_PREF'];
        
        $module = $this->getModule(null, 'standard', 'content_management');

        $group = $this->getParametersGroup($module['id'], 'admin_translations');
        if ($group) {
            if(!$this->getParameter('standard', 'content_management', 'admin_translations', 'save_now_hint')) {
                $this->addStringParameter($group['id'], 'Save now hint', 'save_now_hint', 'Save now - publish later', 1);
            }
            if(!$this->getParameter('standard', 'content_management', 'admin_translations', 'search_widgets')) {
                $this->addStringParameter($group['id'], 'Search widgets', 'search_widgets', 'Search widgets', 1);
            }
        }
    }

    /**
     * (non-PHPdoc)
     * @see IpUpdate\Library\Migration.General::getSourceVersion()
     */
    public function getSourceVersion()
    {
        return '2.2';
    }

    /**
     * (non-PHPdoc)
     * @see IpUpdate\Library\Migration.General::getDestinationVersion()
     */
    public function getDestinationVersion()
    {
        return '2.3';
    }
    
    
    private function getModule($id=null, $groupName=null , $moduleName = null)
    {
        if($id != null)
            $sql = "select m.core, m.id, g.name as g_name, m.name as m_name from `".$this->dbPref."module_group` g, `".$this->dbPref."module` m where m.id = '".mysql_real_escape_string($id)."' and  m.group_id = g.id order by g.row_number, m.row_number limit 1";
        elseif($groupName != null && $moduleName != null)
        $sql = "select m.core, m.id, g.name as g_name, m.name as m_name from `".$this->dbPref."module_group` g, `".$this->dbPref."module` m where g.name = '".mysql_real_escape_string($groupName)."' and m.group_id = g.id and m.name= '".mysql_real_escape_string($moduleName)."' order by g.row_number, m.row_number limit 1";
        else
            $sql = "select m.core, m.id, g.name as g_name, m.name as m_name from `".$this->dbPref."module_group` g, `".$this->dbPref."module` m where m.group_id = g.id order by g.row_number, m.row_number limit 1";
        $rs = mysql_query($sql, $this->conn);
        if($rs)
        {
            if($lock = mysql_fetch_assoc($rs))
                return $lock;
            else
                return false;
        }else
        {
            throw new \IpUpdate\Library\UpdateException($sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
            return false;
        }
    }
    
    private function getParameter($moduleGroupName, $moduleName, $parameterGroupName, $parameterName) 
    {
        $sql = "select * from `".$this->dbPref."module_group` mg, `".$this->dbPref."module` m, `".$this->dbPref."parameter_group` pg, `".$this->dbPref."parameter` p
        where p.group_id = pg.id and pg.module_id = m.id and m.group_id = mg.id
        and mg.name = '".mysql_real_escape_string($moduleGroupName)."' and m.name = '".mysql_real_escape_string($moduleName)."' and pg.name = '".mysql_real_escape_string($parameterGroupName)."' and p.name = '".mysql_real_escape_string($parameterName)."'";
        $rs = mysql_query($sql, $this->conn);
        if ($rs) {
            if($lock = mysql_fetch_assoc($rs)) {
                return $lock;
            } else {
                return false;
            }
        } else {
            throw new \IpUpdate\Library\UpdateException($sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
            return false;
        }

    }
    
    private function getParametersGroup($moduleId, $name)
    {
        $sql = "select * from `".$this->dbPref."parameter_group` where `module_id` = '".mysql_real_escape_string($moduleId)."' and `name` = '".mysql_real_escape_string($name)."' ";
        $rs = mysql_query($sql, $this->conn);
        if($rs){
            if($lock = mysql_fetch_assoc($rs)){
                return $lock;
            } else {
                return false;
            }
        } else {
            throw new \IpUpdate\Library\UpdateException($sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
            return false;
        }
    }
    
    private function addStringParameter($groupId, $translation, $name, $value, $admin)
    {
        $sql = "INSERT INTO `".$this->dbPref."parameter` (`name`, `admin`, `regexpression`, `group_id`, `translation`, `comment`, `type`)
        VALUES ('".mysql_real_escape_string($name)."', ".(int)$admin.", '', ".(int)$groupId.", '".mysql_real_escape_string($translation)."', NULL, 'string')";
        $rs = mysql_query($sql, $this->conn);
        if($rs){
            $sql2 = "INSERT INTO `".$this->dbPref."par_string` (`value`, `parameter_id`)
            VALUES ('".mysql_real_escape_string($value)."', ".mysql_insert_id().");";
            $rs2 = mysql_query($sql2, $this->conn);
            if($rs2) {
                return true;
            } else {
                throw new \IpUpdate\Library\UpdateException($sql2." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
                return false;
            }
        } else {
            throw new \IpUpdate\Library\UpdateException($sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
            return false;
        }

    }

    
    
}

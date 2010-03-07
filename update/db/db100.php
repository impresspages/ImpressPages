<?php



if (!defined('CMS')) exit;


class Db_100 {

    /**
     * Connect to database.
     */   
    public static function connect(){
      $conn = mysql_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD); 
      if(!$conn) {
				trigger_error('Can\'t connect to database.');
        return false;
      }else{
        mysql_select_db(DB_DATABASE);
        mysql_query("SET CHARACTER SET ".MYSQL_CHARSET);
        return true;
      }
    } 

    /**
     * Disconnect from database.
     */
    public static function disconnect(){
     mysql_close();    
    }
     
    public static function getLanguages(){
      $answer = array();
      $sql = "select * from `".DB_PREF."language` where 1 order by row_number";
      $rs = mysql_query($sql);
      if($rs){
        while($lock = mysql_fetch_assoc($rs))
          $answer[] = $lock;
      }else{
        trigger_error($sql." ".mysql_error());
      }
      return $answer;
    }
     
        
    /**
     * Finds information about specified module, or returns first module.
     * @param int $id module id
     * @param string $groupName
     * @param string $moduleName               
     * @return array 
     */
     
     
    public static function getModule($id=null, $groupName=null , $moduleName = null){
      if($id != null)
        $sql = "select m.core, m.id, g.name as g_name, m.name as m_name from `".DB_PREF."module_group` g, `".DB_PREF."module` m where m.id = '".mysql_real_escape_string($id)."' and  m.group_id = g.id order by g.row_number, m.row_number limit 1";
      elseif($groupName != null && $moduleName != null)
        $sql = "select m.core, m.id, g.name as g_name, m.name as m_name from `".DB_PREF."module_group` g, `".DB_PREF."module` m where g.name = '".mysql_real_escape_string($groupName)."' and m.group_id = g.id and m.name= '".mysql_real_escape_string($moduleName)."' order by g.row_number, m.row_number limit 1";
      else      
        $sql = "select m.core, m.id, g.name as g_name, m.name as m_name from `".DB_PREF."module_group` g, `".DB_PREF."module` m where m.group_id = g.id order by g.row_number, m.row_number limit 1";
      $rs = mysql_query($sql);
      if($rs)
      {
        if($lock = mysql_fetch_assoc($rs))      
          return $lock;
        else
          return false;
      }else
      {
        trigger_error($sql." ".mysql_error());
        return false;
      }
    }
    

    
    

    
    /**
     * @access private
     */
    public static function getParLang($id, $reference, $languageId){
      $answer = array();
      $sql = "select p.type as p_type, g.name as g_name, p.name as p_name, t.translation from `".DB_PREF."parameter_group` g, `".DB_PREF."parameter` p, `".DB_PREF."par_lang` t where
      g.".$reference." = '".$id."' and p.group_id = g.id and t.parameter_id = p.id and t.language_id =  '".$languageId."'";
      $rs = mysql_query($sql);
      if($rs){
        while($lock = mysql_fetch_assoc($rs)){
          $answer[$lock['p_type']][$lock['g_name']][$lock['p_name']] = $lock['translation'];
        }      
      }else trigger_error($sql." ".mysql_error());
      return $answer;      
    }


    /**
     * @access private
     */
    public static function getParString($id, $reference){
      $answer = array();
      $sql = "select p.type as p_type, g.name as g_name, p.name as p_name, s.value from `".DB_PREF."parameter_group` g, `".DB_PREF."parameter` p, `".DB_PREF."par_string` s where
      g.".$reference." = '".$id."' and p.group_id = g.id  and p.id = s.parameter_id";
      $rs = mysql_query($sql);
      if($rs){
        while($lock = mysql_fetch_assoc($rs)){
          $answer[$lock['p_type']][$lock['g_name']][$lock['p_name']] = $lock['value'];
        }      
      }else trigger_error($sql." ".mysql_error());
      return $answer;
 
    }    


    /**
     * @access private
     */
    public static function getParInteger($id, $reference){
      $answer = array();
      $sql = "select p.type as p_type, g.name as g_name, p.name as p_name, s.value from `".DB_PREF."parameter_group` g, `".DB_PREF."parameter` p, `".DB_PREF."par_integer` s where
      g.".$reference." = '".$id."' and p.group_id = g.id  and p.id = s.parameter_id";
      $rs = mysql_query($sql);
      if($rs){
        while($lock = mysql_fetch_assoc($rs)){
          $answer[$lock['p_type']][$lock['g_name']][$lock['p_name']] = $lock['value'];
        }      
      }else trigger_error($sql." ".mysql_error());
      return $answer;
 
    }    
  
    /**
     * @access private
     */
    public static function getParBool($id, $reference){
      $answer = array();
      $sql = "select p.type as p_type, g.name as g_name, p.name as p_name, s.value from `".DB_PREF."parameter_group` g, `".DB_PREF."parameter` p, `".DB_PREF."par_bool` s where
      g.".$reference." = '".$id."' and p.group_id = g.id  and p.id = s.parameter_id";
      $rs = mysql_query($sql);
      if($rs){
        while($lock = mysql_fetch_assoc($rs)){
          $answer[$lock['p_type']][$lock['g_name']][$lock['p_name']] = $lock['value'];
        }      
      }else trigger_error($sql." ".mysql_error());
      return $answer;
 
    }    




    /**
     * @access private
     */
    public static function getParameter($id, $reference, $par_group, $parameter){
      $sql = "select p.* from `".DB_PREF."parameter` p,  `".DB_PREF."parameter_group` pg where pg.name = '".mysql_real_escape_string($par_group)."' and p.name = '".mysql_real_escape_string($parameter)."' and p.group_id = pg.id and pg.`".$reference."` = '".$id."'";
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
    
    /**
     * @access private
     */
    public static function getParameterById($id){
      $sql = "select * from `".DB_PREF."parameter` where `id` = '".(int)$id."'";
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

    /**
     * @access private
     */
    public static function getParameterGroupById($id){
      $sql = "select * from `".DB_PREF."parameter_group` where `id` = '".(int)$id."'";
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

    /**
     * @access private
     */
    public static function getParameterGroup($module_id, $group_name){
      $sql = "select * from `".DB_PREF."parameter_group` where `module_id` = '".(int)$module_id."' and `name` = '".mysql_real_escape_string($group_name)."'";
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

    
    private function addStringParameter($groupId, $translation, $name, $value, $admin){
      $sql = "INSERT INTO `".DB_PREF."parameter` (`name`, `admin`, `regexpression`, `group_id`, `translation`, `comment`, `type`)
      VALUES ('".mysql_real_escape_string($name)."', ".(int)$admin.", '', ".(int)$groupId.", '".mysql_real_escape_string($translation)."', NULL, 'string')";
      $rs = mysql_query($sql);
      if($rs){
        $sql2 = "INSERT INTO `".DB_PREF."par_string` (`value`, `parameter_id`)
        VALUES ('".mysql_real_escape_string($value)."', ".mysql_insert_id().");";
        $rs2 = mysql_query($sql2);
        if($rs2) {
            return true;
        } else {
          trigger_error($sql2." ".mysql_error());  
          return false;    
        }
      } else {
        trigger_error($sql." ".mysql_error());  
        return false;    
      }
      
    }    
      

    /**
     * @access private
     */
    public static function setParLang($id, $value, $languageId){
      $sql = "update `".DB_PREF."par_lang` set `translation` = '".mysql_real_escape_string($value)."' where
      `parameter_id` = '".(int)$id."' and `language_id` =  '".(int)$languageId."'";
      $rs = mysql_query($sql);
      if($rs)
        return true;
      else
        return false;
    }


    /**
     * @access private
     */
    public static function setParString($id, $value){
      $sql = "update `".DB_PREF."par_string` set `value` = '".mysql_real_escape_string($value)."' where
      `parameter_id` = '".(int)$id."'";

      $rs = mysql_query($sql);
      if($rs)
        return true;
      else
        return false;
    }    


    /**
     * @access private
     */
    public static function setParInteger($id, $value){
      $sql = "update `".DB_PREF."par_integer` set `value` = '".mysql_real_escape_string($value)."' where
      `parameter_id` = '".(int)$id."'";
      $rs = mysql_query($sql);
      if($rs)
        return true;
      else
        return false;
    }    
  
    /**
     * @access private
     */
    public static function setParBool($id, $value){
      if($value)
        $value = 1;
      else 
        $value = 0;
      $sql = "update `".DB_PREF."par_string` set `value` = '".$value."' where
      `parameter_id` = '".(int)$id."'";
      $rs = mysql_query($sql);
      if($rs)
        return true;
      else
        return false;
    }    
	
  

	
	
    //end parameters
    public static function setSystemVariable($name, $value){
      $sql = "update `".DB_PREF."variables` set `value` = '".mysql_real_escape_string($value)."' where
      `name` = '".mysql_real_escape_string($name)."'";
      $rs = mysql_query($sql);
      if (!$rs) {
        trigger_error($sql." ".mysql_error());
        return false;
      }
    }    
	
    /**
     * @access private
     */	
    public static function getSystemVariable($name){
      $sql = "select value from `".DB_PREF."variables`  where `name` = '".mysql_real_escape_string($name)."'";
      $rs = mysql_query($sql);
      if ($rs) {
        if ($lock = mysql_fetch_assoc($rs)) {
          return $lock['value'];
        } else
          return false;
      } else {
        trigger_error($sql." ".mysql_error());
        return false;
      }
    }    
    
    /**
     * @access private
     */
    public static function insertSystemVariable($name, $value){
      $sql = "insert into `".DB_PREF."variables` set `value` = '".mysql_real_escape_string($value)."', `name` = '".mysql_real_escape_string($name)."'";
      $rs = mysql_query($sql);
      if (!$rs) {
        trigger_error($sql." ".mysql_error());
        return false;
      }
    }        
    
    //end system variables
               
  
}
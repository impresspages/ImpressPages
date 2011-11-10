<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

    
if (!defined('FRONTEND')&&!defined('BACKEND')) exit;



/**
 * Main db class
 * Connects to database, provide some general functions.  
 * @package ImpressPages 
 */
class Db{
    private static $connection;
  
    /**
     * Connect to database.
     */  
    public static function connect(){
      self::$connection = mysql_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);
      if(!self::$connection) {
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
     mysql_close(self::$connection);  
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
        $sql = "select m.translation as m_translation, m.core, m.id, g.name as g_name, g.translation as g_translation, m.name as m_name, m.version from `".DB_PREF."module_group` g, `".DB_PREF."module` m where m.id = '".mysql_real_escape_string($id)."' and  m.group_id = g.id order by g.row_number, m.row_number limit 1";
      elseif($groupName != null && $moduleName != null)
        $sql = "select m.translation as m_translation, m.core, m.id, g.name as g_name, g.translation as g_translation, m.name as m_name, m.version from `".DB_PREF."module_group` g, `".DB_PREF."module` m where g.name = '".mysql_real_escape_string($groupName)."' and m.group_id = g.id and m.name= '".mysql_real_escape_string($moduleName)."' order by g.row_number, m.row_number limit 1";
      else      
        $sql = "select m.translation as m_translation, m.core, m.id, g.name as g_name, g.translation as g_translation, m.name as m_name, m.version from `".DB_PREF."module_group` g, `".DB_PREF."module` m where m.group_id = g.id order by g.row_number, m.row_number limit 1";
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
      $sql = "update `".DB_PREF."par_bool` set `value` = '".$value."' where
      `parameter_id` = '".(int)$id."'";
      $rs = mysql_query($sql);
      if($rs)
        return true;
      else
        return false;
    }    
	
    //end parameters
    
}    
?>
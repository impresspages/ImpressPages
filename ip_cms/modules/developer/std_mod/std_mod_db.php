<?php
/**
 * @package		Library
 * @copyright	Copyright (C) 2011 JSC Apro media.
 * @license		GNU/GPL, see license.html
 */
 
namespace Modules\developer\std_mod;
 
if (!defined('BACKEND')) exit;


class StdModDb{
    
  function __construct(){
 
  }
  
  function languages(){
    $answer = array();
    $sql = "select id, d_short, d_long from `".DB_PREF."language` where 1 order by  row_number  ";
    $rs = mysql_query($sql);
    if($rs){
      while($lock = mysql_fetch_assoc($rs)){
        $answer[] = $lock;
      }
    }else trigger_error($sql." ".mysql_error());
    return $answer;
  }

  function cmsLanguages(){
    $answer = array();
    $sql = "select id, d_short, d_long, def from `".DB_PREF."cms_language` where `visible` order by def, row_number  ";
    $rs = mysql_query($sql);
    if($rs){
      while($lock = mysql_fetch_assoc($rs)){
        $answer[] = $lock;
      }
    }else trigger_error($sql." ".mysql_error());
    return $answer;
  }

  
  function updatedRowsCount() {
    $infoStr = mysql_info();   
    preg_match('/Rows matched: ([0-9]*)/', $infoStr, $matches );
    return $matches[1];
  }  
    

}

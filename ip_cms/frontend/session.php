<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2011 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Frontend;

if (!defined('CMS')) exit;  


/**
 * Website language class
 * @package ImpressPages
 */ 
class Session{
  
  function __construct(){
    session_name(SESSION_NAME);   
    session_start();
  }

  /**
   * @return int loggedIn user id or false      
   */
  function userId(){
    if(isset($_SESSION['frontend_session']['user_id']))
      return $_SESSION['frontend_session']['user_id'];
    else
      return false; 
  }

  /**
   * @return bool true if user is logged in      
   */
  function loggedIn(){
    return isset($_SESSION['frontend_session']['user_id']) && $_SESSION['frontend_session']['user_id'] != null;      
  }

  /**
   * User logout      
   * @return void   
   */
  function logout(){
    if(isset($_SESSION['frontend_session']['user_id']))
      unset($_SESSION['frontend_session']['user_id']);
  }
  
  
  
  /**
   * User login  
   * @param int $id user id
   * @return void   
   */
  function login($id){
    $_SESSION['frontend_session']['user_id'] = $id;
  }
  
  

 
}

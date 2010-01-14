<?php 
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */
namespace Modules\standard\content_management;   
 
if (!defined('FRONTEND')&&!defined('BACKEND')) exit;
class Widget{
   var $errors;
   var $notes;
   function __construct(){
      $this->errors = array();
      $this->notes = array();
   }

   function init(){
   }

  function is_dynamic(){
    return false;
  }

  /* function get_translation_key(){
     return $this->translation_key;
   }
   function set_translation_key($translation_key){
     $this->translation_key = $translation_key;
   }

   function set_error($error){
     $this->errors[] = $error;
   }
   function set_note($note){
     $this->notes[] = $note;
   }
   
 
   function get_all_messages(){
     $answer = array();
     $answer[] = $errors;
     $answer[] = $notes;
     return $answer;
   }*/
}


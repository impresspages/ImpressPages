<?php
/**
 * @package		Library
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
namespace Library\Php\StandardModule;
 
if (!defined('BACKEND')) exit;

require_once(LIBRARY_DIR.'php/file/upload_image.php');


class element_photo extends Element{ //data element in area
  var $default_value;
  var $copies;
  var $mem_images;
  

  function __construct(){
    $this->mem_images = array();
    $this->copies = array();
    parent::__construct();
  }
   
  function print_field_new($prefix, $parent_id = null, $area = null){
    $html = new std_mod_html_output();  
    $html->input_file($prefix);                      
    return $html->html;
  }


  function print_field_update($prefix, $parent_id = null, $area = null){
  
  
    $value = null;
 
    if ($area){
       $sql = "select t.".$this->get_db_field()." from `".DB_PREF."".$area->get_db_table()."` t where ".$area->get_db_key()." = '".$parent_id."' ";
       $rs = mysql_query($sql);
       if (!$rs)
          trigger_error("Can not get photo field data. ".$sql);
       if ($lock = mysql_fetch_assoc($rs)){
         $value = $lock[''.$this->get_db_field()];
       }
    }

    /* translation */
      global $parametersMod;
      $delete_translation = '&nbsp;'.$parametersMod->getValue('developer', 'std_mod', 'admin_translations','delete').'&nbsp;';
    /*eof translation*/ 

    if(isset($this->mem_images[0]) && $this->mem_images[0] != ''){
      $image = BASE_URL.TMP_IMAGE_DIR.$this->mem_images[0];
    }else{
      $image = BASE_URL.$this->copies[0]['dest_dir'].$value;
    }      
      
    $html = new std_mod_html_output();  
      if($value || isset($this->mem_images[0]) && $this->mem_images[0] != '')
        $html->html('<span class="label"><img width="200" src="'.$image.'"/></span>');
      $html->input_file($prefix);        
      if($value  || isset($this->mem_images[0]) && $this->mem_images[0] != ''){
        $html->html('<span class="label"><input  class="stdModBox" type="checkbox" name="'.$prefix.'_delete"></span>');
        $html->html($delete_translation.'');
      }
                            
                            
    return $html->html;

         
  }
  

 
  function get_parameters($action, $prefix){

     return null; //array("name"=>$this->get_db_field(), "value"=>$_REQUEST[''.$prefix]);
  }


  function preview_value($value){
    if($value)    
      return '<img width="80" src="'.$this->copies[0]['dest_dir'].$value.'" >';
    else 
      return '';
  } 

  /*
  store immage to tmp folder
  */
  function memorize($prefix){

    
  }

  function check_field($prefix, $action){
    global $parametersMod;


    if(isset($_POST[$prefix.'_delete']) && $this->required)
      return $parametersMod->getValue('developer', 'std_mod','admin_translations','error_required');
  

  
    $this->new_mem_images = array();
    foreach($this->copies as $key => $copy){
      $upload_image = new \Library\Php\File\UploadImage();
      $error = $upload_image->upload($prefix,$copy['width'], $copy['height'], TMP_IMAGE_DIR, $copy['type'], $copy['forced'], $copy['quality']); 
      if($error == UPLOAD_ERR_OK){
        $this->new_mem_images[$key] = $upload_image->fileName;
      }elseif($error ==  UPLOAD_ERR_NO_FILE && $this->required && (sizeof($this->mem_images) != sizeof($this->copies)) && $action== 'insert'){
        return $parametersMod->getValue('developer', 'std_mod','admin_translations','error_required');
      }elseif($error ==  UPLOAD_ERR_NO_FILE){
        return null;
      }elseif($error == UPLOAD_ERR_EXTENSION)
        return $parametersMod->getValue('developer', 'std_mod','admin_translations','error_file_type').' JPEG, GIF';
      else{
        return $parametersMod->getValue('developer', 'std_mod','admin_translations','error_file_upload')." ".$error;
      }          
    }
    
    if(sizeof($this->new_mem_images) > 0)
      $this->mem_images = $this->new_mem_images;
    elseif(isset($_POST[$prefix.'_delete']))
      $this->mem_images = array();
    
    return null;
  }

  function process_insert( $prefix, $area,$id){
    
    // delete photo selected    
    if(isset($_POST[$prefix.'_delete']) && !$this->required)
      $this->mem_images = array();
    // eof delete photo selected
     

    if(sizeof($this->mem_images) == sizeof($this->copies)){
      require_once(LIBRARY_DIR.'php/file/functions.php');
      foreach($this->copies as $key => $copy){
        $new_name = \Library\Php\File\Functions::genUnocupiedName($this->mem_images[$key], $copy['dest_dir']);
        if(copy(TMP_IMAGE_DIR.$this->mem_images[$key],$copy['dest_dir'].$new_name)){
           $sql = "update `".DB_PREF."".$area->get_db_table()."` set `".$copy['db_field']."` = '".mysql_real_escape_string($new_name)."' where ".$area->get_db_key()." = '".$id."' ";
           $rs = mysql_query($sql);
           if (!$rs)
             trigger_error("Can't update photo field ".$sql);
        }else
          trigger_error("Can't copy file from ".htmlspecialchars(TMP_IMAGE_DIR.$this->mem_images[$key])." to ".htmlspecialchars($copy['dest_dir'].$new_name));
      }
    }


  

  }
  function process_update($prefix, $area, $id){


    global $parametersMod;




    // delete photo selected    
    if(isset($_POST[$prefix.'_delete']) && !$this->required ){
    
      foreach($this->copies as $key => $copy){
        $sql = "select `".$copy['db_field']."` as existing_photo from `".DB_PREF."".$area->get_db_table()."` where ".$area->get_db_key()." = '".$id."' ";
        $rs = mysql_query($sql); 
        if ($rs){
           if ($lock = mysql_fetch_assoc($rs)){
             if($lock['existing_photo'] != "" && file_exists($copy['dest_dir'].$lock['existing_photo']))
                unlink($copy['dest_dir'].$lock['existing_photo']);
           }
        }else{
          trigger_error("Can't get field to update ".$sql);
          return;
        }
      
      }
    
      
      foreach($this->copies as $key => $copy){
        
         $sql = "update `".DB_PREF."".$area->get_db_table()."` set `".$copy['db_field']."` = NULL where ".$area->get_db_key()." = '".$id."' ";
         $rs = mysql_query($sql);
         if (!$rs)
           trigger_error("Can't update photo field ".$sql);
      }    
    
    } 
    
    // eof delete photo selected
     

    if(sizeof($this->mem_images) == sizeof($this->copies)){
      require_once(LIBRARY_DIR.'php/file/functions.php');
      foreach($this->copies as $key => $copy){
        $new_name = \Library\Php\File\Functions::genUnocupiedName($this->mem_images[$key], $copy['dest_dir']);
        if(copy(TMP_IMAGE_DIR.$this->mem_images[$key],$copy['dest_dir'].$new_name)){
           $sql = "update `".DB_PREF."".$area->get_db_table()."` set `".$copy['db_field']."` = '".$new_name."' where ".$area->get_db_key()." = '".$id."' ";
           $rs = mysql_query($sql);
           if (!$rs)
             trigger_error("Can't update photo field ".$sql);
        }else
          trigger_error("Can't copy file from ".htmlspecialchars(TMP_IMAGE_DIR.$this->mem_images[$key])." to ".htmlspecialchars($copy['dest_dir'].$new_name));
      }
    }



    
    




  }
  function process_delete($area, $id){

    // delete photo selected
    
    
      foreach($this->copies as $key => $copy){
          $sql = "select `".$copy['db_field']."` as existing_photo from `".DB_PREF."".$area->get_db_table()."` where ".$area->get_db_key()." = '".$id."' ";
          $rs = mysql_query($sql); 
          if ($rs){
             if ($lock = mysql_fetch_assoc($rs)){
               if($lock['existing_photo'] != "" && file_exists($copy['dest_dir'].$lock['existing_photo']))
                  unlink($copy['dest_dir'].$lock['existing_photo']);
             }
          }else{
            trigger_error("Can't get field to update ".$sql);
            return;
          }
        
         $sql = "update `".DB_PREF."".$area->get_db_table()."` set `".$copy['db_field']."` = NULL where ".$area->get_db_key()." = '".$id."' ";
         $rs = mysql_query($sql);
         if (!$rs)
           trigger_error("Can't update photo field ".$sql);
      }    
    
  }



  function get_filter_option($value){
    return " ".$this->db_field." like '%".$value."%' ";
  }

  function reset(){

  }

}


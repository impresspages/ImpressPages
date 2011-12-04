<?php
/**
 * @package		Library
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
namespace Library\Php\StandardModule;

if (!defined('BACKEND')) exit;

require_once (LIBRARY_DIR.'php/file/upload_file.php');


class element_file extends Element{ //data element in area
    var $default_value;
    var $mem_file;
    var $extensions;

    function __construct(){
        $this->tmp_images = array();
        $this->extensions = array();

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

        if(isset($this->mem_file) && $this->mem_file != ''){
            $file = TMP_FILE_DIR.$this->mem_file;
        }elseif($value != ''){
            $file = $this->dest_dir.$value;
        }
        $html = new std_mod_html_output();
        $html->input_file($prefix);
        if($value || isset($this->mem_file) && $this->mem_file != '')
        $html->html('<span class="label"><a target="_blank" href="'.$file.'" >'.htmlspecialchars(basename($file)).'</a></span>');
        if($value  || isset($this->tmp_images[0]) && $this->tmp_images[0] != ''){
            $html->html('<span class="label"><input class="stdModBox" type="checkbox" name="'.$prefix.'_delete"></span>');
            $html->html($delete_translation.'');
        }


        return $html->html;

         
    }



    function get_parameters($action, $prefix){

        return null; //array("name"=>$this->get_db_field(), "value"=>$_REQUEST[''.$prefix]);
    }


    function preview_value($value){
        if($value)
        return '<a target="_blank" href="'.FILE_DIR.$value.'" >'.htmlspecialchars(basename($value)).'</a>';
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

        $upload_file = new \Library\Php\File\UploadFile();
        if(isset($_FILES[$prefix])){
            $error = $upload_file->upload($prefix, TMP_FILE_DIR);
            if($error == UPLOAD_ERR_OK){
                if(sizeof($this->extensions) == 0){
                    $this->mem_file = $upload_file->fileName;
                    return null;
                }else{
                    $good = false;
                    foreach($this->extensions as $key => $extension)
                    if($_FILES[$prefix]['type'] == $extension)
                    $good = true;
                    if($good){
                        $this->mem_file = $upload_file->fileName;
                        return null;
                    }else{
                        return $parametersMod->getValue('developer', 'std_mod','admin_translations','error_file_type').' '.implode(', ', $this->extensions);
                    }
                }

                return null;
            }elseif($error ==  UPLOAD_ERR_NO_FILE && $this->required && !isset($this->mem_file) && $action== 'insert'){
                return $parametersMod->getValue('developer', 'std_mod','admin_translations','error_required');
            }elseif($error ==  UPLOAD_ERR_NO_FILE){
                return null;
            }elseif($error == UPLOAD_ERR_EXTENSION)
            return $parametersMod->getValue('developer', 'std_mod','admin_translations','error_file_type').' '.implode(', ', $this->extensions);
            else{
                return $parametersMod->getValue('developer', 'std_mod','admin_translations','error_file_upload')." ".$error;
            }

        }

        if(isset($_POST[$prefix.'_delete']) && $this->required)
        return $parametersMod->getValue('developer', 'std_mod','admin_translations','error_required');

        return null;
    }

    function process_insert( $prefix, $area,$id){
        global $parametersMod;
        // delete photo selected
        if(isset($_POST[$prefix.'_delete']) && !$this->required)
        unset($this->mem_file);
        // eof delete photo selected
         

        if(isset($this->mem_file) && $this->mem_file != ''){
            require_once(LIBRARY_DIR.'php/file/functions.php');
            $new_name = \Library\Php\File\Functions::genUnocupiedName($this->mem_file, $this->dest_dir);
            if(copy(TMP_FILE_DIR.$this->mem_file,$this->dest_dir.$new_name)){
                $sql = "update `".DB_PREF."".$area->get_db_table()."` set `".$this->db_field."` = '".$new_name."' where ".$area->get_db_key()." = '".$id."' ";
                $rs = mysql_query($sql);
                if (!$rs)
                trigger_error("Can't update photo field ".$sql);
            }else
            trigger_error("Can't copy file from ".htmlspecialchars(TMP_FILE_DIR.$this->mem_file)." to ".htmlspecialchars($this->dest_dir.$new_name));
        }
    }
    function process_update($prefix, $area, $id){
        if(isset($_POST[$prefix.'_delete']) && !$this->required || isset($this->mem_file) && $this->mem_file != ''){
            $sql = "select `".$this->db_field."` as existing_file from `".DB_PREF."".$area->get_db_table()."` where ".$area->get_db_key()." = '".$id."' ";
            $rs = mysql_query($sql);
            if ($rs){
                if ($lock = mysql_fetch_assoc($rs)){
                    if($lock['existing_file'] != "" && file_exists($this->dest_dir.$lock['existing_file']))
                    unlink($this->dest_dir.$lock['existing_file']);
                }
            }else{
                trigger_error("Can't get field to update ".$sql);
                return;
            }
        }

        // delete file selected
        if(isset($_POST[$prefix.'_delete']) && !$this->required){
            $sql = "update `".DB_PREF."".$area->get_db_table()."` set `".$this->db_field."` = NULL where ".$area->get_db_key()." = '".$id."' ";
            $rs = mysql_query($sql);
            if (!$rs)
            trigger_error("Can't update photo field ".$sql);
        }
        // eof delete file selected
         
        if(isset($this->mem_file) && $this->mem_file != ''){
            require_once(LIBRARY_DIR.'php/file/functions.php');
            $new_name = \Library\Php\File\Functions::genUnocupiedName($this->mem_file, $this->dest_dir);
            if(copy(TMP_FILE_DIR.$this->mem_file,$this->dest_dir.$new_name)){
                $sql = "update `".DB_PREF."".$area->get_db_table()."` set `".$this->db_field."` = '".$new_name."' where ".$area->get_db_key()." = '".$id."' ";
                $rs = mysql_query($sql);
                if (!$rs)
                trigger_error("Can't update photo field ".$sql);
            }else
            trigger_error("Can't copy file from ".htmlspecialchars(TMP_FILE_DIR.$this->mem_file)." to ".htmlspecialchars($this->dest_dir.$new_name));
        }
    }
    function process_delete($area, $id){

        // delete photo selected


        $sql = "select `".$this->db_field."` as existing_file from `".DB_PREF."".$area->get_db_table()."` where ".$area->get_db_key()." = '".$id."' ";
        $rs = mysql_query($sql);
        if ($rs){
            if ($lock = mysql_fetch_assoc($rs)){
                if($lock['existing_file'] != "" && file_exists($this->dest_dir.$lock['existing_file']))
                unlink($this->dest_dir.$lock['existing_file']);
            }
        }else{
            trigger_error("Can't get field to update ".$sql);
            return;
        }

        $sql = "update `".DB_PREF."".$area->get_db_table()."` set `".$this->db_field."` = NULL where ".$area->get_db_key()." = '".$id."' ";
        $rs = mysql_query($sql);
        if (!$rs)
        trigger_error("Can't update photo field ".$sql);

    }

    function print_search_field($level, $key){
        if (isset($_GET['search'][$level][$key]))
        $value = $_GET['search'][$level][$key];
        else
        $value = '';
        return '<input name="search['.$level.']['.$key.']" value="'.htmlspecialchars($value).'" />';
    }

    function get_filter_option($value){
        return " ".$this->db_field." like '%".mysql_real_escape_string($value)."%' ";
    }



    function reset(){
        unset($this->mem_file);

    }

}


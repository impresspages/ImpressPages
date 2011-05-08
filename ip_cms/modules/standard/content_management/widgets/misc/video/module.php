<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management\Widgets\misc\video;

if (!defined('CMS')) exit;

const GROUP_KEY = 'misc';
const MODULE_KEY = 'video';

global $site;

$site->requireTemplate ('standard/content_management/widgets/misc/video/template.php');

require_once(BASE_DIR.LIBRARY_DIR.'php/file/functions.php');

class Module extends \Modules\standard\content_management\Widget{



  function init(){
    global $site;    
    $answer =
     '<script type="text/javascript" src="'.BASE_URL.CONTENT_MODULE_URL.'misc/video/module.js"></script>
      <div style="display: none;"><input style="display: none;" type="hidden" id="mod_video_action_after_photo_save" value="" /></div>
     <iframe style="display: none; width: 0px; height: 0px; border: 0;" name="mod_video_iframe" onload="f_mod_video_after_photo_save()" width="400" height="200"></iframe>  
     <script type="text/javascript">
      //<![CDATA[
       function f_mod_video_after_photo_save(){
          eval(document.getElementById(\'mod_video_action_after_photo_save\').value);
       }
       //]]>
     </script>';

    $site->requireConfig('standard/content_management/widgets/'.GROUP_KEY.'/'.MODULE_KEY.'/config.php');
    
    $layouts = Config::getLayouts();
    $script = '';
    if(!isset($layouts) || sizeof($layouts) == 0){
      $layouts = array();
      $layouts[] = array('translation'=>'', 'name'=>'default');
    }
    
    foreach($layouts as $key => $layout){
      $script .= '<option value="'.addslashes($layout['name']).'" >'.addslashes($layout['translation']).'</option>';
    }
    
    if(sizeof($layouts) <=1)
      $script = '<div class="ipCmsModuleLayout hidden"><label class="ipCmsTitle">Layout: </label><select name="layout">'.$script.'</select></div>';
    else
      $script = '<div class="ipCmsModuleLayout"><label class="ipCmsTitle">Layout: </label><select name="layout">'.$script.'</select></div>';
        

    $answer .= '
    <script type="text/javascript" >
    //<![CDATA[
    mod_video_layout = \''.$script.'\';
     //]]>
    </script>
    ';


    return $answer;
  }
  
  function getData($id) {
    $sql = "select * from `".DB_PREF."mc_misc_video` where `id` = '".(int)$id."' ";
    $rs = mysql_query($sql);
    if($rs){
        $data = mysql_fetch_assoc($rs);
        return $data;
    }   
    return false;   
  }    
  

  function getLayout($id){
    $sql = "select * from `".DB_PREF."mc_misc_video` where id = '".(int)$id."'";
    $rs = mysql_query($sql);
    if($rs){
      if($lock = mysql_fetch_assoc($rs)){
        $layout = $lock['layout'];
        return $layout;
      }
    } else {
      trigger_error($sql.' '.mysql_error());
    }
    return false;
  }


  function add_to_modules($mod_management_name, $collection_number, $module_id, $visible){ //add existing module from database to javascript array
    global $site;
    $site->requireTemplate('standard/content_management/widgets/'.GROUP_KEY.'/'.MODULE_KEY.'/template.php');
    $answer = "";
    $sql = "select title, photo from `".DB_PREF."mc_misc_video` where id = '".(int)$module_id."' ";
    $rs = mysql_query($sql);
    if (!$rs || !$lock = mysql_fetch_assoc($rs))
    trigger_error("Can't get module information ".$sql);
    else{
      $answer = "";
      $answer .= '<script type="text/javascript">
                  //<![CDATA[
                  ';
      $answer .= "  var new_module = new content_mod_video();";
      $answer .= "  var new_module_name = '".$mod_management_name.".' + ".$mod_management_name.".get_modules_array_name() + '[".$collection_number."]';";
      $answer .= "  new_module.init(".$collection_number.", ".$module_id.", ".$visible.", new_module_name, ".$mod_management_name.");";
      $answer .= "  new_module.preview_html = '".str_replace('script',"scr' + 'ipt", str_replace("\r", "", str_replace("\n", "' + \n '", str_replace("'", "\\'", Template::generateHtml($lock['title'], VIDEO_DIR.urlencode($lock['photo']), true, $this->getLayout($module_id))))))."';";
      $answer .= "  new_module.layout = '".str_replace("\r", "", str_replace("\n", "' + \n '", str_replace("'", "\\'",$this->getLayout($module_id))))."';";
      $answer .= "  new_module.set_title('".$lock['title']."');";
      $answer .= "  new_module.set_existing_photo('".$lock['photo']."');";
      $answer .= "  ".$mod_management_name.".get_modules().push(new_module);";
      $answer .= "  ";
      $answer .= "  ";
      $answer .= "  //]]>";
      $answer .= "</script>";

       
    }
    return $answer;
  }

  function create_new_instance($values){
    $new_name = $values['new_photo'];
    $ext_pos = strrpos($values['new_photo'], ".");
    if ($new_name != ""){
      $new_name = \Library\Php\File\Functions::genUnocupiedName($new_name, BASE_DIR.VIDEO_DIR);
    }


    if ($new_name != ''){
      copy(TMP_VIDEO_DIR.$values['new_photo'], VIDEO_DIR.$new_name);
    }
    if (true){
      $sql = "insert into `".DB_PREF."mc_misc_video` set layout= '".mysql_real_escape_string($values['layout'])."',title = '".mysql_real_escape_string($values['title'])."', photo = '".mysql_real_escape_string($new_name)."' ";
      $rs = mysql_query($sql);
      if(!$rs){
        return "Can't insert new module. ".$sql;
      }else{
        $sql = "select max(id) as max_id from `".DB_PREF."mc_misc_video` where 1";
        $rs = mysql_query($sql);
        if (!$rs)
        $this->set_error("Can't get last inserted id ".$sql);
        else{
          $lock = mysql_fetch_assoc($rs);
          $sql = "insert into `".DB_PREF."content_element_to_modules` set".
                " row_number = '".(int)$values['row_number']."', element_id = '".(int)$values['content_element_id']."' ".
                ", group_key='misc', module_key='video', module_id = '".(int)$lock['max_id']."'".
                ", visible= '".(int)$values['visible']."' ";
          $rs = mysql_query($sql);
          if (!$rs)
          $this->set_error("Can't asociate element to module ".$sql);
        }
      }
    }
    return;
  }

  function update($values){
    if(isset($values['new_photo']) && $values['new_photo'] != null){
      if (isset($values['existing_photo']) && $values['existing_photo'] != null){
        if (file_exists(VIDEO_DIR.$values['existing_photo'])){
          if ($values['existing_photo'] != '' && file_exists(VIDEO_DIR.$values['existing_photo'])){
            if(!unlink(VIDEO_DIR.$values['existing_photo'])){
              $this->set_error("Can't delete old photo.");
            }
          }
        }
      }


      $new_name = $values['new_photo'];
      if ($new_name != ""){
        $new_name = \Library\Php\File\Functions::genUnocupiedName($new_name, BASE_DIR.VIDEO_DIR);

        copy(TMP_VIDEO_DIR.$values['new_photo'], VIDEO_DIR.$new_name);
      }


    }
    else{
      $new_name = $values['existing_photo'];
    }


    $sql = "update `".DB_PREF."content_element_to_modules` set visible='".(int)$values['visible']."',row_number = '".(int)$values['row_number']."' where module_id = '".(int)$values['id']."' and group_key = '".mysql_real_escape_string(GROUP_KEY)."' and module_key = '".mysql_real_escape_string(MODULE_KEY)."'   ";
    if (!mysql_query($sql))
    return("Can't update module row number".$sql);
    else{
      $sql = "update `".DB_PREF."mc_misc_video` set layout = '".mysql_real_escape_string($values['layout'])."', title = '".mysql_real_escape_string($values['title'])."', photo = '".mysql_real_escape_string($new_name)."' where id = '".(int)$values['id']."' ";
      if (!mysql_query($sql))
      $this->set_error("Can't update module ".$sql);
    }
    return;
  }

  function delete($values){

    if (isset($values['existing_photo']) && $values['existing_photo'] != null && file_exists(VIDEO_DIR.$values['existing_photo'])){
      if ($values['existing_photo'] != '' && file_exists(VIDEO_DIR.$values['existing_photo'])){
        if(!unlink(VIDEO_DIR.$values['existing_photo'])){
          $this->set_error("Can't delete photo.");
        }
      }
    }



    $sql = "delete from `".DB_PREF."content_element_to_modules` where module_id = '".(int)$values['id']."'  and group_key = '".mysql_real_escape_string(GROUP_KEY)."' and module_key = '".mysql_real_escape_string(MODULE_KEY)."'";
    if (!mysql_query($sql))
    $this->set_error("Can't delete element to module association ".$sql);
    else{
      $sql = "delete from `".DB_PREF."mc_misc_video` where id = '".(int)$values['id']."' ";
      if (!mysql_query($sql))
      $this->set_error("Can't delete module ".$sql);
    }
    return;

  }

  function delete_by_id($id){

    $sql = "select photo from `".DB_PREF."mc_misc_video` where id = '".(int)$id."'";
    $rs = mysql_query($sql);
    if ($rs && $lock = mysql_fetch_assoc($rs)){


      if ($lock['photo'] != '' && file_exists(VIDEO_DIR.$lock['photo'])){
        if(!unlink(VIDEO_DIR.$lock['photo'])){
          $this->set_error("Can't delete photo.");
        }
      }


      $sql = "delete from `".DB_PREF."content_element_to_modules` where module_id = '".(int)$id."'  and group_key = '".mysql_real_escape_string(GROUP_KEY)."' and module_key = '".mysql_real_escape_string(MODULE_KEY)."'";
      if (!mysql_query($sql))
      $this->set_error("Can't delete element to module association ".$sql);
      else{
        $sql = "delete from `".DB_PREF."mc_misc_video` where id = '".$id."' ";
        if (!mysql_query($sql))
        $this->set_error("Can't delete module ".$sql);
      }


    }else
    trigger_error("Can't get data about photo paragraph ".$sql);


    return;
  }



  function make_html($id){
    global $site;
     
    $layout = $this->getLayout($id);

    $site->requireTemplate('standard/content_management/widgets/'.GROUP_KEY.'/'.MODULE_KEY.'/template.php');
    $sql = "select title, photo from `".DB_PREF."mc_misc_video` where id = '".(int)$id."' ";
    $rs = mysql_query($sql);
    if ($rs){
      if ($lock = mysql_fetch_assoc($rs)){
        if ($lock['photo'] != null){
          return Template::generateHtml($lock['title'], VIDEO_DIR.urlencode($lock['photo']), false, $layout);
        }
        else return;
      }
    }else
    trigger_error("Can't get photo to create HTML ".$sql);
  }

  function manager_preview(){
    global $site;
    $site->requireTemplate('standard/content_management/widgets/'.GROUP_KEY.'/'.MODULE_KEY.'/template.php');
    if(isset($_REQUEST['new_photo']) && $_REQUEST['new_photo'] != null)
    return Template::generateHtml($_REQUEST['title'], TMP_VIDEO_DIR.urlencode($_REQUEST['new_photo']), true, $_REQUEST['layout']);
    else
    return Template::generateHtml($_REQUEST['title'], VIDEO_DIR.urlencode($_REQUEST['existing_photo']), true, $_REQUEST['layout']);
  }

   


  function set_error($error){
    global $globalWorker;
    $globalWorker->set_error($error);
  }

}


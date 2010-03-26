<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management\Widgets\text_photos\text_photo;

if (!defined('CMS')) exit;

const GROUP_KEY = 'text_photos';
const MODULE_KEY = 'text_photo';




class Module extends \Modules\standard\content_management\Widget{


  function init(){
    global $site;    
    $answer =
     '<script type="text/javascript"  src="'.BASE_URL.CONTENT_MODULE_URL.'text_photos/text_photo/module.js"></script>
    <div style="display: none;"><input type="hidden" id="mod_text_photo_action_after_photo_save" value="" /></div>
    <iframe style="display: none; width: 0px; height: 0px; border: 0;" name="mod_text_photo_iframe" onload="f_mod_text_photo_after_photo_save()" width="400" height="200"></iframe> 
     <script type="text/javascript" >
      //<![CDATA[
       function f_mod_text_photo_after_photo_save(){
          eval(document.getElementById(\'mod_text_photo_action_after_photo_save\').value);
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
    mod_text_photo_layout = \''.$script.'\';
     //]]>
    </script>
    ';


    return $answer;
  }

  function getLayout($id){
    $sql = "select * from `".DB_PREF."mc_text_photos_text_photo` where id = '".(int)$id."'";
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
    $sql = "select text, title, photo, photo_big from `".DB_PREF."mc_text_photos_text_photo` where id = '".(int)$module_id."' ";
    $rs = mysql_query($sql);
    if (!$rs || !$lock = mysql_fetch_assoc($rs))
    trigger_error("Can't get module information ".$sql);
    else{
      if($lock['photo']){
        $preview_photo = BASE_URL.IMAGE_DIR.$lock['photo'];
        $preview_photo_big = BASE_URL.IMAGE_DIR.$lock['photo_big'];
      }else{
        $preview_photo = '';
        $preview_photo_big = '';
      }


      $answer = "";
      $answer .= '<script type="text/javascript">
                  //<![CDATA[
                  ';
      $answer .= "  var new_module = new content_mod_text_photo();";
      $answer .= "  var new_module_name = '".$mod_management_name.".' + ".$mod_management_name.".get_modules_array_name() + '[".$collection_number."]';";
      $answer .= "  new_module.init(".$collection_number.", ".$module_id.", ".$visible.", new_module_name, ".$mod_management_name.");";
      $answer .= "  new_module.preview_html = '".str_replace('script',"scr' + 'ipt", str_replace("\r", "", str_replace("\n", "' + \n '", str_replace("'", "\\'", Template::generateHtml($lock['title'], $preview_photo, $preview_photo_big, $lock['text'], $this->getLayout($module_id))))))."';";
      $answer .= "  new_module.layout = '".str_replace("\r", "", str_replace("\n", "' + \n '", str_replace("'", "\\'",$this->getLayout($module_id))))."';";
      $answer .= "  new_module.set_title('".addslashes($lock['title'])."');";
      $answer .= "  new_module.set_text('".addslashes(str_replace("\r", " ",str_replace("\n", " ",$lock['text'])))."');";
      $answer .= "  new_module.set_existing_photo('".$lock['photo']."');";
      $answer .= "  new_module.set_existing_bigphoto('".$lock['photo_big']."');";
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
    if ($new_name != ""){
      $new_name = substr($new_name, 0, strrpos($new_name, ".") );
      if (file_exists(IMAGE_DIR.$new_name.'.jpg')){
        $i = 1;
        while(file_exists(IMAGE_DIR.$new_name.'_'.$i.'.jpg')){
          $i++;
        }
        $new_name .= '_'.$i;
      }
      $new_name .= ".jpg";
    }

    $new_bigname = $values['new_bigphoto'];
    if ($new_bigname != ""){
      $new_bigname = substr($new_bigname, 0, strrpos($new_bigname, ".") );
      if (file_exists(IMAGE_DIR.$new_bigname.'.jpg') || $new_bigname.".jpg" == $new_name){
        $i = 1;
        while(file_exists(IMAGE_DIR.$new_bigname.'_'.$i.'.jpg') ||  $new_bigname.'_'.$i.'.jpg' == $new_name){
          $i++;
        }
        $new_bigname .= '_'.$i;
      }
      $new_bigname .= ".jpg";
    }


    if ($new_bigname != '' && $new_name != ''){
      copy(TMP_IMAGE_DIR.$values['new_photo'], IMAGE_DIR.$new_name);
      copy(TMP_IMAGE_DIR.$values['new_bigphoto'], IMAGE_DIR.$new_bigname);
    }
    if (true){
      $sql = "insert into `".DB_PREF."mc_text_photos_text_photo` set layout= '".mysql_real_escape_string($values['layout'])."' , text='".mysql_real_escape_string($values['text'])."', title = '".mysql_real_escape_string($values['title'])."', photo = '".mysql_real_escape_string($new_name)."', photo_big = '".mysql_real_escape_string($new_bigname)."' ";
      $rs = mysql_query($sql);
      if(!$rs){
        return "Can't insert new module. ".$sql;
      }else{
        $sql = "select max(id) as max_id from `".DB_PREF."mc_text_photos_text_photo` where 1";
        $rs = mysql_query($sql);
        if (!$rs)
        $this->set_error("Can't get last inserted id ".$sql);
        else{
          $lock = mysql_fetch_assoc($rs);
          $sql = "insert into `".DB_PREF."content_element_to_modules` set".
                " row_number = '".(int)$values['row_number']."', element_id = '".(int)$values['content_element_id']."' ".
                ", group_key='text_photos', module_key='text_photo', module_id = '".(int)$lock['max_id']."'".
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
        if (file_exists(IMAGE_DIR.$values['existing_photo'])){
          if (!unlink(IMAGE_DIR.$values['existing_photo'])){
            $this->set_error("Can't delete old photo.");
          }
        }
      }

      if (isset($values['existing_bigphoto']) && $values['existing_bigphoto'] != null){
        if (file_exists(IMAGE_DIR.$values['existing_bigphoto'])){
          if (!unlink(IMAGE_DIR.$values['existing_bigphoto'])){
            $this->set_error("Can't delete old photo.");
          }
        }
      }

      $new_name = $values['new_photo'];
      if ($new_name != ""){
        $new_name = substr($new_name, 0, strrpos($new_name, ".") );
        if (file_exists(IMAGE_DIR.$new_name.'.jpg')){
          $i = 1;
          while(file_exists(IMAGE_DIR.$new_name.'_'.$i.'.jpg')){
            $i++;
          }
          $new_name.'_'.$i;
        }
        $new_name .= '.jpg';
        copy(TMP_IMAGE_DIR.$values['new_photo'], IMAGE_DIR.$new_name);
      }

      $new_bigname = $values['new_bigphoto'];
      if ($new_bigname != ""){
        $new_bigname = substr($new_bigname, 0, strrpos($new_bigname, ".") );
        if (file_exists(IMAGE_DIR.$new_bigname.'.jpg') || $new_bigname.".jpg" == $new_name){
          $i = 1;
          while(file_exists(IMAGE_DIR.$new_bigname.'_'.$i.'.jpg') ||  $new_bigname.'_'.$i.'.jpg' == $new_name){
            $i++;
          }
          $new_bigname .= '_'.$i;
        }
        $new_bigname .= ".jpg";
        copy(TMP_IMAGE_DIR.$values['new_bigphoto'], IMAGE_DIR.$new_bigname);
      }

    }
    else{
      $new_name = $values['existing_photo'];
      $new_bigname = $values['existing_bigphoto'];
    }


    $sql = "update `".DB_PREF."content_element_to_modules` set visible='".(int)$values['visible']."', row_number = '".(int)$values['row_number']."' where module_id = '".(int)$values['id']."' and group_key = '".mysql_real_escape_string(GROUP_KEY)."' and module_key = '".mysql_real_escape_string(MODULE_KEY)."'   ";
    if (!mysql_query($sql))
    return("Can't update module row number".$sql);
    else{
      $sql = "update `".DB_PREF."mc_text_photos_text_photo` set layout = '".mysql_real_escape_string($values['layout'])."', `text` = REPLACE('".mysql_real_escape_string($values['text'])."', `base_url`, '".mysql_real_escape_string(BASE_URL)."'), photo = '".mysql_real_escape_string($new_name)."', photo_big = '".mysql_real_escape_string($new_bigname)."', `base_url` = '".mysql_real_escape_string(BASE_URL)."' where id = '".(int)$values['id']."'  ";
      if (!mysql_query($sql))
      $this->set_error("Can't update module ".$sql);
    }
    return;
  }

  function delete($values){

    if (isset($values['existing_photo']) && $values['existing_photo'] != null && file_exists(IMAGE_DIR.$values['existing_photo'])){
      if (!unlink(IMAGE_DIR.$values['existing_photo'])){
        $this->set_error("Can't delete photo.");
      }
    }

    if (isset($values['existing_bigphoto']) && $values['existing_bigphoto'] != null && file_exists(IMAGE_DIR.$values['existing_bigphoto'])){
      if (!unlink(IMAGE_DIR.$values['existing_bigphoto'])){
        $this->set_error("Can't delete photo.");
      }
    }


    $sql = "delete from `".DB_PREF."content_element_to_modules` where module_id = '".(int)$values['id']."'  and group_key = '".mysql_real_escape_string(GROUP_KEY)."' and module_key = '".mysql_real_escape_string(MODULE_KEY)."'";
    if (!mysql_query($sql))
    $this->set_error("Can't delete element to module association ".$sql);
    else{
      $sql = "delete from `".DB_PREF."mc_text_photos_text_photo` where id = '".(int)$values['id']."' ";
      if (!mysql_query($sql))
      $this->set_error("Can't delete module ".$sql);
    }
    return;

  }

  function delete_by_id($id){

    $sql = "select photo, photo_big from `".DB_PREF."mc_text_photos_text_photo` where id = '".(int)$id."'";
    $rs = mysql_query($sql);
    if ($rs && $lock = mysql_fetch_assoc($rs)){


      if (!unlink(IMAGE_DIR.$lock['photo'])){
        $this->set_error("Can't delete photo.");
      }

      if (!unlink(IMAGE_DIR.$lock['photo_big'])){
        $this->set_error("Can't delete photo.");
      }


      $sql = "delete from `".DB_PREF."content_element_to_modules` where module_id = '".(int)$id."'  and group_key = '".mysql_real_escape_string(GROUP_KEY)."' and module_key = '".mysql_real_escape_string(MODULE_KEY)."'";
      if (!mysql_query($sql))
      $this->set_error("Can't delete element to module association ".$sql);
      else{
        $sql = "delete from `".DB_PREF."mc_text_photos_text_photo` where id = '".(int)$id."' ";
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
    $sql = "select title, text, photo, photo_big from `".DB_PREF."mc_text_photos_text_photo` where id = '".(int)$id."' ";
    $rs = mysql_query($sql);
    if ($rs){
      if ($lock = mysql_fetch_assoc($rs)){
        if($lock['photo'])
        $lock['photo'] = BASE_URL.IMAGE_DIR.$lock['photo'];
        if($lock['photo_big'])
        $lock['photo_big'] = BASE_URL.IMAGE_DIR.$lock['photo_big'];
        return Template::generateHtml($lock['title'], $lock['photo'], $lock['photo_big'], $lock['text'], $layout);
      }
    }else
    trigger_error("Can't get photo to create HTML ".$sql);
  }
   
  function manager_preview(){
    global $site;
    $site->requireTemplate('standard/content_management/widgets/'.GROUP_KEY.'/'.MODULE_KEY.'/template.php');
    return Template::generateHtml($_REQUEST['title'], $_REQUEST['photo'], $_REQUEST['photo_big'], $_REQUEST['text'], $_REQUEST['layout']);
  }


  function set_error($error){
    global $globalWorker;
    $globalWorker->set_error($error);
  }
  function clearCache() {
    $sql = "update `".DB_PREF."mc_text_photos_text_photo` set `text` = REPLACE(`text`, `base_url`, '".mysql_real_escape_string(BASE_URL)."'), `base_url` = '".mysql_real_escape_string(BASE_URL)."' where base_url <> '".mysql_real_escape_string(BASE_URL)."' ";
    $rs = mysql_query($sql);
    if (!$rs) {
      trigger_error($sql." ".mysql_error());
    }
  }


}


<?php 
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management\Widgets\misc\rich_text;   
 
if (!defined('CMS')) exit;

const GROUP_KEY = 'misc';
const MODULE_KEY = 'rich_text';

global $site;
$site->requireTemplate('standard/content_management/widgets/misc/rich_text/template.php');

class Module extends \Modules\standard\content_management\Widget{

 
  function init(){
    global $site;
    require_once(BASE_DIR.LIBRARY_DIR.'php/js/functions.php');
    $answer = '';
    $answer .= ' <script type="text/javascript"  src="'.BASE_URL.CONTENT_MODULE_URL.'misc/rich_text/module.js"></script>';

     
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
        
    
    $answer .= "
    <script type=\"text/javascript\" >
    //<![CDATA[
      mod_rich_text_layout = '".$script."';
      configWidgetMiscRichTextMceInit = '".\Library\Php\Js\Functions::htmlToString(str_replace("\\", "\\\\",Config::getMceInit()))."';
     //]]>
    </script>
    ";
    
      		 
    return $answer;
  }
  
  function getLayout($id){
    $sql = "select * from `".DB_PREF."mc_misc_rich_text` where id = '".(int)$id."'";
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
     $sql = "select text from `".DB_PREF."mc_misc_rich_text` where id = '".(int)$module_id."' ";
     $rs = mysql_query($sql);
     if (!$rs || !$lock = mysql_fetch_assoc($rs))
       trigger_error("Can't get module information ".$sql);
     else{
       $answer = "";
       $answer .= '<script type="text/javascript">
                  //<![CDATA[
                  ';
       $answer .= "  var new_module = new content_mod_rich_text();";
//       $answer .= "  var new_module_name = '".$mod_management_name."' + ".$mod_management_name.".get_modules_array_name() + '[' + ".$mod_management_name.".get_modules.length + ']';";
       $answer .= "  var new_module_name = '".$mod_management_name.".' + ".$mod_management_name.".get_modules_array_name() + '[".$collection_number."]';";
       $answer .= "  new_module.init(".$collection_number.", ".$module_id.", ".$visible.", new_module_name, ".$mod_management_name.");";
       $answer .= "  new_module.preview_html = '".str_replace('script',"scr' + 'ipt", str_replace("\r", "", str_replace("\n", "' + \n '", str_replace("'", "\\'", Template::generateHtml($lock['text'], $this->getLayout($module_id))))))."';";
       $answer .= "  new_module.layout = '".str_replace("\r", "", str_replace("\n", "' + \n '", str_replace("'", "\\'",$this->getLayout($module_id))))."';";
       $answer .= "  new_module.set_text('".addslashes(str_replace("\r", " ",str_replace("\n", " ", $lock['text'])))."');";
       $answer .= "  ".$mod_management_name.".get_modules().push(new_module);";
       $answer .= "  ";
       $answer .= "  ";
       $answer .= "  //]]>";
       $answer .= "</script>";
     }  
     return $answer;
   }

   function create_new_instance($values){
      $sql = "insert into `".DB_PREF."mc_misc_rich_text` set layout= '".mysql_real_escape_string($values['layout'])."', text = '".mysql_real_escape_string($values['text'])."' ";
      $rs = mysql_query($sql);
      if(!$rs){
         return "Can't insert new module. ".$sql;
      }else{
         $sql = "select max(id) as max_id from `".DB_PREF."mc_misc_rich_text` where 1";
         $rs = mysql_query($sql);
         if (!$rs)
            return "Can't get last inserted id ".$sql;  
         else{
            $lock = mysql_fetch_assoc($rs);
            $sql = "insert into `".DB_PREF."content_element_to_modules` set".
            " row_number = '".(int)$values['row_number']."', element_id = '".(int)$values['content_element_id']."' ".
            ", group_key='misc', module_key='rich_text', module_id = '".(int)$lock['max_id']."'".
            ", visible= '".(int)$values['visible']."' ";
            $rs = mysql_query($sql);
            if (!$rs)
               set_error("Can't asociate element to module ".$sql);
          
         }  
      }
   }

   function update($values){
   
      $sql = "update `".DB_PREF."content_element_to_modules` set visible='".(int)$values['visible']."',row_number = '".(int)$values['row_number']."' where  module_id = '".(int)$values['id']."' and group_key = '".mysql_real_escape_string(GROUP_KEY)."' and module_key = '".mysql_real_escape_string(MODULE_KEY)."'   ";
      if (!mysql_query($sql))
        return("Can't update module row number".$sql);
      else{
        $text = $values['text'];
        $sql = "update `".DB_PREF."mc_misc_rich_text` set `text` = '".mysql_real_escape_string($text)."' where id = '".(int)$values['id']."'  ";
        if (!mysql_query($sql))
          set_error("Can't update module ".$sql);
       
      }

   }

   function delete($values){
      $sql = "delete from `".DB_PREF."content_element_to_modules` where module_id = '".(int)$values['id']."'  and group_key = '".mysql_real_escape_string(GROUP_KEY)."' and module_key = '".mysql_real_escape_string(MODULE_KEY)."' ";
      if (!mysql_query($sql))
        return("Can't delete element to module association ".$sql);
      else{
        $sql = "delete from `".DB_PREF."mc_misc_rich_text` where id = '".(int)$values['id']."' ";
        if (!mysql_query($sql))
          set_error("Can't delete module ".$sql);
       
      }
   }

   function delete_by_id($id){
      $sql = "delete from `".DB_PREF."content_element_to_modules` where module_id = '".(int)$id."'  and group_key = '".mysql_real_escape_string(GROUP_KEY)."' and module_key = '".mysql_real_escape_string(MODULE_KEY)."' ";
      if (!mysql_query($sql))
        trigger_error("Can't delete element to module association ".$sql);
      else{
        $sql = "delete from `".DB_PREF."mc_misc_rich_text` where id = '".(int)$id."' ";
        if (!mysql_query($sql))
          trigger_error("Can't delete module ".$sql);       
      }
   }



   function make_html($id){
     global $site;
     
     $layout = $this->getLayout($id);
          
     $site->requireTemplate('standard/content_management/widgets/'.GROUP_KEY.'/'.MODULE_KEY.'/template.php');
      $sql = "select text from `".DB_PREF."mc_misc_rich_text` where id = '".(int)$id."' ";
      $rs = mysql_query($sql);
      if ($rs){
        if ($lock = mysql_fetch_assoc($rs)){
          return Template::generateHtml($lock['text'], $layout);
        }
      }else
        trigger_error("Can't get text to create HTML ".$sql);      
   }
   
   function manager_preview(){
     global $site;
     $site->requireTemplate('standard/content_management/widgets/'.GROUP_KEY.'/'.MODULE_KEY.'/template.php');
     return Template::generateHtml($_REQUEST['text'], $_REQUEST['layout']); 
   }
   
   function clearCache($cachedBaseUrl) {
     $sql = "update `".DB_PREF."mc_misc_rich_text` set `text` = REPLACE(`text`, '".mysql_real_escape_string($cachedBaseUrl)."', '".mysql_real_escape_string(BASE_URL)."')  where 1 ";
     $rs = mysql_query($sql);
     if (!$rs) {
       trigger_error($sql." ".mysql_error());
     }
   }

  function updateLinks($oldUrl, $newUrl) {
    $sql = "update `".DB_PREF."mc_misc_rich_text` set `text` = REPLACE(`text`, '".mysql_real_escape_string($oldUrl)."', '".mysql_real_escape_string($newUrl)."') where 1 ";
    $rs = mysql_query($sql);
    if (!$rs) {
      trigger_error($sql." ".mysql_error());
    }
  }


}


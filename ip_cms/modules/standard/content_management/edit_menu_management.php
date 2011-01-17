<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license		GNU/GPL, see ip_license.html
 */
namespace Modules\standard\content_management;  

if (!defined('FRONTEND')&&!defined('BACKEND')) exit; 


require_once (__DIR__.'/db.php');

define('CONTENT_MODULE_URL', MODULE_DIR.'standard/content_management/widgets/');


class EditMenuManagement {
  var $current_element;
  var $modules;
  var $db_module;
  var $mod_group; //to find module id and send to worker
  var $mod_name; //to find module id and send to worker
  function __construct($current_element) {



    $this->db_module = new Db();
    $this->current_element = $current_element;
    $this->mod_group = "standard";
    $this->mod_name = "content_management";
    $this->module_url = MODULE_DIR.'standard/content_management/';
  }

  function manageElement() {
    global $parametersMod;
    global $site;

    $answer = '';
    $answer = '<link href="'.BASE_URL.$this->module_url.'design/style.css" rel="stylesheet" type="text/css" />';


    $answer .= '

			
    <script type="text/javascript">
      //<![CDATA[
      var management_header = document.createElement("div");
      var current_zone= "'.addslashes($site->currentZone).'";
      management_header.style.height= \'117px\';
      document.body.insertBefore(management_header, document.body.childNodes[0]);
      //]]>
    </script> 
    ';

    $tmp_module = \Db::getModule('', $this->mod_group, $this->mod_name);
    $answer .= $this->tep_modules_init($this->modules);
    $answer .= '<script  type="text/javascript" src="'.BASE_URL.$this->module_url.'edit_menu_saver.js"></script>';
    $answer .= '<script type="text/javascript">
             //<![CDATA[
             var menu_saver = new edit_menu_saver();
             document.write(menu_saver.init(document.getElementById(\'modules\'), '.$this->current_element.', mod_management.get_modules(),\'page_title\', \'menu_saver\', \''.BASE_URL.BACKEND_WORKER_FILE.'\', '.$tmp_module['id'].'));
             //]]>
          </script>
          ';   



    $answer .= '
      <script type="text/javascript">
      //<![CDATA[
        LibDefault.addEvent(window, \'beforeunload\', close_check);

        function close_check(evt) {
          if (typeof evt == \'undefined\') {
            evt = window.event;
          }

          if(mod_management.changed == true){
            evt.returnValue = \''.$parametersMod->getValue('standard', 'content_management','admin_translations','warning_save').'\';
            return \''.$parametersMod->getValue('standard', 'content_management','admin_translations','warning_save').'\';
            //return false;
          }

        }
      //]]>
      </script>
		';

    $cur_url = $site->generateUrl(null, $site->currentZone);
    if(strpos($cur_url, "?") !== false)
      $cur_url = substr($cur_url, 0, strpos($cur_url, "?"));

    $tmpModule = \Db::getModule('', 'standard', 'content_management');
    $workerUrl = BASE_URL.BACKEND_WORKER_FILE."?module_id=".$tmpModule['id'].'&security_token='.$_SESSION['backend_session']['security_token'];

    $answer .= '
    
  <script type="text/javascript">
    function mod_standard_content_management_key_check(e){
      var evt = e || window.event;
      if(evt.keyCode == 27){
        f_main_fields_popup_close();
      }
      
    }
    document[\'onkeyup\'] = mod_standard_content_management_key_check;
  </script>    
    <div id="main_fields_popup" style="display: none;">
    <div  onclick="LibDefault.cancelBubbling(event)" id="main_fields_popup_border" class="ipCmsBorder">
    <div class="ipCmsHead">
    <img
    alt="Close"
    onmouseover="this.src=\''.BASE_URL.MODULE_DIR.'standard/content_management/design/popup_close_hover.gif\'"
    onmouseout="this.src=\''.BASE_URL.MODULE_DIR.'standard/content_management/design/popup_close.gif\'"
    onclick="f_main_fields_popup_close()" style="cursor: pointer; float: right;" src="'.BASE_URL.MODULE_DIR.'standard/content_management/design/popup_close.gif"/>
    '.$parametersMod->getValue('standard', 'content_management', 'admin_translations','man_additional_page_properties').'
    </div>
    <div class="ipCmsManagement" >
    <form  id="f_main_fields_popup" action="" onsubmit="f_main_fields_popup_save(); return false;">
    <div>
    <label class="ipCmsTitle">'.htmlspecialchars($parametersMod->getValue('standard', 'content_management', 'admin_translations','man_additional_button_title')).'</label>
    <div class="ipCmsInput"><input name="page_button_title"  value="" /></div><br />

    <label class="ipCmsTitle">'.htmlspecialchars($parametersMod->getValue('standard', 'content_management', 'admin_translations','man_additional_page_title')).'</label>
    <div class="ipCmsInput"><input name="page_page_title"  value="" /></div><br />

    <label class="ipCmsTitle">'.htmlspecialchars($parametersMod->getValue('standard', 'content_management', 'admin_translations','man_additional_keywords')).'</label>
    <div class="ipCmsInput"><input name="keywords"  value=""/></div><br />

    <label class="ipCmsTitle">'.htmlspecialchars($parametersMod->getValue('standard', 'content_management', 'admin_translations','man_additional_description')).'</label>
    <textarea rows="5" cols="30" name="description"></textarea><br /><br />

    <label class="ipCmsTitle">'.htmlspecialchars($parametersMod->getValue('standard', 'content_management', 'admin_translations','man_additional_url')).' '.$cur_url.'</label>
    <div style="width: " class="ipCmsInput"><input name="url" value=""/></div><br />

    <label class="ipCmsTitle">'.htmlspecialchars($parametersMod->getValue('standard', 'content_management', 'admin_translations','man_additional_type')).'</label>
    <div>
    <label class="ipCmsTitle"><input id="f_main_fields_type_default" name="type" type="radio" value="default" />&nbsp;'.htmlspecialchars($parametersMod->getValue('standard', 'content_management', 'admin_translations','man_additional_no_redirect')).'</label>
    <label class="ipCmsTitle"><input id="f_main_fields_type_inactive" name="type" type="radio" value="inactive" />&nbsp;'.htmlspecialchars($parametersMod->getValue('standard', 'content_management', 'admin_translations','man_additional_inactive')).'</label>
    <label class="ipCmsTitle"><input id="f_main_fields_type_subpage" name="type" type="radio" value="subpage" />&nbsp;'.htmlspecialchars($parametersMod->getValue('standard', 'content_management', 'admin_translations','man_additional_subpage')).'</label>
    <label class="ipCmsTitle"><input id="f_main_fields_type_redirect" name="type" type="radio" value="redirect" />&nbsp;'.htmlspecialchars($parametersMod->getValue('standard', 'content_management', 'admin_translations','man_additional_redirect')).'</label>
    <p id="f_main_fields_redirect_error" class="ipCmsError">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'error_type_url_empty')).'</p>
    <div class="ipCmsInput"><input name="redirect_url" value=""/></div>
    </div><br />

    <label class="ipCmsTitle">'.htmlspecialchars($parametersMod->getValue('standard', 'content_management', 'admin_translations','man_additional_visible')).'</label>
    <input type="checkbox" name="visible" /><br /><br />

    <label class="ipCmsTitle">'.htmlspecialchars($parametersMod->getValue('standard', 'content_management', 'admin_translations','man_additional_add_rss')).'</label>
    <input type="checkbox" name="rss" /><br /><br />

    <label class="ipCmsTitle">'.htmlspecialchars($parametersMod->getValue('standard', 'content_management', 'admin_translations','man_additional_created_on')).'</label>
    <p id="f_main_fields_created_on_error" class="ipCmsError">'.htmlspecialchars($parametersMod->getValue('standard', 'content_management', 'admin_translations', 'man_additional_error_date_format')).' '.date("Y-m-d").'</p>
    <div class="ipCmsInput"><input name="created_on"  value="" /></div>

    <input type="submit" style="width:0px; height: 0px; overflow: hidden; border: 0pt none;" />
    </div>
    </form>
    </div>
    <div class="ipCmsModuleControlButtons">
    <a onclick="f_main_fields_popup_save();" class="ipCmsButton">'.$parametersMod->getValue('standard', 'content_management', 'admin_translations','man_paragraph_confirm').'</a>
    <a onclick="f_main_fields_popup_close();"class="ipCmsButton">'.$parametersMod->getValue('standard', 'content_management', 'admin_translations','man_paragraph_cancel').'</a>
    <div style="clear: both;"></div>
    </div>
    </div>
    </div>
    <div id="manHead">
    '.$this->tep_modules().'
    <div class="ipCmsAttributes">
    '.$this->tep_title_input().'
    </div>
    </div>
    <script type="text/javascript">
    //<![CDATA[
    function f_main_fields_popup_show(){
    var form = document.getElementById((\'f_main_fields\'));
    var form_popup = document.getElementById((\'f_main_fields_popup\'));

    form_popup.page_button_title.value = form.page_button_title.value;
    form_popup.page_page_title.value = form.page_page_title.value;
    form_popup.url.value = form.url.value;
    form_popup.keywords.value = form.keywords.value;
    form_popup.description.value = form.description.value;
    form_popup.created_on.value = form.created_on.value;
    form_popup.redirect_url.value = form.redirect_url.value;
    if(form.rss.value == 1)
    form_popup.rss.checked = true;
    else
    form_popup.rss.checked = false;

    if(form.visible.value == 1)
    form_popup.visible.checked = true;
    else
    form_popup.visible.checked = false;

    document.getElementById(\'f_main_fields_type_default\').checked = true;
    if(form.type.value == \'inactive\')
    document.getElementById(\'f_main_fields_type_inactive\').checked = true;
    if(form.type.value == \'subpage\')
    document.getElementById(\'f_main_fields_type_subpage\').checked = true;
    if(form.type.value == \'redirect\')
    document.getElementById(\'f_main_fields_type_redirect\').checked = true;

    var popup = document.getElementById(\'main_fields_popup\');
    var border = document.getElementById(\'main_fields_popup_border\');

    popup.style.display = \'block\';
    form_popup.page_page_title.focus();
    border.style.marginTop = Math.abs((LibWindow.getWindowHeight() - border.offsetHeight)/2) + \'px\';

    }
    function f_main_fields_popup_close(){
    document.getElementById(\'main_fields_popup\').style.display = \'none\';
    }



    function f_main_fields_popup_save(){
      var form_popup = document.getElementById((\'f_main_fields_popup\'));
      var type = \'\';
      if (document.getElementById(\'f_main_fields_type_redirect\').checked)
      type = \'redirect\';
      LibDefault.ajaxMessage(\''.$workerUrl.'\', \'action=check_parameters&date=\' + form_popup.created_on.value + \'&redirect_url=\' + form_popup.redirect_url.value + \'&type=\' + type);
      return false;
    }
    function f_main_fields_popup_save_process(){
      document.getElementById(\'f_main_fields_created_on_error\').style.display = \'none\';
      document.getElementById(\'f_main_fields_redirect_error\').style.display = \'none\';

      //mod_management.changed = true;
      mod_management.setChanged(true);

      var form = document.getElementById((\'f_main_fields\'));
      var form_popup = document.getElementById((\'f_main_fields_popup\'));

      form.page_button_title.value = form_popup.page_button_title.value;
      form.page_page_title.value = form_popup.page_page_title.value;
      form.url.value = form_popup.url.value;
      form.keywords.value = form_popup.keywords.value;
      form.description.value = form_popup.description.value;
      form.redirect_url.value = form_popup.redirect_url.value;
      form.created_on.value = form_popup.created_on.value;

      if (form_popup.rss.checked == true)
      form.rss.value = 1;
      else
      form.rss.value = 0;

      if (form_popup.visible.checked == true)
      form.visible.value = 1;
      else
      form.visible.value = 0;

      form.type.value = \'default\';
      if(document.getElementById(\'f_main_fields_type_inactive\').checked)
      form.type.value = \'inactive\';
      if(document.getElementById(\'f_main_fields_type_subpage\').checked)
      form.type.value = \'subpage\';
      if(document.getElementById(\'f_main_fields_type_redirect\').checked)
      form.type.value = \'redirect\';


      f_main_fields_popup_close();


      return false;
    }

    function f_main_fields_popup_click(e){
      var border = document.getElementById(\'main_fields_popup_border\');
      var mouseY = LibMouse.getMouseY(e);
      var mouseX = LibMouse.getMouseX(e);
      if(mouseY < LibPositioning.getY(border) || mouseY > LibPositioning.getY(border) + border.offsetHeight)
      f_main_fields_popup_close();
      if(mouseX < LibPositioning.getX(border) || mouseX > LibPositioning.getX(border) + border.offsetWidth)
      f_main_fields_popup_close();
    }

    //LibDefault.addEvent(document.getElementById(\'main_fields_popup\'), \'mousedown\', f_main_fields_popup_click);
    //]]>
    </script>
					
					
					

     <!-- parameters edit popup -->


    <div id="mod_content_management_popup_parameter" onclick="mod_content_management_parameter_cancel();">
        <div onclick="LibDefault.cancelBubbling(event)" class="ipCmsBorder" style="margin-top: 37px;">
          <div class="ipCmsHead">
                  <img onclick="mod_content_management_parameter_cancel();" src="'.BASE_URL.MODULE_DIR.'standard/content_management/design/popup_close.gif" style="cursor: pointer; float: right;" onclick="f_main_fields_popup_close()" onmouseout="this.src=\''.BASE_URL.MODULE_DIR.'standard/content_management/design/popup_close.gif\'" onmouseover="this.src=\''.BASE_URL.MODULE_DIR.'standard/content_management/design/popup_close_hover.gif\'" alt="Close"/>
                  Page properties
          </div>
          <div class="ipCmsManagement">
              <form onsubmit="mod_content_management_parameter_save(); return false;" action="" id="mod_content_management_popup_form">
                <label id="mod_content_management_popup_form_title" class="ipCmsTitle"></label>
                <div id="mod_content_management_popup_form_string">
<div class="ipCmsInput"><input value="" name="field_string"/></div>
                </div>
                <div id="mod_content_management_popup_form_textarea">
<textarea value="" name="field_textarea"></textarea>
                </div>
                <div id="mod_content_management_popup_form_wysiwyg">
<textarea value="" id="parameters" name="field_wysiwyg"></textarea>
                </div>
                <input id="mod_content_management_popup_form_parameter_id" type="hidden" />
                <input id="mod_content_management_popup_form_parameter_type" type="hidden" />
                <input type="submit" style="border: 0pt none ; overflow: hidden; width: 0px; height: 0px; float:left;"/>
              </form>
          </div>
          <div class="ipCmsModuleControlButtons">
            <a class="ipCmsButton" onclick="mod_content_management_parameter_save();" >Confirm</a>
            <a class="ipCmsButton" onclick="mod_content_management_parameter_cancel();" >Cancel</a>
            <div style="clear: both;"></div>
          </div>
        </div>
    </div>
    <script type="text/javascript">
      //<![CDATA[
        //var mod_content_management_parameters = new Array();
        function mod_content_management_parameter_manage(parameter_id, title, type, event){
          document.getElementById(\'mod_content_management_popup_parameter\').style.display = \'block\';
          document.getElementById(\'mod_content_management_popup_form_title\').innerHTML = title;
          document.getElementById(\'mod_content_management_popup_form_parameter_id\').value = parameter_id;
          document.getElementById(\'mod_content_management_popup_form_parameter_type\').value = type;

          document.getElementById(\'mod_content_management_popup_form_string\').style.display = \'none\';
          document.getElementById(\'mod_content_management_popup_form_textarea\').style.display = \'none\';
          document.getElementById(\'mod_content_management_popup_form_wysiwyg\').style.display = \'none\';


          switch(type){
            case \'string\':
              document.getElementById(\'mod_content_management_popup_form_string\').style.display = \'block\';
              document.getElementById(\'mod_content_management_popup_form\').field_string.value = eval(\'mod_content_management_parameters_\' + parameter_id + \'[1]\');
            break;
            case \'textarea\':
              document.getElementById(\'mod_content_management_popup_form_textarea\').style.display = \'block\';
              document.getElementById(\'mod_content_management_popup_form\').field_textarea.value = eval(\'mod_content_management_parameters_\' + parameter_id + \'[1]\');
            break;
            case \'wysiwyg\':
              document.getElementById(\'mod_content_management_popup_form_wysiwyg\').style.display = \'block\';
              var tmp_id = document.getElementById(\'mod_content_management_popup_form\').field_wysiwyg.id;
              value = tinyMCE.get(tmp_id).setContent(eval(\'mod_content_management_parameters_\' + parameter_id + \'[1]\'));
            break;
          }
        }
        function mod_content_management_parameter_cancel(){
          document.getElementById(\'mod_content_management_popup_parameter\').style.display = \'none\';
        }
        function mod_content_management_parameter_save(){
          mod_management.setChanged(true);
          //mod_management.changed = true;

          document.getElementById(\'mod_content_management_popup_parameter\').style.display = \'none\';
          var value = \'\';
          switch(document.getElementById(\'mod_content_management_popup_form_parameter_type\').value){
            case \'string\':
              value = document.getElementById(\'mod_content_management_popup_form\').field_string.value;
            break;
            case \'textarea\':
              value = document.getElementById(\'mod_content_management_popup_form\').field_textarea.value;
            break;
            case \'wysiwyg\':
             //value = document.getElementById(\'mod_content_management_popup_form\').field_wysiwyg.value;
             var tmp_id = document.getElementById(\'mod_content_management_popup_form\').field_wysiwyg.id;
             value = tinyMCE.get(tmp_id).getContent();
            break;
          }

          var parameter_id = document.getElementById(\'mod_content_management_popup_form_parameter_id\').value;
          var parameterControls = document.getElementById(\'mod_content_management_parameter_buttons_\' + parameter_id);
          var parameterNode = parameterControls.parentNode;
          parameterNode.removeChild(parameterControls);
          if(document.getElementById(\'mod_content_management_popup_form_parameter_type\').value != \'wysiwyg\'){
            var tmpNode = document.createTextNode(value);
            parameterNode.innerHTML = \'\';
            parameterNode.appendChild(tmpNode);
          }else
            parameterNode.innerHTML = value;

          if(value == \'\'){
            parameterNode.style.width = \'50px\';
            parameterNode.style.height = \'16px\';
          }
          parameterNode.insertBefore(parameterControls, parameterNode.firstChild);

          eval(\'mod_content_management_parameters_\' + parameter_id + \'[1] = value;\');
          eval(\'mod_content_management_parameters_\' + parameter_id + \'[3] = true;\'); //changed = true
        }
      //]]>
    </script>
         
          					
					<!-- eof parameters edit popup -->
    ';     




    return $answer;
  }
  function tep_modules() {
    global $parametersMod;
    $answer = '';
    $answer .= '
		<div class="ipCmsModuleGroups"><img alt="Up" onclick="switch_group(get_group()-1)" onmouseout="this.src=\''.BASE_URL.$this->module_url.'design/group_up.gif\'" onmouseover="this.src=\''.BASE_URL.$this->module_url.'design/group_up_act.gif\'" src="'.BASE_URL.$this->module_url.'design/group_up.gif"/><div class="ipCmsDots'.sizeof($this->modules).'">
    ';
    $i = 0;

    foreach($this->modules as $group_translation => $group) {
      $answer .= '
      <img  id="manHeadDot'.$i.'" class="ipCmsDot" onclick="switch_group('.$i.')" src="'.BASE_URL.$this->module_url.'design/group_dot.gif" alt="'.htmlspecialchars($group_translation).'" />
      ';
      $i++;
    }
    $answer .= '
				</div><img alt="Group" onclick="switch_group(get_group()+1)" onmouseout="this.src=\''.BASE_URL.$this->module_url.'design/group_down.gif\'" onmouseover="this.src=\''.BASE_URL.$this->module_url.'design/group_down_act.gif\'" src="'.BASE_URL.$this->module_url.'design/group_down.gif"/></div>
		
		';

    $answer .= '
      <script type="text/javascript">
             //<![CDATA[
        var menuModules = new Array();
				var menuModulesActiveGroup;
        {
        
    ';
    $i = 0;
    foreach($this->modules as $group_translation => $group) {
      $answer .= '
          var menuModulesGroup = new Array();
              
      ';
      foreach($group as $key => $module) {
        $answer .= '
          var module = new Array();
          module[0] = \''.addslashes($module['module_name']).'\';          
          module[1] = \''.addslashes($parametersMod->getValue('standard', 'content_management', 'widget_'.$module['module_name'], 'widget_title')).'\';
          module[2] = \''.addslashes($module['group_name']).'\';
          menuModulesGroup['.$key.'] = module;
          ';
      }
      $answer .= ' menuModules.push(menuModulesGroup);';
      $i++;
    }
    $answer .= '
        }
        
				
          function get_group(){
            ii = 0;
            while(document.getElementById(\'manHeadDot\' + ii)){
                    var tmp_dot = document.getElementById(\'manHeadDot\' + ii);
                    if(tmp_dot.src == \''.BASE_URL.$this->module_url.'design/group_dot_act.gif\')
                            return ii;
                    ii++;
            }
            return 0;
          }

          function switch_group(i){
            menuModulesActiveGroup = i;
            if(i > '.(sizeof($this->modules)-1).')
                    i = '.(sizeof($this->modules)-1).';
            if(i<0)
                    i = 0;
          var ii = 0;
          for(ii; ii<12; ii++){
						//document.getElementById(\'menuModButtonNew\' + ii).parentNode.style.display = \'none\';
            document.getElementById(\'menuModButtonNew\' + ii).style.background = \'none\';
						
            document.getElementById(\'menuModButtonNew\' + ii).new_paragraph_name = \'\';
            if(document.getElementById(\'menuModButtonNew\' + ii).childNodes.length > 0)
              document.getElementById(\'menuModButtonNew\' + ii).removeChild(document.getElementById(\'menuModButtonNew\' + ii).firstChild );       
            if(document.getElementById(\'menuModButtonNew\' + ii).childNodes.length > 0)
              document.getElementById(\'menuModButtonNew\' + ii).removeChild(document.getElementById(\'menuModButtonNew\' + ii).firstChild );       
            if(document.getElementById(\'menuModButtonNew\' + ii).childNodes.length > 0)
              document.getElementById(\'menuModButtonNew\' + ii).removeChild(document.getElementById(\'menuModButtonNew\' + ii).firstChild );       
          }
          ii = 0;
          for(ii; ii<menuModules[i].length; ii++){
            //document.getElementById(\'menuModButtonNew\' + ii).parentNode.style.display = \'block\';
            //document.getElementById(\'menuModButtonNew\' + ii).style.background = \'url('.BASE_URL.str_replace('/', '\\/', $this->module_url).'design\\/widgets\\/\' + menuModules[i][ii][0] + \'.jpg) no-repeat\';
            document.getElementById(\'menuModButtonNew\' + ii).new_paragraph_name = menuModules[i][ii][0];
            var mod_image = document.createElement(\'img\');
            mod_image.setAttribute(\'src\', \''.BASE_URL.CONTENT_MODULE_URL.'\' + menuModules[i][ii][2] + \'/\' + menuModules[i][ii][0] + \'/design/mod_\' + menuModules[i][ii][0] + \'.gif\');
            mod_image.orig_src = \''.BASE_URL.CONTENT_MODULE_URL.'\' + menuModules[i][ii][2] + \'/\' + menuModules[i][ii][0] + \'/design/mod_\' + menuModules[i][ii][0] + \'.gif\';
            mod_image.act_src = \''.BASE_URL.CONTENT_MODULE_URL.'\' + menuModules[i][ii][2] + \'/\' + menuModules[i][ii][0] + \'/design/mod_\' + menuModules[i][ii][0] + \'_act.gif\';
            mod_image.onmouseover = function(){
                    this.src = this.act_src;
            }
            mod_image.onmouseout = function(){
                    this.src = this.orig_src;
            }
            document.getElementById(\'menuModButtonNew\' + ii).appendChild(mod_image);       
            document.getElementById(\'menuModButtonNew\' + ii).appendChild(document.createElement(\'br\'));       
            document.getElementById(\'menuModButtonNew\' + ii).appendChild(document.createTextNode(menuModules[i][ii][1]));       
          }
					
          for(ii = menuModules[i].length; ii<12; ii++){
            document.getElementById(\'menuModButtonNew\' + ii).style.cursor = \'default\';
          }
		
					
          ii = 0;
          while(document.getElementById(\'manHeadDot\' + ii)){
                  var tmp_dot = document.getElementById(\'manHeadDot\' + ii);
                  if(i == ii)
                          tmp_dot.src = \''.BASE_URL.$this->module_url.'design/group_dot_act.gif\';
                  else
                          tmp_dot.src = \''.BASE_URL.$this->module_url.'design/group_dot.gif\';
                  ii++;
          }
					
        
        }
				
				
      //]]>
        
      </script>        
    ';




    for($i = 0; $i<12; $i++) {
      $answer .= '
  <div class="ipCmsModuleDrag">
    <div class="ipCmsDragable" id="menuModButtonNew'.$i.'"></div>
  </div>
        ';
    }

    $answer .= '
      <script type="text/javascript">
      //<![CDATA[
        switch_group(0);
      //]]>
      </script>
    ';


    $answer .= $this->init_draging();

    return $answer;
  }

  function init_draging() {
    $answer = '';
    $ids_array = array();
    $i = 0;
    $init_names = '';
    $ids_array[] = "hackDrag";
    for($i=0; $i<12; $i++) {
      $ids_array[] = 'menuModButtonNew'.$i;
    }
//     $init_names .= ' document.getElementById(\'menuModButtonNew'.$i.'\').new_paragraph_name = \''.$module['module_name'].'\';';

    $init_string = '';
    foreach($ids_array as $key => $module) {
      if($init_string != '')
        $init_string .= ', "'.$module.'"+TRANSPARENT';
      else
        $init_string .= '"'.$module.'"+SCROLL+TRANSPARENT';
    }

    $answer .= '<script type="text/javascript" src="'.BASE_URL.LIBRARY_DIR.'js/wzdrag_drop/wz_dragdrop.js"></script>';
    $answer .= '
    <script type="text/javascript">
    //<![CDATA[

    function my_PickFunc(){
      if(mod_management.state == \'drag\'){
      dd.obj.moveTo(dd.obj.orig_x, dd.obj.orig_y);
      }

      dd.obj.orig_x = dd.obj.x;
      dd.obj.orig_y = dd.obj.y;

      mod_management.calc_module_dimensions();

      if(dd.obj.name.substring(16, dd.obj.name.length) >= menuModules[menuModulesActiveGroup].length){
        return;
      }


      mod_management.state = \'drag\';
      LibDefault.addEvent(document, "mousemove", mod_management.show_insert_module);
      //document.getElementById(\'modules\').addEventListener("click", mod_management.show_insert_module, false);
    }

    function my_DragFunc(){
      if(dd.obj.name.substring(16, dd.obj.name.length) >= menuModules[menuModulesActiveGroup].length){
        return;
      }
      mod_management.state = \'drag\';
    }


    function my_DropFunc(){
      //alert(dd.obj.name);


      mod_management.state = \'wait\';
      dd.obj.moveTo(dd.obj.orig_x, dd.obj.orig_y);

      if(dd.obj.name.substring(16, dd.obj.name.length) >= menuModules[menuModulesActiveGroup].length){
      return;
      }


      var modules = document.getElementById(\'modules\');

      if(modules.new_paragraph_before !== \'\' && modules.new_paragraph_before != null){
        mod_management.insert_new_module(\'content_mod_\' + document.getElementById(dd.obj.name).new_paragraph_name, modules.new_paragraph_before);
      }

      LibDefault.removeEvent(document, "mousemove", mod_management.show_insert_module);
      if(document.getElementById(\'new_paragraph_\' + mod_management.module_dimensions.last_active))
      document.getElementById(\'new_paragraph_\' + mod_management.module_dimensions.last_active).style.display = \'none\';
    }

    //]]>
    </script>
    ';

    $answer .= '
      <script type="text/javascript">
        //<![CDATA[
        SET_DHTML('.$init_string.');
        '.$init_names.'
      //]]>
      </script>
      ';

    return $answer;
  }

  function tep_preview_save_cancel_buttons() {
    global $parametersMod;
    global $site;
    $answer = "";
    $answer .= '
    <span class="ipCmsButtonBg"><a class="ipCmsButton ipCmsButtonPreview" onclick="window.open(\''.str_replace("&cms_action=manage", "",str_replace("?cms_action=manage", "", $site->getCurrentUrl())).'\',\'mywindow\',\'width=600,height=450,toolbar=yes,location=yes,directories=yes,status=yes,menubar=yes,scrollbars=yes,copyhistory=yes,resizable=yes\')">'.$parametersMod->getValue('standard', 'content_management','admin_translations','preview').'</a></span>
    <span id="ipCmsButtonSaved" class="ipCmsButtonBg2"><span class="ipCmsButton ipCmsButtonSaved">'.htmlspecialchars($parametersMod->getValue('standard', 'content_management', 'admin_translations', 'saved')).'</span></span>
    <span id="ipCmsButtonSave" style="display: none;" class="ipCmsButtonBg"><a class="ipCmsButton ipCmsButtonOk" onclick="mod_management.changed = false; menu_saver.save_to_db()">'.$parametersMod->getValue('standard', 'content_management','admin_translations','man_save').'</a></span>
    <span id="ipCmsButtonWarningSave" style="display: none;" class="ipCmsButtonText">'.htmlspecialchars($parametersMod->getValue('standard', 'content_management', 'admin_translations', 'warning_not_saved')).'</span>
    <script type="text/javascript">
        //<![CDATA[
      function change_mode(){      
        var modules = document.getElementById(\'modules\');
        var new_value = \'\';
        if(mod_management.mode == \'separators\'){
          new_value = \'none\';
          mod_management.mode = \'no_separators\';
        }else{
          new_value = \'block\';
          mod_management.mode = \'separators\';
        }
          
          
        for(var i=0; i<modules.childNodes.length;  i++){
          for(var ii=0; ii<modules.childNodes[i].childNodes.length; ii++){
            if(modules.childNodes[i].childNodes[ii].getAttribute(\'class\') == \'paragraphSeparator\')
              modules.childNodes[i].childNodes[ii].style.display = new_value;
          }
        }
        
     
      }
      //]]>
    </script>
    ';
    return $answer;
  }

  function tep_modules_init(&$modules) {
    global $parametersMod;
    global $site;

    $site->requireConfig('developer/std_mod/config.php');

    $answer = '';
    $answer .= '<script type="text/javascript">
        //<![CDATA[
    ';

    $translations = $parametersMod->getGroups('standard', 'content_management');
    foreach($translations as $key => $translation_group) {
      foreach($translation_group as $key2 => $translation) {
        $answer .= "var translation_edit_menu_$key2 = '".addslashes($translation->value)."'; ";
      }
    }
    $answer .= '
      //]]>    
      </script>';

    //$answer .= '<script type="text/javascript" src="'.BASE_URL.LIBRARY_DIR.'js/tiny_mce/tiny_mce_gzip.js"></script>';
    $answer .= '<script type="text/javascript" src="'.BASE_URL.LIBRARY_DIR.'js/tiny_mce/tiny_mce.js"></script>';
    //$answer .= '<script type="text/javascript" src="'.BASE_URL.LIBRARY_DIR.'js/ext/adapter/prototype/prototype.js"></script>';

    $answer .= '
<script type="text/javascript">
//<![CDATA[
  '.\Modules\developer\std_mod\Config::getMceInit('mode: "exact"', 'elements : "tmp,parameters"', 'frontend').'


       
//]]>     
</script>
		'; 
    $answer .= '<script type="text/javascript" src="'.BASE_URL.LIBRARY_DIR.'js/positioning.js"></script>';
    $answer .= '<script type="text/javascript" src="'.BASE_URL.LIBRARY_DIR.'js/mouse.js"></script>';
    $answer .= '<script type="text/javascript" src="'.BASE_URL.LIBRARY_DIR.'js/windowsize.js"></script>';
    $answer .= '<script type="text/javascript" src="'.BASE_URL.$this->module_url.'module_dimensions.js"></script>';
    $answer .= '<script type="text/javascript" src="'.BASE_URL.$this->module_url.'edit_menu_management.js"></script>';
    $answer .= '<script type="text/javascript">
        //<![CDATA[
        var all_modules = Array();
        var all_modules_translations = Array();
      //]]>
      </script>';

    $sql = "select translation_key from `".DB_PREF."module` where type_menu order by row_number";
    $rs = mysql_query($sql);
    $modules = $this->db_module->menuModules();


    //tinymce styles
    $site->requireConfig('standard/content_management/config.php');
    $tinyMceStylesStr = '';
    foreach(Config::getMceStyles() as $style) {
      if($tinyMceStylesStr != '') {
        $tinyMceStylesStr .= ';';
      }
      $tinyMceStylesStr .= $style['translation'].'='.$style['css_style'];
    }
    //end tinymce styles


    $answer .= '<script type="text/javascript">
        //<![CDATA[
        var global_config_modules_url = \''.str_replace('/', '\\/', BASE_URL.MODULE_DIR).'\';
        var global_config_image_url = \''.str_replace('/', '\\/', BASE_URL.IMAGE_DIR).'\';
        var global_config_tmp_image_url = \''.str_replace('/', '\\/', BASE_URL.TMP_IMAGE_DIR).'\';
        var global_config_file_url = \''.str_replace('/', '\\/', BASE_URL.FILE_DIR).'\';
        var global_config_tmp_file_url = \''.str_replace('/', '\\/', BASE_URL.TMP_FILE_DIR).'\';
        var global_config_video_url = \''.str_replace('/', '\\/', BASE_URL.VIDEO_DIR).'\';
        var global_config_tmp_video_url = \''.str_replace('/', '\\/', BASE_URL.TMP_VIDEO_DIR).'\';
        var global_config_base_url = \''.str_replace('/', '\\/', BASE_URL).'\';
        var global_config_library_url = \''.str_replace('/', '\\/', LIBRARY_DIR).'\';
        var global_config_template_url = \''.str_replace('/', '\\/', THEME_DIR).'\';
        var global_config_template = \''.str_replace('/', '\\/', THEME).'\';
        var global_config_backend_worker_file = \''.str_replace('/', '\\/', BACKEND_WORKER_FILE).'\';
        var global_config_tiny_mce_styles = \''.str_replace('/', '\\/', $tinyMceStylesStr).'\';
        var global_config_security_token = \''.str_replace('/', '\\/', $_SESSION['backend_session']['security_token']).'\';
        //]]>
      </script>';      

    if ($modules) {
      require_once(CONTENT_MODULE_URL."widget.php");
      foreach($modules as $key => $group) {
        foreach($group as $key2 => $module) {
          eval(" require_once('".CONTENT_MODULE_URL.$module['group_name']."/".$module['module_name']."/module.php'); ");
          eval(' $new_module = new \\Modules\\standard\\content_management\\Widgets\\'.$module['group_name'].'\\'.$module['module_name'].'\\Module(); ');


          $answer .= $new_module->init();
          $answer .= '<script type="text/javascript">
              //<![CDATA[
              all_modules.push(\''. $module['module_name'] .'\');
              all_modules_translations.push(\''.$module['module_translation'].'\'); 
            ';

          $answer .= "
            //]]>
          </script>";  
        }
      }

      $answer .= '<script type="text/javascript">
          //<![CDATA[
          ';
      //$menu_mod_parameters = new standard_menu_management_parameter($this->db_module);
      //$menu_mod_parameters = $menu_mod_parameters->load_menu_mod_parameters($module['group_name'], $module['module_name']);
      $menu_mod_parameters = $parametersMod->getGroups('standard', 'content_management');
      foreach($menu_mod_parameters as $key3 => $parameter_g) {
        foreach($parameter_g as $key4 => $parameter) {
          $answer .= " var ".$key3."_".$key4." = '".addslashes($parameter->value)."'; " ;
        }
      }

      $answer .= "
        //]]>
      </script>";  


    }

    $answer .= '<div id="modules"></div>';


    $tmp_module = \Db::getModule('', $this->mod_group, $this->mod_name);
    $answer .= '
      <script type="text/javascript">
        //<![CDATA[
        var mod_management = new edit_menu_management();
        mod_management.init(document.getElementById(\'modules\'), all_modules, all_modules_translations, \'mod_management\', '.$tmp_module['id'].'); 
      //]]>
      </script>';

    $modules_in_page = $this->db_module->pageModules($this->current_element);
    if ($modules_in_page) {
      foreach ($modules_in_page as $key => $module) {
        //$menu_mod_parameters = new standard_menu_management_parameter($this->db_module);
        //$menu_mod_parameters = $menu_mod_parameters->load_menu_mod_parameters($module['group_name'], $module['module_name']);
        eval(' $tmp_module = new \\Modules\\standard\\content_management\\Widgets\\'.$module['group_name'].'\\'.$module['module_name'].'\\Module(); ');
        $answer .= $tmp_module->add_to_modules('mod_management', $key, $module['instance_id'], $module['visible']);
      }
    }

    $answer .= '
        <script type="text/javascript">
          //<![CDATA[
          mod_management.print();
          //]]>
        </script>';

    return $answer;
  }



  function tep_title_input() {
    global $parametersMod;
    global $cms;

    $lock = $this->db_module->menuElement($this->current_element);

    $tmp_module = \Db::getModule('', 'standard', 'content_management');

    if($lock['rss'] == 1)
      $rss = '1';
    else
      $rss = '0';

    if($lock['visible'] == 1)
      $visible = '1';
    else
      $visible = '0';

    $answer = '
          <form onsubmit="mod_management.changed = false; menu_saver.save_to_db(); return false;" id="f_main_fields" action="'.BASE_URL.BACKEND_WORKER_FILE."?module_id=".$tmp_module['id'].'&security_token='.$_SESSION['backend_session']['security_token'].'" method="post" enctype="multipart/form-data">  
            <span class="ipCmsTitle">'.$parametersMod->getValue('standard', 'content_management', 'admin_translations', 'man_additional_button_title').'</span>
            <input name="page_button_title" type="text" value="'.htmlspecialchars($lock['button_title']).'" />
            <a class="ipCmsAdvancedButton" onclick="f_main_fields_popup_show();">'.$parametersMod->getValue('standard', 'content_management', 'admin_translations','advanced').'</a>  
						
            <input name="page_page_title" type="hidden" value="'.htmlspecialchars($lock['page_title']).'" />
            <input name="keywords" type="hidden" value="'.htmlspecialchars($lock['keywords']).'"/>  
            <input name="description" type="hidden" value="'.htmlspecialchars($lock['description']).'" />
            <input name="url" type="hidden" value="'.htmlspecialchars($lock['url']).'"/>               
            <input name="created_on" type="hidden" value="'.htmlspecialchars(substr($lock['created_on'], 0, 10)).'"/>               
            <input name="type" type="hidden" value="'.htmlspecialchars($lock['type']).'"/>               
            <input name="redirect_url" type="hidden" value="'.htmlspecialchars($lock['redirect_url']).'"/>               
            <input name="rss" style="display: none;" type="hidden" value="'.$rss.'" />               
            <input name="visible" style="display: none;" type="hidden" value="'.$visible.'" />               
            <input type="hidden" name="action" value="set_main_fields" />  
            <input type="hidden" id="f_main_fields_answer_function" name="answer_function" value="" /> 
            <input type="hidden" id="f_main_fields_id" name="id" value="" />    
          </form>       

					<span class="ipCmsControllButtons">'.$this->tep_preview_save_cancel_buttons().'</span>
';

    return $answer;
  }

}


/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license		GNU/GPL, see ip_license.html
 */

function edit_menu_management(){
  var parent;
  var new_page_links_count;
  var all_modules;
  var all_modules_translations;
  var this_object;
  var modules;
  var changed; //true if document is not saved.
  this.module_dimesnions;
  this.print = print;
  this.init = init;
  this.insert_new_module = insert_new_module;
  this.module_control_buttons = module_control_buttons;
  this.module_manage = module_manage;
  this.hide_chose_new_module = hide_chose_new_module;
  this.module_control_buttons_management = module_control_buttons_management;
  this.module_preview_cancel = module_preview_cancel;
  this.module_preview_save = module_preview_save;
  this.get_modules = get_modules;
  this.print_existing_module = print_existing_module;
  this.module_delete = module_delete;
  this.module_change_visibility = module_change_visibility;
  this.module_up = module_up;
  this.module_down = module_down;
  this.get_modules_array_name = get_modules_array_name;
  this.module_preview_save_response = module_preview_save_response;
  this.module_preview_save_response_error = module_preview_save_response_error;
  this.show_insert_module = show_insert_module;
  this.make_module_div_table = make_module_div_table;
  this.window_resize_recalc = window_resize_recalc;
  this.calc_module_dimensions = calc_module_dimensions;
  this.confirm_all = confirm_all;
  this.state;
  this.mode;
  this.universal_preview_response = universal_preview_response;
  var module_id; //used when posting to worker
  function confirm_all(){
    for(var i=0; i<this.modules.length; i++){       //append existing modules
      if(this.modules[i].managed){
        this.module_preview_save(i, 1);
        this.modules[i].managed = false;
      }
    }
  }
     
  function init(parent, all_modules, all_modules_translations, this_object, module_id){
    this.parent = parent;
    this.all_modules = all_modules;
    this.all_modules_translations = all_modules_translations;
    this.this_object = this_object;
    this.modules = new Array();
    this.state = 'wait';
    this.mode = 'no_separators';
    this.module_id = module_id;
    this.changed = false;
    LibDefault.addEvent(window, 'load', window_resize_recalc);
    LibDefault.addEvent(window, 'resize', window_resize_recalc);
  }

  function print(){
    this.parent.innerHTML = '';
   
			
    for(var i=0; i<this.modules.length; i++){       //append existing modules
      this.print_existing_module(this.modules[i]);
    }
      
    empty_div = document.createElement("div");
    this.parent.appendChild(this.make_module_div_table('end', empty_div, false));

      
  }
  
  function module_control_buttons(collection_number, visible){
    var div = document.createElement('div');
    //div.setAttribute('name', 'moduleControlButtons');
    //div.setAttribute("className", 'ipCmsButtons');
    //div.setAttribute("class", 'ipCmsButtons');
    div.className = 'ipCmsButtons';
    div.setAttribute('id', 'ipCmsModuleControlButtons');
      
    var image_visibility;
    var visibility_action;
    if (visible == 1){
      image_visibility = global_config_modules_url + 'standard/content_management/design/icon_visible.gif';
      image_visibility_hover = global_config_modules_url + 'standard/content_management/design/icon_visible_hover.gif';
      visibility_action = translation_edit_menu_man_paragraph_hide;
    }else{
      image_visibility = global_config_modules_url + 'standard/content_management/design/icon_hidden.gif';
      image_visibility_hover = global_config_modules_url + 'standard/content_management/design/icon_hidden_hover.gif';
      visibility_action = translation_edit_menu_man_paragraph_show;
    }

    div.innerHTML = ''+
    ' <img title="' + translation_edit_menu_man_paragraph_edit + '" onmouseover="this.src=\'' + global_config_modules_url + 'standard/content_management/design/icon_edit_hover.gif\'" onmouseout="this.src=\'' + global_config_modules_url + 'standard/content_management/design/icon_edit.gif\'" src="' + global_config_modules_url + 'standard/content_management/design/icon_edit.gif" border="0"'+
    '   onclick="' + this.this_object + '.module_manage(' + collection_number + ')" />'+
    ' <img title="' + translation_edit_menu_man_paragraph_up + '" onmouseover="this.src=\'' + global_config_modules_url + 'standard/content_management/design/icon_up_hover.gif\'" onmouseout="this.src=\'' + global_config_modules_url + 'standard/content_management/design/icon_up.gif\'" src="' + global_config_modules_url + 'standard/content_management/design/icon_up.gif" border="0"'+
    '   onclick="' + this.this_object + '.module_up(' + collection_number + ')" />'+
    ' <img title="' + translation_edit_menu_man_paragraph_down + '" onmouseover="this.src=\'' + global_config_modules_url + 'standard/content_management/design/icon_down_hover.gif\'" onmouseout="this.src=\'' + global_config_modules_url + 'standard/content_management/design/icon_down.gif\'" src="' + global_config_modules_url + 'standard/content_management/design/icon_down.gif" border="0"'+
    '   onclick="' + this.this_object + '.module_down(' + collection_number + ')" />'+
    ' <img title="' + visibility_action + '" onmouseover="this.src=\'' + image_visibility_hover + '\'" onmouseout="this.src=\'' + image_visibility + '\'" src="' + image_visibility + '" border="0"'+
    '   onclick="' + this.this_object + '.module_change_visibility(' + collection_number + ')" />'+
    ' <img title="' + translation_edit_menu_man_paragraph_delete + '" onmouseover="this.src=\'' + global_config_modules_url + 'standard/content_management/design/icon_delete_hover.gif\'" onmouseout="this.src=\'' + global_config_modules_url + 'standard/content_management/design/icon_delete.gif\'" src="' + global_config_modules_url + 'standard/content_management/design/icon_delete.gif" border="0"'+
    '   onclick="' + this.this_object + '.module_delete(' + collection_number + ')" />'+
    '';
    return div;
  }

  function module_control_buttons_management(collection_number){
    var div = document.createElement('div');
    //      div.setAttribute('name', 'module_control_buttons');
    //      div.setAttribute(document.all ? "className" : "class", 'moduleControlButtons');
    //      div.setAttribute("className", 'ipCmsModuleControlButtons');
    //      div.setAttribute("class", 'ipCmsModuleControlButtons');
    div.className = 'ipCmsModuleControlButtons';
    div.innerHTML = ''+
    ' <a class="ipCmsButton" onclick="' + this.this_object + '.module_preview_save(' + collection_number + ')" href="javascript:void(0)">' + translation_edit_menu_man_paragraph_confirm + '</a>'+
    ' <a class="ipCmsButton" onclick="' + this.this_object + '.module_preview_cancel(' + collection_number + ')" href="javascript:void(0)">' + translation_edit_menu_man_paragraph_cancel + '</a> '+
    ' <div class="ipCmsClear"></div>';
         
    return div;
  }


 
  function insert_new_module(translation_key, insert_before_collection_number){
    this.confirm_all();
    var div = document.createElement('div');
    eval('var new_module =  new ' + translation_key + '();');
    eval ('new_module.init(' + this.modules.length + ', null, 1, \'' + this.this_object + '.' + this.get_modules_array_name() + '[' + this.modules.length + ']\', ' + this.this_object + ');');
    div.setAttribute('name', 'mod_collection_' + this.modules.length);
    div.setAttribute('id', 'mod_collection_' + this.modules.length);
    div.className = 'ipWidget ipCmsModuleManagement';


    this.modules.push(new_module);

    var tmp_div = document.createElement("div");
    //			tmp_div.setAttribute(document.all ? "className" : "class", 'moduleManagement moduleBorder');
    //			tmp_div.setAttribute("className", 'ipCmsModuleManagement ipCmsModuleBorder');
    //			tmp_div.setAttribute("class", 'ipCmsModuleManagement ipCmsModuleBorder');
    tmp_div.className = 'ipCmsModuleManagement ipCmsModuleBorder';
    tmp_div.appendChild(new_module.manage(this.modules.length-1));
    tmp_div.appendChild(this.module_control_buttons_management(this.modules.length-1));
    var table = this.make_module_div_table(this.modules.length-1, tmp_div, false);
    div.appendChild(table);

    this.modules[this.modules.length-1].managed = true;
    this.parent.insertBefore(div, document.getElementById('mod_collection_' + insert_before_collection_number));
    new_module.manage_init();
    this.changed = true;
    if(new_module.static){
      setTimeout(this.this_object + '.module_preview_save(' + (this.modules.length-1) + ', true);',500); //used the same frame_worker as for confirm all
    }
  }

  function print_existing_module(new_module){
    var module = document.createElement('div');
    module.innerHTML = new_module.preview_html;
    var div = this.make_module_div_table(new_module.collection_number, module, true);
      
    this.parent.insertBefore(div, document.getElementById('mod_collection_'));
    this.hide_chose_new_module();
  }

  function module_manage(collection_number){
    this.confirm_all();
    this.modules[collection_number].managed = true;

    var mod_div = document.getElementById('mod_collection_' + collection_number);
    mod_div.className = 'ipWidget ipCmsModuleManagement';

    mod_div.onmouseover = '';
    mod_div.onmouseout = '';
    mod_div.innerHTML = '';
				
    var tmp_div = document.createElement('div');
    //				tmp_div.setAttribute(document.all ? "className" : "class", 'moduleManagement moduleBorder');
    //				tmp_div.setAttribute("className", 'ipCmsModuleManagement ipCmsModuleBorder');
    //				tmp_div.setAttribute("class", 'ipCmsModuleManagement ipCmsModuleBorder');
    tmp_div.className = 'ipCmsModuleManagement ipCmsModuleBorder';
				
    tmp_div.appendChild(this.modules[collection_number].manage(collection_number));
    tmp_div.appendChild(this.module_control_buttons_management(collection_number));

    var table = this.make_module_div_table(collection_number, tmp_div, false);

    mod_div.appendChild(table);
    this.modules[collection_number].manage_init();
				
    if(this.modules[collection_number].static){
      setTimeout(this.this_object + '.module_preview_save(' + collection_number + ', true);',500);  //used the same frame_worker as for confirm all
    }else
      this.changed = true;
  }

  function module_up(collection_number){
    if(collection_number == null) return;
        
    var div = document.getElementById("mod_collection_" + collection_number);

    var target = div.previousSibling;
    if (target != null){
      this.parent.removeChild(div);
      this.parent.insertBefore(div, target);
    }
    this.changed = true;
  }

  function module_down(collection_number){
    if(collection_number == null) return;
    var div = document.getElementById("mod_collection_" + collection_number);
    var target = div.nextSibling;
    if (target != null){
      target = target.nextSibling;
      if (target != null){
        this.parent.removeChild(div);
        this.parent.insertBefore(div, target);
      }
    }
    this.changed = true;
			 
  }

  function module_delete(collection_number, dont_ask){
    if(dont_ask == null){
      dont_ask = false;
    }
    
    var div = document.getElementById('mod_collection_' + collection_number);
    //      if (confirm(translation_edit_menu_paragraph_delete_confirm)){
    if (dont_ask || confirm(translation_edit_menu_man_paragraph_question_delete)){
      this.modules[collection_number].deleted = 1;
      div.style.display = 'none';
    }
    this.changed = true;
			
  }



  function module_change_visibility(collection_number){
    var div = document.getElementById('mod_collection_' + collection_number);

    if(this.modules[collection_number].visible)
      this.modules[collection_number].visible = 0;
    else
      this.modules[collection_number].visible = 1;

    div.innerHTML = '';
    this.modules[collection_number].preview('worker_form', this.this_object + '.universal_preview_response', collection_number);

    this.changed = true;


  }


  function module_preview_save_response(collection_number){
    document.getElementById('loading').style.display = 'none';
    this.modules[collection_number].managed = false;
    this.modules[collection_number].preview('worker_form', this.this_object + '.universal_preview_response', collection_number);
  //worker_frame will trow universal_preview_response;
  }

  function module_preview_save_response_error(collection_number){
    document.getElementById('loading').style.display = 'none';
    if(menu_saver.saving) //auto cofirm hack
      menu_saver.save_to_db();
  }


  function module_preview_save(collection_number, forced){
    document.getElementById('loading').style.display = 'inline';
    this.changed = false; //ugly ie bug fires onbeforeunload if iframe reloads
    this.modules[collection_number].save(forced);
  }


		
  function module_preview_cancel(collection_number){
    this.changed = false; //ugly ie bug fires onbeforeunload if iframe reloads
    this.modules[collection_number].managed = false;
    this.modules[collection_number].preview('worker_form', this.this_object + '.universal_preview_response', collection_number);
  //worker_frame will trow universal_preview_response;
  }
     
  function universal_preview_response(notes, errors, variables){
    var html = variables[0];
    var collection_number = variables[1];
    var preview_script = variables[2]
    var div = document.getElementById('mod_collection_' + collection_number);
    this.modules[collection_number].close();
    div.setAttribute('id', 'delete');
    div.innerHTML = '';
    var content = document.createElement("div");
    //      content.className = 'menuMod';
    content.innerHTML = html;
    var table = this.make_module_div_table(collection_number, content, true);
    var div_parent = div.parentNode;
    div_parent.insertBefore(table, div);
    div_parent.removeChild(div);
      
    if(this.modules[collection_number].empty()){
      this.module_delete(collection_number, true);
    }
				
			
    this.window_resize_recalc();

    if(menu_saver.saving){ //auto cofirm hack
      //alert('gerai');
      //setTimeout(menu_saver.save_to_db, '5000');
      this.changed = false;
      menu_saver.save_to_db();
    } else {
      this.changed = true;
    }
  }
     
  function hide_chose_new_module(){
    for(var i=0; i<this.modules.length; i++){
      var element = document.getElementById('select_new_page_type_' + this.modules[i].collection_number);
      if (element)
        element.style.display = "none";
    }
  }

  function show_insert_module(evt){//shows place where new module will be inserted on event e
    var e = (window.event) ? window.event : evt;

    var posY = LibMouse.getMouseY(e);
    var posX = LibMouse.getMouseX(e);
    if(document.getElementById('new_paragraph_' + mod_management.module_dimensions.last_active))
      document.getElementById('new_paragraph_' + mod_management.module_dimensions.last_active).style.display='none';
    var active_module = mod_management.module_dimensions.active_module_number(posX, posY);
    if(document.getElementById('new_paragraph_' + (active_module)))
      document.getElementById('new_paragraph_' + (active_module)).style.display='block';
    document.getElementById('modules').new_paragraph_before = active_module;
  }
		
  function make_module_div_table(collection_number, obj3, controls){
    var main_div = document.createElement("div");
    main_div.setAttribute('id', 'mod_collection_' + collection_number);
    //      main_div.setAttribute(document.all ? "className" : "class", 'contentMod');
    //      main_div.setAttribute("className", 'ipWidget');
    //      main_div.setAttribute("class", 'ipWidget'); //maskas
			
    /*main_div.onclick = function(){
				if(!mod_management.modules[this.id.substring(15)].managed)
					mod_management.module_manage(this.id.substring(15));
			}*/
			
			
    main_div.onmouseout = function(e){
      if (!document.getElementById('mod_collection_nav_' + collection_number)) { //border already hidden
        return;
      };
	    
      document.getElementById('modules').new_paragraph_before = '';
      if (!e) var e = window.event;
      var tg = document.getElementById('mod_collection_' + collection_number);
      var reltg = (e.relatedTarget) ? e.relatedTarget : e.toElement;
      try { //firefox trow Permission denied to access property 'nodeName' from a non-chrome context ir reltg is file input.
        while (reltg && reltg != tg && reltg.nodeName && reltg.nodeName != 'BODY'){
          reltg= reltg.parentNode;
        }
      } catch(e) {
      //probably everthing is fine
      }
      if (reltg== tg)
        return;
				
      if(document.getElementById('mod_collection_nav_' + collection_number))
        document.getElementById('mod_collection_nav_' + collection_number).style.display='none';
    //document.getElementById('new_paragraph_' + collection_number).style.display='none';
    };

    main_div.onmouseover = function(){
      if(mod_management.state == "wait"){
        if(document.getElementById('mod_collection_nav_' + collection_number))
          document.getElementById('mod_collection_nav_' + collection_number).style.display='block';
      }else{
    /*	document.getElementById('new_paragraph_' + collection_number).style.display='block';
					if(collection_number !== '')
						document.getElementById('modules').new_paragraph_before = collection_number;
					else
						document.getElementById('modules').new_paragraph_before = 'end';*/
    }
    };
		
			
    var mod_nav = document.createElement('div');

    if(controls === true){
      if(collection_number)
        mod_nav.appendChild(this.module_control_buttons(collection_number, this.modules[collection_number].visible));
      else
        mod_nav.appendChild(this.module_control_buttons(collection_number, true));
    }else{
    }
    mod_nav.setAttribute('id', 'mod_collection_nav_' + collection_number);
    //      mod_nav.setAttribute(document.all ? "className" : "class", "modNav");
    //      mod_nav.setAttribute("className", "ipCmsModNav");
    //      mod_nav.setAttribute("class", "ipCmsModNav");
    mod_nav.className = "ipCmsModNav";



    var new_paragraph = document.createElement("div");
    //      new_paragraph.setAttribute(document.all ? "className" : "class", "newParagraph");
    //      new_paragraph.setAttribute("className", "ipCmsNewParagraph");
    //      new_paragraph.setAttribute("class", "ipCmsNewParagraph");
    new_paragraph.className = "ipCmsNewParagraph";
    new_paragraph.setAttribute('id', 'new_paragraph_' + collection_number);

    var separator = document.createElement("div");
    //      separator.setAttribute(document.all ? "className" : "class", 'paragraphSeparator');
    //      separator.setAttribute("className", 'ipCmsParagraphSeparator');
    //      separator.setAttribute("class", 'ipCmsParagraphSeparator');
    separator.className = 'ipCmsParagraphSeparator';
    separator.style.width = '100%';
    separator.style.backgroundColor = 'darkred';
    if(this.mode = 'no_separators')
      separator.style.display = 'none';
    separator.style.height = '1px';
    separator.zIndex = 6;

			
    var manage_div = document.createElement('div');
			
    if(controls === true)
      manage_div.appendChild(mod_nav);
    manage_div.appendChild(separator);
    manage_div.appendChild(new_paragraph);
    //      manage_div.setAttribute(document.all ? "className" : "class", 'moduleManagement');
    //      manage_div.setAttribute("className", 'ipCmsModuleManagement');
    //		manage_div.setAttribute("class", 'ipCmsModuleManagement');
      
    manage_div.className = 'ipCmsModuleManagement';
      
    /*      var clearElement = document.createElement('div');
      clearElement.className = 'clear';
      manage_div.appendChild(clearElement);
*/      
      
      
    main_div.appendChild(manage_div);
    main_div.appendChild(obj3);
			
			
    return main_div;
  }


  function get_modules(){
    return this.modules;
  }

  function get_modules_array_name(){
    return "modules";
  }

  function window_resize_recalc(){
    //get window width height
    var myWidth = 0, myHeight = 0;
    if( typeof( window.innerWidth ) == 'number' ) {
      //Non-IE
      myWidth = window.innerWidth;
      myHeight = window.innerHeight;
    } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
      //IE 6+ in 'standards compliant mode'
      myWidth = document.documentElement.clientWidth;
      myHeight = document.documentElement.clientHeight;
    }
    //eof get window width height


    var i = 0;
    while(document.getElementById('mod_collection_nav_' + i)){
      var nav_div = document.getElementById('mod_collection_nav_' + i);
      var mod_div = document.getElementById('mod_collection_' + i);
      if(LibPositioning.getX(mod_div) + mod_div.offsetWidth + 5 > LibWindow.getScrollWidth())
        nav_div.style.width = (LibWindow.getScrollWidth() - LibPositioning.getX(mod_div) - 5) + 'px';
      else
        nav_div.style.width = (mod_div.offsetWidth - 2) + 'px';
      if(mod_div.offsetHeight < 2)
        nav_div.style.height = '0px';
      else
        nav_div.style.height = (mod_div.offsetHeight - 2) + 'px';
				
      i++;
				
    }
  }
		
		
  function calc_module_dimensions(){
    this.module_dimensions = new EditMenuManagementModuleDimensions(LibPositioning.getX(document.getElementById('modules')), document.getElementById('modules').offsetWidth);
    var i=0;
    while(document.getElementById('mod_collection_' + i)){
      var tmp_mod = document.getElementById('mod_collection_' + i);
      this.module_dimensions.add_dimension(i, LibPositioning.getY(tmp_mod), tmp_mod.offsetHeight);
      i++;
    }
  }
		
}

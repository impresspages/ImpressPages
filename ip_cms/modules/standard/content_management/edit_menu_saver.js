/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

  function edit_menu_saver(){
    var menu_element;
    var menu_element_title;  
    var my_name;
    var modules;
    var progres;
    var parent;
	  var module_id;
	  var saving;
    this.init = init;
    this.get_answer = get_answer;  
    this.save_to_db = save_to_db;  
    this.proceed_save_to_db = proceed_save_to_db; 
    this.worker_frame_loaded = worker_frame_loaded; 
	

    function init(parent, menu_element, modules, menu_element_title, my_name, worker, module_id){
      this.parent = parent;
      this.menu_element = menu_element;
      this.menu_element_title = menu_element_title;
      this.my_name = my_name;
	    this.module_id = module_id;
	    this.saving = false;
      this.modules = modules; //this is a pointer to modules, that are managed by menu_management.js
      var answer = '<div style="display: none;" id="worker"><form action="' + worker + '?module_id=' + this.module_id + '&security_token=' + global_config_security_token + '" method="post" id="worker_form" target="worker_frame"></form></div>';
      answer = answer + '<iframe scrolling="no" style="width: 0px; height: 0px; border: none;" onload="' + this.my_name + '.worker_frame_loaded()" name="worker_frame" width="0" height="0"></iframe>';
      this.progress = 0;
      return answer;
    } 


    function save_to_db(){
      this.saving = true;
			document.getElementById('loading').style.display = 'inline';
			var tmp = document.getElementById('menuTop');
			//tmp.innerHTML = '';
      if (true || this.progress == 0){
      
        var paragraphs = [];        
        var fields = [];
        
        {
          //auto confirm
          for(var i=0; i<this.modules.length; i++){       //append existing modules
            if(this.modules[i].managed){
              this.modules[i].managed = false;
              mod_management.module_preview_save(i, 1);
              //alert('save');
              return;
            }
          }             
          //mod_management.confirm_all();
        }
        
        for(var module_key=0; module_key<this.modules.length; module_key++){
           for(var i=0; i<this.parent.childNodes.length; i++){
             if (this.parent.childNodes[i].getAttribute('id') == 'mod_collection_' + module_key){
               row_number = i;
             }
           }        
           //this.modules[module_key].save(true);
           paragraphs.push(this.modules[module_key].store_to_db_fields(row_number, this.menu_element));
        }
         
        
        var form = document.getElementById('worker_form');
        
        for (var paragraph_key=0; paragraph_key<paragraphs.length;paragraph_key++)
        {
          var paragraph = paragraphs[paragraph_key];
          for(var field_key=0; field_key < paragraph.length;  field_key++){
            var field = paragraph[field_key];
            var input = document.createElement('textarea');
            input.value = field[1];
            input.setAttribute("name", 'paragraphs[' + paragraph_key + '][' + field[0] + ']');
            form.appendChild(input); 
            //fields = fields + '<textarea name="paragraphs[' + paragraph_key + '][' + field[0] + ']">' + field[1] + '</textarea>';
          }
        }
        
        if(undefined !== window.mod_content_management_parameters){ //isset hack
          for(var i=0; i<mod_content_management_parameters.length; i++){
            if(mod_content_management_parameters[i][3]){ //if changed
              var input = document.createElement('input');
              input.value = mod_content_management_parameters[i][0];
              input.setAttribute("name", 'f_main_parameter[]');
              form.appendChild(input); 
              var input = document.createElement('input');
              input.value = mod_content_management_parameters[i][1];
              input.setAttribute("name", 'f_main_parameter_value[]');
              form.appendChild(input); 
              var input = document.createElement('input');
              input.value = mod_content_management_parameters[i][2];
              input.setAttribute("name", 'f_main_parameter_language[]');
              form.appendChild(input); 
              //fields = fields + '<textarea name="f_main_parameter[]">' + mod_content_management_parameters[i][0] + '</textarea>';
              //fields = fields + '<textarea name="f_main_parameter_value[]">' + mod_content_management_parameters[i][1] + '</textarea>';
              //fields = fields + '<textarea name="f_main_parameter_language[]">' + mod_content_management_parameters[i][2] + '</textarea>';
            }
          }
        }
        
        var input = document.createElement('input');
        input.value = this.menu_element;
        input.setAttribute("name", 'id');
        form.appendChild(input); 
        var input = document.createElement('input');
        input.value = "save_page";
        input.setAttribute("name", 'action');
        form.appendChild(input); 
        var input = document.createElement('input');
        input.value = document.getElementById('f_main_fields').page_page_title.value;
        input.setAttribute("name", 'page_page_title');
        form.appendChild(input); 
        var input = document.createElement('input');
        input.value = document.getElementById('f_main_fields').page_button_title.value;
        input.setAttribute("name", 'page_button_title');
        form.appendChild(input); 
        var input = document.createElement('input');
        input.value = document.getElementById('f_main_fields').keywords.value;
        input.setAttribute("name", 'keywords');
        form.appendChild(input); 
        var input = document.createElement('input');
        input.value = document.getElementById('f_main_fields').description.value;
        input.setAttribute("name", 'description');
        form.appendChild(input); 
        var input = document.createElement('input');
        input.value = document.getElementById('f_main_fields').url.value;
        input.setAttribute("name", 'url');
        form.appendChild(input); 
        var input = document.createElement('input');
        input.value = document.getElementById('f_main_fields').rss.value;
        input.setAttribute("name", 'rss');
        form.appendChild(input); 
        var input = document.createElement('input');
        input.value = document.getElementById('f_main_fields').visible.value;
        input.setAttribute("name", 'visible');
        form.appendChild(input); 
        var input = document.createElement('input');
        input.value = document.getElementById('f_main_fields').created_on.value;
        input.setAttribute("name", 'created_on');
        form.appendChild(input); 
        var input = document.createElement('input');
        input.value = document.getElementById('f_main_fields').redirect_url.value;
        input.setAttribute("name", 'redirect_url');
        form.appendChild(input); 
        var input = document.createElement('input');
        input.value = document.getElementById('f_main_fields').type.value;
        input.setAttribute("name", 'type');
        form.appendChild(input); 
        var input = document.createElement('input');
        input.value = this.my_name + '.get_answer';
        input.setAttribute("name", 'answer_function');
        form.appendChild(input); 
        
       /* fields = fields + 
        '<input type="hidden" name="id" value="' + this.menu_element + '" />'+
        '<input type="hidden" name="action" value="save_page" />'+
        '<input type="hidden" name="page_page_title" value="' + document.getElementById('f_main_fields').page_page_title.value + '" />'+
        '<input type="hidden" name="page_button_title" value="' + document.getElementById('f_main_fields').page_button_title.value+ '" />'+
        '<input type="hidden" name="keywords" value="' + document.getElementById('f_main_fields').keywords.value + '" />'+
        '<input type="hidden" name="description" value="' + document.getElementById('f_main_fields').description.value + '" />'+
        '<input type="hidden" name="url" value="' + document.getElementById('f_main_fields').url.value + '" />'+
        '<input type="hidden" name="rss" value="' + document.getElementById('f_main_fields').rss.value + '" />'+
        '<input type="hidden" name="answer_function" value="' + this.my_name + '.get_answer" />';
      */
        
        //form.innerHTML = form.innerHTML + fields;
        form.submit();
        //document.getElementById('worker_form').submit();                
                
      }

    }


    function proceed_save_to_db(){
      /*
      var row_number = 0;

      if (this.progress < this.modules.length){
         this.progress++; //moved up to avoid some concurrent programing problems           
         
         for(var i=0; i<this.parent.childNodes.length; i++){
           if (this.parent.childNodes[i].getAttribute('id') == 'mod_collection_' + (this.progress-1)){
             row_number = i;
           }
         }
         this.modules[this.progress-1].store_to_db('worker_form', 'worker_frame', this.my_name + '.get_answer', row_number, this.menu_element);
         //this.modules[this.progress-1].store_to_db('worker_form', 'worker_frame', this.my_name + '.get_answer', this.progress, this.menu_element);

      }else{
        if (this.progress == this.modules.length){
           this.progress++;
           document.getElementById('worker_form').innerHTML = ''+        
           '<input name="module_id" value="' +  this.module_id + '" />'+
           '<input name="action" value="make_html" />'+
           '<input name="answer_function" value="' + this.my_name + '.get_answer" />'+
           '<input name="id" value="' + this.menu_element + '" />';       
            document.getElementById('worker_form').submit();
        } 
        else{
          this.progress = 0;
          //window.location.reload( true ); reloads forever in some computers. Unknown reason
          //window.location.href=window.location.href;
          LibDefault.ajaxMessage(window.location.href, "module_group=standard&module_name=content_management&action=reload&id=" + this.menu_element); //to avoid broken link on url changes
        }
      } */
    }

    function get_answer(notes, errors, variables){
      for(var i=0; i<errors.length; i++)
        alert(errors[i])  
      for(var i=0; i<notes.length; i++)
        alert(notes[i])
      LibDefault.ajaxMessage(window.location.href, "module_group=standard&module_name=content_management&action=reload&id=" + this.menu_element); //to avoid broken link on url changes          
      /*if (this.progress > 0 && this.progress <= this.modules.length){ //all other steps
            if (this.modules[this.progress-1].get_answer(errors) == false);
               this.proceed_save_to_db();
         
      }else{
       // if (this.progress <= this.modules.length + 1) //first or last save step
          this.proceed_save_to_db();
      }*/
    }

    function worker_frame_loaded(){
       var iFrameDocObj = window.frames['worker_frame'].window.document;
       // alert(iFrameDocObj.body.innerHTML);
       //eval(iFrameDocObj.body.innerHTML);
       variables = window.frames['worker_frame'].variables;
       errors = window.frames['worker_frame'].errors;
       notes = window.frames['worker_frame'].notes;
       if(window.frames['worker_frame'].script)
        eval(window.frames['worker_frame'].script);
    }
  }

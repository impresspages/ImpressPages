/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license		GNU/GPL, see ip_license.html
 */
  function logo_gallery_photo(){
    this.title = '';
    this.new_photo = '';
    this.link_to_existing_photo = '';
    this.photo_id = '';

    this.set_title = set_title;
    this.set_existing_photo = set_existing_photo;
    this.init = init;
    this.set_new_photo = set_new_photo; 
    this.draw_management = draw_management;
    this.save_title = save_title;
    this.draw_preview = draw_preview;
    this.set_photo_id = set_photo_id;


    function init(){
       this.deleted = false;
       this.photo_id = '';
        this.title = '';
        this.new_photo = '';
        this.link_to_existing_photo = '';
    }

    function save_title(prefix){
       this.title = document.getElementById(prefix + '_title').value;
    }

    function set_photo_id(photo_id){
       this.photo_id = photo_id;
    }


    function draw_management(prefix, move_left_script, move_right_script, delete_script){

      var current_photo_img;
      if (this.new_photo != ''){ 
         current_photo_img = '<img class="ipCmsPhotoSmall" src="' + global_config_tmp_image_url + this.new_photo + '" />';
      }else{
         if(this.link_to_existing_photo != '')
            current_photo_img = '<img class="ipCmsPhotoSmall" src="' + global_config_image_url + this.link_to_existing_photo + '" />';
         else
            current_photo_img = '';
      }

       var answer = '' + 
			 '<label class="ipCmsTitle">' + widget_logo_gallery_link + '</label>'+
       '<div class="ipCmsInput"><input type="text" id="' + prefix + '_title" value="' + this.title.replace(/"/g, "&quot;") + '" /></div>' +
				 '<div class="ipCmsNav">' +
		       '<img border="0" onclick="' + move_left_script + '" src="' + global_config_modules_url + 'standard/content_management/design/icon_left.gif" />'+
		       '<img border="0" onclick="' + move_right_script + '" src="' + global_config_modules_url + 'standard/content_management/design/icon_right.gif" />'+
		       '<img border="0" onclick="if(confirm(\'' + widget_logo_gallery_delete_confirm + '\')) ' + delete_script + '" src="' + global_config_modules_url + 'standard/content_management/design/icon_delete.gif" />'+
	       '</div>'+
       current_photo_img +
			 '';
      return answer;
    }


    function draw_preview(){

      var current_photo_img;
      if (this.new_photo != ''){ 
         current_photo_img = '<img border="0" src="' + global_config_tmp_image_url + this.new_photo + '" />';
      }else{
         if(this.link_to_existing_photo != '')
            current_photo_img = '<img border="0" src="' + global_config_image_url + this.link_to_existing_photo + '"  />';
         else
            current_photo_img = '';
      }
       var answer = current_photo_img;
   
      return answer;
    }

    function set_title(title){
      this.title = title;
    }

    function set_existing_photo(photo){
      this.link_to_existing_photo = photo;
    }


    function set_new_photo(photo){
      this.new_photo = photo;
    }
  }

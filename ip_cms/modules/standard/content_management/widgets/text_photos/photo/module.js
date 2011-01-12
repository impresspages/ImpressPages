/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */
function content_mod_photo() {
	this.title = '';
	this.new_photo = '';
	this.link_to_existing_photo = '';
	this.new_bigphoto = '';
	this.link_to_existing_bigphoto = '';
	this.my_name;
	this.menu_management = '';

	this.preview = preview;
	this.manage = manage;
	this.save = save;
	this.init = init;
	this.store_to_db_fields = store_to_db_fields;
	this.get_answer = get_answer;
	this.set_title = set_title;
	this.close = close;
	this.manage_init = manage_init;
	this.response_after_photo_save = response_after_photo_save;
	this.set_existing_photo = set_existing_photo;
	this.set_existing_bigphoto = set_existing_bigphoto;
	this.empty = empty;

	var collection_number;
	var id;
	var visible;
	var deleted;
	var layout;

	function init(collection_number, id, visible, my_name, menu_management) {
		this.my_name = my_name;
		this.collection_number = collection_number;
		this.id = id;
		this.visible = visible;
		this.deleted = 0;
		this.menu_management = menu_management;
	}

	function empty() {
		if (this.new_photo == '' && this.link_to_existing_photo == ''
				&& this.title == '')
			return true;
		else
			return false;
	}

	function preview(worker_form, return_script, collection_number) {
		var photo;

		if (this.new_photo != '') {
			photo = global_config_tmp_image_url + this.new_photo;
		} else {
			if (this.link_to_existing_photo != '')
				photo = global_config_image_url + this.link_to_existing_photo;
			else
				photo = '';
		}

		document.getElementById(worker_form).innerHTML = ''
				+ '<input name="action" value="make_preview" />'
				+ '<input name="collection_number" value="' + collection_number
				+ '" />' + '<input name="module_key" value="photo" />'
				+ '<input name="group_key" value="text_photos" />'
				+ '<input name="layout" value="' + this.layout + '" />'
				+ '<input name="title" value="' + this.title + '" />'
				+ '<input name="photo" value="' + photo + '" />'
				+ '<input name="answer_function" value="' + return_script
				+ '" />';
		document.getElementById(worker_form).submit();

	}
	;

	function manage() {
		var div = document.createElement('div');

		var current_photo;
		var current_photo_img;
		if (this.new_photo != '')
			current_photo_img = '<div class="ipCmsManagementPhoto"><img src="'
					+ global_config_tmp_image_url + this.new_photo
					+ '" /></div>';
		else {
			if (this.link_to_existing_photo != '')
				current_photo_img = '<div class="ipCmsManagementPhoto"><img src="'
						+ global_config_image_url
						+ this.link_to_existing_photo
						+ '" /></div>';
			else
				current_photo_img = '';
		}

		if (this.new_photo != '')
			current_photo = this.new_photo;
		else {
			if (this.link_to_existing_photo != '')
				current_photo = this.link_to_existing_photo;
			else
				current_photo = '';
		}

		div.innerHTML = ''
				+ '<div id="management_'
				+ this.collection_number
				+ '_error" class="ipCmsError"></div>'
				+ '<div class="ipCmsManagement">'
				+ '<label class="ipCmsModuleName">'
				+ widget_photo_photo
				+ '</label>'
				+ '<form id="mod_' + this.collection_number	+ '_layout" action="">'	+ mod_photo_layout + '</form>'
				+ '<div class="ipCmsModuleSeparator"></div>'
				+ '<form id="mod_'
				+ this.collection_number
				+ '_form" action="'
				+ global_config_base_url
				+ global_config_backend_worker_file
				+ '?module_id='
				+ this.menu_management.module_id + '&security_token=' + global_config_security_token
				+ '" target="mod_photo_iframe" method="post" enctype="multipart/form-data">'
				+ '<div>'
				+ '<label class="ipCmsTitle">'
				+ widget_photo_new_photo
				+ '</label>'
				+ '<div class="ipCmsInputFile"><input type="file" name="new_photo" id="management_'
				+ this.collection_number
				+ '_new_photo"  /></div>'
				+ '<div class="ipCmsClear"></div>'
				+ '<label class="ipCmsTitle">'
				+ widget_photo_title
				+ '</label> '
				+ '<div class="ipCmsInput"><input id="management_'
				+ this.collection_number
				+ '_title" value="'
				+ this.title.replace(/"/g, "&quot;")
				+ '"></div>'
				+ '<input type="hidden" name="action" value="upload_tmp_image"/>'
				+ '<input type="hidden" name="photo_width[]" value="'
				+ widget_photo_width + '"/>'
				+ '<input type="hidden" name="photo_height[]" value="'
				+ widget_photo_height + '"/>'
				+ '<input type="hidden" name="photo_quality[]" value="'
				+ widget_photo_quality + '"/>'
				+ '<input type="hidden" name="photo_method[]" value="fit"/>'
				+ '<input type="hidden" name="photo_forced[]" value="0"/>'
				+ '<input type="hidden" name="photo_width[]" value="'
				+ widget_photo_big_width + '"/>'
				+ '<input type="hidden" name="photo_height[]" value="'
				+ widget_photo_big_height + '"/>'
				+ '<input type="hidden" name="photo_quality[]" value="'
				+ widget_photo_big_quality + '"/>'
				+ '<input type="hidden" name="photo_method[]" value="fit"/>'
				+ '<input type="hidden" name="photo_forced[]" value="0"/>'
				+ '</div>' + '</form>' + '</div>' + current_photo_img + '';
		return div;
	}
	;

	function manage_init() {
		var LayoutSelect = document.getElementById('mod_' + this.collection_number + '_layout').layout;
		for (index = 0; index < LayoutSelect.length; index++) {
			if (LayoutSelect[index].value == this.layout)
				LayoutSelect.selectedIndex = index;
		}
	}

	function save(forced) {
		this.layout = document.getElementById('mod_' + this.collection_number + '_layout').layout.value;
		
		
		this.title = document.getElementById('management_' + this.collection_number + '_title').value;
		if (document.getElementById('management_' + this.collection_number + '_new_photo').value != '') {
			document.getElementById('mod_photo_action_after_photo_save').value = this.my_name
					+ '.response_after_photo_save(' + forced + ')';
			var form = document.getElementById('mod_' + this.collection_number + '_form');
			form.submit();
		} else
			this.menu_management.module_preview_save_response(this.collection_number);
	}

	function response_after_photo_save(forced) {
		var iFrameDocObj = window.frames['mod_photo_iframe'].window.document;
		
		if (iFrameDocObj.body.innerHTML != '') {
			var name0 = '';
			var name1 = '';

			errors = window.frames['mod_photo_iframe'].errors;
			if (errors.length == 0 || errors[0] == 4 || forced) {
				if(errors.length == 0) {
					this.new_photo = window.frames['mod_photo_iframe'].variables[0];
					this.new_bigphoto = window.frames['mod_photo_iframe'].variables[1];
				}
				

				this.menu_management.module_preview_save_response(this.collection_number);					


			} else {
				var answer = '';

				switch (errors[0]) {
				case '1':
				case '2':
					answer = widget_photo_too_big;
					break;
				case '3':
					answer = widget_photo_partial;
					break;
				case '4':
					answer = widget_photo_no_file;
					break;
				case '6':
				case '7':
					answer = widget_photo_unknown;
					break;
				case '8':
					answer = widget_photo_bad_type;
					break;
				}

				document
						.getElementById('management_' + this.collection_number + '_error').innerHTML = answer;
				document
						.getElementById('management_' + this.collection_number + '_error').style.display = 'block';
				this.menu_management
						.module_preview_save_response_error(this.collection_number);
			}
		}
	}

	function get_answer(notes) { // answer after save_to_db
		/*
		 * for(var i=0; i<notes.length; i++) alert(notes[i]);
		 */

		return false;
	}

	function close() {

	}

	function store_to_db_fields(row_number, menu_element_id) {
		if (this.id == null) {
			if (this.deleted == 0) {
				var fields = [];
				fields.push( [ 'action', 'new_module' ]);
				fields.push( [ 'group_key', 'text_photos' ]);
				fields.push( [ 'module_key', 'photo' ]);
				fields.push( [ 'layout', this.layout ]);
				fields.push( [
						'title',
						this.title.replace(/</g, "&lt;").replace(/>/g, "&gt;")
								.replace(/"/g, "&quot;") ]);
				fields.push( [ 'new_photo', this.new_photo ]);
				fields.push( [ 'new_bigphoto', this.new_bigphoto ]);
				fields.push( [ 'existing_photo', this.link_to_existing_photo ]);
				fields.push( [ 'existing_bigphoto',
						this.link_to_existing_bigphoto ]);
				fields.push( [ 'content_element_id', menu_element_id ]);
				fields.push( [ 'row_number', row_number ]);
				fields.push( [ 'visible', this.visible ]);
				return fields;
			} else {
				return [];
			}
		} else {
			if (this.deleted == 0) {
				var fields = [];
				fields.push( [ 'action', 'update_module' ]);
				fields.push( [ 'group_key', 'text_photos' ]);
				fields.push( [ 'module_key', 'photo' ]);
				fields.push( [ 'layout', this.layout ]);
				fields.push( [
						'title',
						this.title.replace(/</g, "&lt;").replace(/>/g, "&gt;")
								.replace(/"/g, "&quot;") ]);
				fields.push( [ 'new_photo', this.new_photo ]);
				fields.push( [ 'new_bigphoto', this.new_bigphoto ]);
				fields.push( [ 'existing_photo', this.link_to_existing_photo ]);
				fields.push( [ 'existing_bigphoto',
						this.link_to_existing_bigphoto ]);
				fields.push( [ 'id', this.id ]);
				fields.push( [ 'row_number', row_number ]);
				fields.push( [ 'visible', this.visible ]);
				return fields;
			} else {
				var fields = [];
				fields.push( [ 'action', 'delete_module' ]);
				fields.push( [ 'group_key', 'text_photos' ]);
				fields.push( [ 'module_key', 'photo' ]);
				fields.push( [ 'layout', this.layout ]);
				fields.push( [ 'existing_photo', this.link_to_existing_photo ]);
				fields.push( [ 'existing_bigphoto',
						this.link_to_existing_bigphoto ]);
				fields.push( [ 'id', this.id ]);
				return fields;
			}
		}
		document.getElementById(worker_form).submit();
	}

	function set_title(title) {
		this.title = title;
	}

	function set_photo_id(id) {
		this.id = id;
	}

	function set_existing_photo(photo) {
		this.link_to_existing_photo = photo;
	}

	function set_existing_bigphoto(bigphoto) {
		this.link_to_existing_bigphoto = bigphoto;
	}
}

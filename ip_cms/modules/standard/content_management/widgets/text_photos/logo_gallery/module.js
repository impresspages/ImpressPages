/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 JSC Apro media.
 * @license GNU/GPL, see ip_license.html
 */
function content_mod_logo_gallery() {

	this.my_name;
	this.menu_management = '';

	this.preview = preview;
	this.manage = manage;
	this.save = save;
	this.init = init;
	this.set_photos = set_photos;
	this.store_to_db_fields = store_to_db_fields;
	this.get_answer = get_answer;
	this.close = close;
	this.manage_init = manage_init;
	this.response_after_photo_save = response_after_photo_save;
	this.redraw_gallery_management = redraw_gallery_management;
	this.upload_new_photo = upload_new_photo;
	this.empty = empty;

	this.photo_move_left = photo_move_left;
	this.photo_move_right = photo_move_right;
	this.photo_delete = photo_delete;

	this.gallery_management = gallery_management;

	var collection_number;
	var id;
	var visible;
	var photos;
	var new_photo;
	var deleted;
	var photos_deleted;
	var backup_photos;
	var backup_photos_deleted;

	var count_in_row;
	var layout;

	function init(collection_number, id, visible, my_name, menu_management) {
		this.my_name = my_name;
		this.collection_number = collection_number;
		this.id = id;
		this.deleted = 0;
		this.photos = new Array();
		this.photos_deleted = new Array();
		this.menu_management = menu_management;
		this.count_in_row = 3;
		this.visible = visible;
	}

	function empty() {
		if (this.photos.length == 0)
			return true;
		else
			return false;
	}

	function set_photos(photos) {
		this.photos = photos.slice();
	}

	function preview(worker_form, return_script, collection_number) {
		document.getElementById(worker_form).innerHTML = ''
				+ '<input name="action" value="make_preview" />'
				+ '<input name="collection_number" value="' + collection_number + '" />'
				+ '<input name="layout" value="' + this.layout + '" />'
				+ '" />' + '<input name="module_key" value="logo_gallery" />'
				+ '<input name="group_key" value="text_photos" />'
				+ '<input name="answer_function" value="' + return_script
				+ '" />';

		for ( var i = 0; i < this.photos.length; i++) {
			var photo;
			if (this.photos[i].new_photo != '') {
				photo = global_config_tmp_image_url + this.photos[i].new_photo;
			} else {
				if (this.photos[i].link_to_existing_photo != '') {
					photo = global_config_image_url
							+ this.photos[i].link_to_existing_photo;
				} else {
					photo = '';
				}
			}

			document.getElementById(worker_form).innerHTML = document
					.getElementById(worker_form).innerHTML
					+ '<input name="photo_number[]" value="'
					+ i
					+ '" />'
					+ '<input name="title'
					+ i
					+ '" value="'
					+ this.photos[i].title.replace(/</g, "&lt;").replace(/>/g,
							"&gt;").replace(/"/g, "&quot;")
					+ '" />'
					+ '<input name="photo' + i + '" value="' + photo + '" />';
		}

		document.getElementById(worker_form).submit();

	}
	;

	function manage() {
		var div = document.createElement('div');

		this.backup_photos = this.photos.slice();
		this.backup_photos_deleted = this.photos_deleted.slice();

		div.setAttribute('name', 'management');
		div.setAttribute('style', 'margin: 0px; padding: 0px;');

		div.innerHTML = ''
				+ '<div id="management_'
				+ this.collection_number
				+ '_error" class="ipCmsError"></div>'
				+ '<div class="ipCmsManagement2">'
				+ '<label class="ipCmsModuleName">'
				+ widget_logo_gallery_logo_gallery
				+ '</label>'
				+ '<form id="mod_' + this.collection_number	+ '_layout" action="">'	+ mod_logo_gallery_layout + '</form>'
				+ '<div class="ipCmsModuleSeparator"></div>'
				+ '<form id="mod_'
				+ this.collection_number
				+ '_form" action="'
				+ global_config_base_url
				+ global_config_backend_worker_file
				+ '?module_id='
				+ this.menu_management.module_id+ '&security_token=' + global_config_security_token
				+ '" target="mod_logo_gallery_iframe" method="post" enctype="multipart/form-data">'
				+ '<label class="ipCmsTitle">'
				+ widget_logo_gallery_new_logo
				+ '</label>'
				+ '<input type="hidden" id="management_'
				+ this.collection_number
				+ '_title" value="">'
				+ '<div class="ipCmsInputFile"><input type="file" name="new_photo" id="management_'
				+ this.collection_number
				+ '_new_photo"  /></div>'
				+ '<input type="hidden" id="management_'
				+ this.collection_number
				+ '_title" value="">'
				+ '<input type="hidden" name="action" value="upload_tmp_image"/>'
				+ '<input type="hidden" name="photo_width[]" value="'
				+ widget_logo_gallery_width
				+ '"/>'
				+ '<input type="hidden" name="photo_height[]" value="'
				+ widget_logo_gallery_height
				+ '"/>'
				+ '<input type="hidden" name="photo_quality[]" value="100"/>'
				+ '<input type="hidden" name="photo_method[]" value="fit"/>'
				+ '<input type="hidden" name="photo_forced[]" value="false"/>'
				+ '<a class="ipCmsButton" style="margin-left: 5px;" href="javascript:void(0)" onclick="'
				+ this.my_name + '.upload_new_photo()">'
				+ widget_logo_gallery_upload + '</a>' + '</form>'
				+ '<div class="ipCmsClear"></div>' + '</div>'
				+ '<div id="logo_gallery_' + this.collection_number
				+ '_photos">' + this.gallery_management() + '</div>' + '';

		return div;
	}
	;

	function gallery_management() {
		if (this.backup_photos.length <= 0)
			return '';

		var answer = '';
		for ( var i = 0; i < this.backup_photos.length; i++) {
			answer = answer
					+ '<div class="ipCmsGalleryPhoto" style="height: '
					+ (50 + parseInt(widget_logo_gallery_height))
					+ 'px;">'
					+ this.backup_photos[i].draw_management('mod'
							+ this.collection_number + '' + i
							+ '_photo_management', this.my_name
							+ '.photo_move_left(' + i + ')', this.my_name
							+ '.photo_move_right(' + i + ')', this.my_name
							+ '.photo_delete(' + i + ')') + '</div>';
		}

		answer = '<div class="ipCmsSeparator"></div>'
				+ '<div class="ipCmsManagement" id="mod_'
				+ this.collection_number + '_gallery_management" >' + answer
				+ '<div class="ipCmsClear"></div>';
		'</div>' + '</div>' + '<div class="ipCmsClear"></div>';

		return answer;

	}

	function redraw_gallery_management() {
		var div2 = document
				.getElementById('logo_gallery_' + this.collection_number + '_photos');
		if (div2)
			div2.innerHTML = this.gallery_management();

	}

	function photo_move_left(number) {
		if (number > 0) {
			for ( var i = 0; i < this.backup_photos.length; i++) {
				this.backup_photos[i].save_title('mod' + this.collection_number
						+ '' + i + '_photo_management');
			}
			var photo;
			photo = this.backup_photos[number];
			this.backup_photos[number] = this.backup_photos[number - 1];
			this.backup_photos[number - 1] = photo;
			this.redraw_gallery_management();
		}
	}
	function photo_move_right(number) {
		if (number < this.backup_photos.length - 1) {
			for ( var i = 0; i < this.backup_photos.length; i++) {
				this.backup_photos[i].save_title('mod' + this.collection_number
						+ '' + i + '_photo_management');
			}
			var photo;
			photo = this.backup_photos[number];
			this.backup_photos[number] = this.backup_photos[number + 1];
			this.backup_photos[number + 1] = photo;
			this.redraw_gallery_management();
		}
	}
	function photo_delete(number) {
		for ( var i = 0; i < this.backup_photos.length; i++) {
			this.backup_photos[i].save_title('mod' + this.collection_number
					+ '' + i + '_photo_management');
		}
		this.backup_photos_deleted.unshift(this.backup_photos[number]);
		for ( var i = number; i < this.backup_photos.length - 1; i++) {
			this.backup_photos[i] = this.backup_photos[i + 1];
		}
		this.backup_photos.pop();
		this.redraw_gallery_management();
	}

	function manage_init() {
		var LayoutSelect = document.getElementById('mod_' + this.collection_number + '_layout').layout;
		for (index = 0; index < LayoutSelect.length; index++) {
			if (LayoutSelect[index].value == this.layout)
				LayoutSelect.selectedIndex = index;
		}
	}

	function upload_new_photo() {
		document.getElementById('loading').style.display = 'inline';

		for ( var i = 0; i < this.backup_photos.length; i++) {
			this.backup_photos[i].save_title('mod' + this.collection_number
					+ '' + i + '_photo_management');
		}
		
		this.new_photo = new logo_gallery_photo();
		this.new_photo.init();
		this.new_photo.title = document
				.getElementById('management_' + this.collection_number + '_title').value;
		document.getElementById('mod_logo_gallery_action_after_photo_save').value = this.my_name + '.response_after_photo_save()';
		var form = document
				.getElementById('mod_' + this.collection_number + '_form');
		this.menu_management.changed = false; // ugly ie bug fires
												// onbeforeunload if iframe
												// reloads
		form.submit();
	}

	function save() {
		this.layout = document.getElementById('mod_' + this.collection_number + '_layout').layout.value;

		
		for ( var i = 0; i < this.backup_photos.length; i++) {
			this.backup_photos[i].save_title('mod' + this.collection_number
					+ '' + i + '_photo_management');
		}
		this.photos = this.backup_photos;
		this.photos_deleted = this.backup_photos_deleted;
		this.menu_management
				.module_preview_save_response(this.collection_number);
	}

	function response_after_photo_save() {
		var iFrameDocObj = window.frames['mod_logo_gallery_iframe'].window.document;
		document.getElementById('loading').style.display = 'none';
		this.menu_management.changed = true; // ugly ie bug fires
												// onbeforeunload if iframe
												// reloads

		if (iFrameDocObj.body.innerHTML != '') {
			var name0 = '';
			var errors;
			name0 = window.frames['mod_logo_gallery_iframe'].variables[0];
			errors = window.frames['mod_logo_gallery_iframe'].errors;
			if (errors.length == 0) {
				this.new_photo.set_new_photo(name0);
				this.backup_photos.unshift(this.new_photo);
				this.redraw_gallery_management();
				// this.menu_management.module_preview_save_response(this.collection_number);
			} else {
				var answer = '';

				/*
				 * UPLOAD_ERR_OK Value: 0; There is no error, the file uploaded
				 * with success. UPLOAD_ERR_INI_SIZE Value: 1; The uploaded file
				 * exceeds the upload_max_filesize directive in php.ini.
				 * UPLOAD_ERR_FORM_SIZE Value: 2; The uploaded file exceeds the
				 * MAX_FILE_SIZE directive that was specified in the HTML form.
				 * UPLOAD_ERR_PARTIAL Value: 3; The uploaded file was only
				 * partially uploaded. UPLOAD_ERR_NO_FILE Value: 4; No file was
				 * uploaded. UPLOAD_ERR_NO_TMP_DIR Value: 6; Missing a temporary
				 * folder. Introduced in PHP 4.3.10 and PHP 5.0.3.
				 * UPLOAD_ERR_CANT_WRITE Value: 7; Failed to write file to disk.
				 * Introduced in PHP 5.1.0. UPLOAD_ERR_EXTENSION Value: 8; File
				 * upload stopped by extension. Introduced in PHP 5.2.0.
				 */
				switch (errors[0]) {
				case '1':
				case '2':
					answer = widget_logo_gallery_too_big;
					break;
				case '3':
					answer = widget_logo_gallery_partial;
					break;
				case '4':
					answer = widget_logo_gallery_no_file;
					break;
				case '6':
				case '7':
					answer = widget_logo_gallery_unknown;
					break;
				case '8':
					answer = widget_logo_gallery_bad_type;
					break;
				}

				document
						.getElementById('management_' + this.collection_number + '_error').innerHTML = answer;
				document
						.getElementById('management_' + this.collection_number + '_error').style.display = 'block';
			}
		}
	}

	function get_answer(notes) { // answer after save_to_db
		for ( var i = 0; i < notes.length; i++)
			alert(notes[i]);

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
				fields.push( [ 'module_key', 'logo_gallery' ]);
				fields.push( [ 'layout', this.layout ]);
				fields.push( [ 'row_number', row_number ]);
				fields.push( [ 'visible', this.visible ]);
				fields.push( [ 'action', 'new_module' ]);
				fields.push( [ 'content_element_id', menu_element_id ]);

				for ( var i = 0; i < this.photos.length; i++) {
					fields.push( [
							'title' + i,
							this.photos[i].title.replace(/</g, "&lt;").replace(
									/>/g, "&gt;").replace(/"/g, "&quot;") ]);
					fields.push( [ 'photo_id' + i, this.photos[i].photo_id ]);
					fields.push( [ 'new_photo' + i, this.photos[i].new_photo ]);
					fields.push( [ 'new_bigphoto' + i,
							this.photos[i].new_bigphoto ]);
					fields.push( [ 'existing_photo' + i,
							this.photos[i].link_to_existing_photo ]);
					fields.push( [ 'existing_bigphoto' + i,
							this.photos[i].link_to_existing_bigphoto ]);
				}

				return fields;

			} else {
				return [];
			}
		} else {
			if (this.deleted == 0) {
				var fields = [];
				fields.push( [ 'action', 'update_module' ]);
				fields.push( [ 'group_key', 'text_photos' ]);
				fields.push( [ 'module_key', 'logo_gallery' ]);
				fields.push( [ 'layout', this.layout ]);
				fields.push( [ 'id', this.id ]);
				fields.push( [ 'row_number', row_number ]);
				fields.push( [ 'visible', this.visible ]);

				for ( var i = 0; i < this.photos.length; i++) {
					fields.push( [
							'title' + i,
							this.photos[i].title.replace(/</g, "&lt;").replace(
									/>/g, "&gt;").replace(/"/g, "&quot;") ]);
					fields.push( [ 'photo_id' + i, this.photos[i].photo_id ]);
					fields.push( [ 'new_photo' + i, this.photos[i].new_photo ]);
					fields.push( [ 'new_bigphoto' + i,
							this.photos[i].new_bigphoto ]);
					fields.push( [ 'existing_photo' + i,
							this.photos[i].link_to_existing_photo ]);
					fields.push( [ 'existing_bigphoto' + i,
							this.photos[i].link_to_existing_bigphoto ]);
				}
				for ( var i = 0; i < this.photos_deleted.length; i++) {
					fields.push( [
							'title' + i + '_del',
							this.photos_deleted[i].title.replace(/</g, "&lt;")
									.replace(/>/g, "&gt;").replace(/"/g,
											"&quot;") ]);
					fields.push( [ 'photo_id' + i + '_del',
							this.photos_deleted[i].photo_id ]);
					fields.push( [ 'new_photo' + i + '_del',
							this.photos_deleted[i].new_photo ]);
					fields.push( [ 'new_bigphoto' + i + '_del',
							this.photos_deleted[i].new_bigphoto ]);
					fields.push( [ 'existing_photo' + i + '_del',
							this.photos_deleted[i].link_to_existing_photo ]);
					fields.push( [ 'existing_bigphoto' + i + '_del',
							this.photos_deleted[i].link_to_existing_bigphoto ]);
				}

				return fields;

			} else {
				var fields = [];
				fields.push( [ 'action', 'delete_module' ]);
				fields.push( [ 'group_key', 'text_photos' ]);
				fields.push( [ 'module_key', 'logo_gallery' ]);
				fields.push( [ 'layout', this.layout ]);
				fields.push( [ 'id', this.id ]);
				return fields;
			}
		}
		document.getElementById(worker_form).submit();
	}

}

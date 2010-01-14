/**
 * @package ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license GNU/GPL, see ip_license.html
 */
function content_mod_text_photo() {
	this.title = '';
	this.new_photo = '';
	this.link_to_existing_photo = '';
	this.new_bigphoto = '';
	this.link_to_existing_bigphoto = '';
	this.my_name;
	this.menu_management = '';

	this.text = '';

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
	this.set_text = set_text;
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
		this.tinyMCE_loaded = 0;
		this.menu_management = menu_management;
	}

	function empty() {
		if ((this.text == '' || this.text.length < 4 || this.text == '<p><br mce_bogus="1"></p>')
				&& (this.new_photo == null || this.new_photo == '')
				&& this.link_to_existing_photo == '') {
			return true;
		} else {
			return false;
		}
	}

	function preview(worker_form, return_script, collection_number) {
		var bigphoto;
		var photo;
		if (this.new_photo != '') {
			photo = global_config_tmp_image_url + this.new_photo;
			bigphoto = global_config_tmp_image_url + this.new_bigphoto;
		} else {
			if (this.link_to_existing_photo != '') {
				photo = global_config_image_url + this.link_to_existing_photo;
				bigphoto = global_config_image_url
						+ this.link_to_existing_bigphoto;
			} else {
				photo = '';
				bigphoto = '';
			}
		}

		document.getElementById(worker_form).innerHTML = ''
				+ '<input name="module_id" value="'
				+ this.menu_management.module_id + '" />'
				+ '<input name="action" value="make_preview" />'
				+ '<input name="collection_number" value="' + collection_number
				+ '" />' + '<input name="module_key" value="text_photo" />'
				+ '<input name="group_key" value="text_photos" />'
				+ '<input name="layout" value="' + this.layout + '" />'
				+ '<input name="title" value="' + this.title + '" />'
				+ '<input name="photo" value="' + photo + '" />'
				+ '<input name="photo_big" value="' + bigphoto + '" />'
				+ '<textarea name="text" /></textarea>'
				+ '<input name="answer_function" value="' + return_script
				+ '" />';
		document.getElementById(worker_form).text.value = this.text;

		document.getElementById(worker_form).submit();

	}
	;

	function manage() {
		var div = document.createElement('div');

		var current_photo;
		var current_photo_img;
		if (this.new_photo != '')
			current_photo_img = '<img style="margin-bottom: 5px;" src="'
					+ global_config_tmp_image_url + this.new_photo + '" />';
		else {
			if (this.link_to_existing_photo != '')
				current_photo_img = '<img style="margin-bottom: 5px;" src="'
						+ global_config_image_url + this.link_to_existing_photo
						+ '" />';
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
				+ '<table cellspacing="0" cellpadding="0" class="ipCmsColumns">'
				+ '<tbody>'
				+ '<tr>'
				+ '<td colspan="2">'
				+ '<div id="management_'
				+ this.collection_number
				+ '_error" class="ipCmsError"></div>'
				+ '</td>'
				+ '</tr>'
				+ '<tr>'
				+ '<td style="border-bottom: 1px solid #dbdcdd;" class="ipCmsManagement" colspan="2">'
				+ '<label class="ipCmsModuleName">'
				+ widget_text_photo_text_photo
				+ '</label>'
				+ '<form id="mod_' + this.collection_number	+ '_layout" action="">'	+ mod_text_photo_layout + '</form>'
				+ '</td>'
				+ '</tr>'
				+ '<tr>'
				+ '<td class="ipCmsLeft">'
				+ current_photo_img
				+ '<form id="mod_'
				+ this.collection_number
				+ '_form" action="'
				+ global_config_base_url
				+ global_config_backend_worker_file
				+ '?module_id='
				+ this.menu_management.module_id + '&security_token=' + global_config_security_token
				+ '" target="mod_text_photo_iframe" method="post" enctype="multipart/form-data">'
				+ ''
				+

				'<input type="hidden" name="module_id" value="'
				+ this.menu_management.module_id
				+ '" />'
				+ '<label class="ipCmsTitle">'
				+ widget_text_photo_new_photo
				+ '</label>'
				+ '<div class="ipCmsInputFile"><input style="width: 210px;" class="ipCmsManager" type="file" name="new_photo" id="management_'
				+ this.collection_number
				+ '_new_photo"  /></div>'
				+ '<div class="ipCmsClear"></div>'
				+ '<input type="hidden" name="action" value="upload_tmp_image"/>'
				+ '<input type="hidden" name="photo_width[]" value="'
				+ widget_text_photo_width + '"/>'
				+ '<input type="hidden" name="photo_height[]" value="'
				+ widget_text_photo_height + '"/>'
				+ '<input type="hidden" name="photo_quality[]" value="'
				+ widget_text_photo_quality + '"/>'
				+ '<input type="hidden" name="photo_method[]" value="width"/>'
				+ '<input type="hidden" name="photo_forced[]" value="0"/>'
				+ '<input type="hidden" name="photo_width[]" value="'
				+ widget_text_photo_big_width + '"/>'
				+ '<input type="hidden" name="photo_height[]" value="'
				+ widget_text_photo_big_height + '"/>'
				+ '<input type="hidden" name="photo_quality[]" value="'
				+ widget_text_photo_big_quality + '"/>'
				+ '<input type="hidden" name="photo_method[]" value="width"/>'
				+ '<input type="hidden" name="photo_forced[]" value="0"/>'
				+ '<label class="ipCmsTitle">' + widget_text_photo_title
				+ '</label>' + '<div class="ipCmsInput"><input id="management_'
				+ this.collection_number + '_title" value="'
				+ this.title.replace(/"/g, "&quot;") + '"></div>' + '</form>'
				+ '</td>' + '<td style="vertical-align: top;">'
				+ '<textarea style="width: 100%;" id="management_'
				+ this.collection_number + '_text"></textarea>' + '</td>'
				+ '</div>' + '</tr>' + '</tbody></table>';
		div.getElementsByTagName('textarea')[0].value = this.text;

		return div;
	}
	;

	function manage_init() {

		var LayoutSelect = document.getElementById('mod_' + this.collection_number + '_layout').layout;
		for (index = 0; index < LayoutSelect.length; index++) {
			if (LayoutSelect[index].value == this.layout)
				LayoutSelect.selectedIndex = index;
		}
		
		
		tinyMCE
				.init( {
					theme : "advanced",
					mode : "exact",
					elements : "management_" + this.collection_number + "_text",
					plugins : "paste,inlinepopups",
					theme_advanced_buttons1 : "pastetext,separator,justifyleft,justifycenter,justifyright,separator,undo,redo,separator",
					theme_advanced_buttons2 : "bold,italic,underline,styleselect",
					theme_advanced_buttons3 : "bullist,numlist,outdent,indent,link,unlink,sub,sup",
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_statusbar_location : "bottom",
					theme_advanced_resizing : true,
					theme_advanced_resize_horizontal : false,
					/* theme_advanced_resize_vertical : true, */
					/* theme_advanced_path_location : "none", */
					extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
					height : 300,
					content_css : global_config_base_url
							+ global_config_template_url
							+ global_config_template + "/default_content.css",
					theme_advanced_styles : global_config_tiny_mce_styles,
					forced_root_block : "p",

					document_base_url : global_config_base_url,
					remove_script_host : false,
					relative_urls : false,
					convert_urls : false,

					paste_auto_cleanup_on_paste : true,
					paste_retain_style_properties : false,
					paste_strip_class_attributes : true,
					paste_remove_spans : true,
					paste_remove_styles : true,
					paste_convert_middot_lists : true,

					paste_preprocess : function(pl, o) {
						o.content = o.content.stripScripts();
						var tmpContent = o.content;

						tmpContent = tmpContent.replace(new RegExp(
								'<!(?:--[\\s\\S]*?--\s*)?>', 'g'), '') // remove
																		// comments
						tmpContent = tmpContent.replace(/(<([^>]+)>)/ig,
								"</p><p>");
						tmpContent = tmpContent.replace(/\n/ig, " "); // remove
																		// newlines
						tmpContent = tmpContent.replace(/\r/ig, " "); // remove
																		// newlines
						tmpContent = tmpContent.replace(/[\t]+/ig, " "); // remove
																			// tabs
						tmpContent = tmpContent.replace(/[ ]+/ig, " "); // remove
																		// multiple
																		// spaces

						tmpContent = tmpContent.replace(
								/(<\/p><p>([ ]*(<\/p><p>)*[ ]*)*<\/p><p>)/ig,
								"</p><p>"); // remove multiple paragraphs

						o.content = tmpContent;
						// Content string containing the HTML from the clipboard
						// alert(o.content);
					},
					paste_postprocess : function(pl, o) {
						// Content DOM node containing the DOM structure of the
						// clipboard
					// alert(o.node.innerHTML);
				}

				});
		tinyMCE.execCommand('mceAddControl', true,
				'management_' + this.collection_number + '_text');

	}

	function close() {
		tinyMCE.execCommand('mceRemoveControl', true,
				'management_' + this.collection_number + '_text');

	}
	function save(forced) {
		this.layout = document.getElementById('mod_' + this.collection_number + '_layout').layout.value;
		
		tinyMCE.execCommand('mceFocus', false,
				'management_' + this.collection_number + '_text');
		this.text = tinyMCE.get(
				'management_' + this.collection_number + '_text').getContent( {
			format : 'raw'
		});// or or tinyMCE.activeEditor.getContent()
		this.title = document
				.getElementById('management_' + this.collection_number + '_title').value;

		if (document
				.getElementById('management_' + this.collection_number + '_new_photo').value != '') {
			document.getElementById('mod_text_photo_action_after_photo_save').value = this.my_name
					+ '.response_after_photo_save(' + forced + ')';
			var form = document
					.getElementById('mod_' + this.collection_number + '_form');

			form.submit();
		} else
			this.menu_management
					.module_preview_save_response(this.collection_number);

	}

	function response_after_photo_save(forced) {

		var iFrameDocObj = window.frames['mod_text_photo_iframe'].window.document;
		if (iFrameDocObj.body.innerHTML != '') {
			var name0 = '';
			var name1 = '';

			errors = window.frames['mod_text_photo_iframe'].errors;

			if (errors.length == 0 || errors[0] == 4 || forced) {
				if (errors.length == 0) {
					this.new_photo = window.frames['mod_text_photo_iframe'].variables[0];
					this.new_bigphoto = window.frames['mod_text_photo_iframe'].variables[1];
				}
				this.menu_management
						.module_preview_save_response(this.collection_number);
			} else {
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
					answer = widget_text_photo_to_big;
					break;
				case '3':
					answer = widget_text_photo_partial;
					break;
				case '4':
					answer = widget_text_photo_no_file;
					break;
				case '6':
				case '7':
					answer = widget_text_photo_unknown;
					break;
				case '8':
					answer = widget_text_photo_bad_type;
					break;
				}

				document
						.getElementById('management_' + this.collection_number + '_error').innerHTML = answer;
				document
						.getElementById('management_' + this.collection_number + '_error').style.display = 'block';
				this.menu_management
						.module_preview_save_response_error(this.collection_number);
			}

			// this.menu_management.module_preview_save_response(this.collection_number);
		}
	}

	function get_answer(notes) { // answer after save_to_db
		/*
		 * for(var i=0; i<notes.length; i++) alert(notes[i]);
		 */

		return false;
	}

	function store_to_db_fields(row_number, menu_element_id) {
		if (this.id == null) {
			if (this.deleted == 0) {
				var fields = [];
				fields.push( [ 'action', 'new_module' ]);
				fields.push( [ 'group_key', 'text_photos' ]);
				fields.push( [ 'module_key', 'text_photo' ]);
				fields.push( [ 'layout', this.layout ]);
				fields.push( [
						'title',
						this.title.replace(/</g, "&lt;").replace(/>/g, "&gt;")
								.replace(/"/g, "&quot;") ]);
				fields.push( [ 'new_photo', this.new_photo ]);
				fields.push( [ 'text', this.text ]);
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
				fields.push( [ 'module_key', 'text_photo' ]);
				fields.push( [ 'layout', this.layout ]);
				fields.push( [
						'title',
						this.title.replace(/</g, "&lt;").replace(/>/g, "&gt;")
								.replace(/"/g, "&quot;") ]);
				fields.push( [ 'text', this.text ]);
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
				fields.push( [ 'module_key', 'text_photo' ]);
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

	function set_existing_photo(photo) {
		this.link_to_existing_photo = photo;
	}

	function set_existing_bigphoto(bigphoto) {
		this.link_to_existing_bigphoto = bigphoto;
	}

	function set_text(text) {
		this.text = text;
	}

}

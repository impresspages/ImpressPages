/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

function content_mod_html_code() {
	this.text = '';
	this.my_name = '';
	this.menu_management = '';

	this.preview = preview;
	this.manage = manage;
	this.save = save;
	this.init = init;
	this.store_to_db_fields = store_to_db_fields;
	this.get_answer = get_answer;
	this.set_text = set_text;
	this.close = close;
	this.manage_init = manage_init;
	this.empty = empty;

	var tinyMCE_loaded;
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
		if (this.text == '' || this.text.length < 3)
			return true;
		else
			return false;
	}

	function preview(worker_form, return_script, collection_number) {
		document.getElementById(worker_form).innerHTML = ''
				+ '<input name="action" value="make_preview" />'
				+ '<input name="collection_number" value="' + collection_number
				+ '" />' + '<input name="module_key" value="html_code" />'
				+ '<input name="group_key" value="misc" />'
				+ '<input name="layout" value="' + this.layout + '" />'
				+ '<textarea name="text" />' + this.text + '</textarea>'
				+ '<input name="answer_function" value="' + return_script
				+ '" />';
		document.getElementById(worker_form).submit();

	}
	;

	function manage() {
		var div = document.createElement('div');
		div.setAttribute(document.all ? "className" : "class",
				'ipCmsManagement');

		div.innerHTML = '' + '<label class="ipCmsModuleName">'
				+ widget_html_code_html_code + '</label>'
				+ '<form id="mod_' + this.collection_number	+ '_layout" action="">'	+ mod_html_code_layout + '</form>'
				+ '<div class="ipCmsModuleSeparator"></div>'
				+ '<textarea class="ipCmsHtmlCode" id="management_'
				+ this.collection_number + '_html_code">' + this.text
				+ '</textarea>' + '';

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
	function close() {

	}

	function save() {
		
		this.layout = document.getElementById('mod_' + this.collection_number + '_layout').layout.value;
		
		this.text = document
				.getElementById('management_' + this.collection_number + '_html_code').value;
		this.menu_management
				.module_preview_save_response(this.collection_number);
	}

	function get_answer(notes) {
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
				fields.push( [ 'group_key', 'misc' ]);
				fields.push( [ 'module_key', 'html_code' ]);
				fields.push( [ 'layout', this.layout ]);
				fields.push( [ 'text', this.text ]);
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
				fields.push( [ 'group_key', 'misc' ]);
				fields.push( [ 'module_key', 'html_code' ]);
				fields.push( [ 'layout', this.layout ]);
				fields.push( [ 'text', this.text ]);
				fields.push( [ 'id', this.id ]);
				fields.push( [ 'row_number', row_number ]);
				fields.push( [ 'visible', this.visible ]);

				return fields;
			} else {
				var fields = [];
				fields.push( [ 'action', 'delete_module' ]);
				fields.push( [ 'layout', this.layout ]);
				fields.push( [ 'group_key', 'misc' ]);
				fields.push( [ 'module_key', 'html_code' ]);
				fields.push( [ 'id', this.id ]);

				return fields;

			}
		}
		document.getElementById(worker_form).submit();
	}

	function set_text(text) {
		this.text = text;
	}

}

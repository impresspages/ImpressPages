/**
 * @package ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license GNU/GPL, see ip_license.html
 */

function content_mod_title() {
	this.title = '';
	this.level = '';
	this.my_name = '';
	this.menu_management = '';

	this.preview = preview;
	this.manage = manage;
	this.save = save;
	this.init = init;
	this.store_to_db_fields = store_to_db_fields;
	this.get_answer = get_answer;
	this.set_title = set_title;
	this.set_level = set_level;
	this.close = close;
	this.manage_init = manage_init;
	this.empty = empty;

	var collection_number;
	var id;
	var visible;
	var deleted;
	var layout;
	var tmpLevel;

	function init(collection_number, id, visible, my_name, menu_management) {
		this.my_name = my_name;
		this.collection_number = collection_number;
		this.id = id;
		this.visible = visible;
		this.deleted = 0;
		this.menu_management = menu_management;
		this.level = 1;
		this.tmpLevel = 1;		
	}

	function empty() {
		if (this.title == '')
			return true;
		else
			return false;

	}

	function preview(worker_form, return_script, collection_number) {

		document.getElementById(worker_form).innerHTML = ''
				+ '<input name="action" value="make_preview" />'
				+ '<input name="collection_number" value="' + collection_number
				+ '" />' + '<input name="module_key" value="title" />'
				+ '<input name="group_key" value="text_photos" />'
				+ '<input name="layout" value="' + this.layout + '" />'
				+ '<textarea name="title" />' + this.title + '</textarea>'
				+ '<input name="level" value="' + this.level + '"/>'
				+ '<input name="answer_function" value="' + return_script
				+ '" />';
		document.getElementById(worker_form).submit();

	}
	;

	function manage() {
		var div = document.createElement('div');
		div.setAttribute("className", 'ipCmsManagement');
		div.setAttribute("class", 'ipCmsManagement');
		div.innerHTML = ''
				+ '<label class="ipCmsModuleName">'
				+ widget_title_title
				+ '</label>'
				+ '<form id="mod_' + this.collection_number	+ '_layout" action="">'	+ mod_title_layout + '</form>'
				+ '<div class="ipCmsModuleSeparator"></div>'
				+ '<input type="hidden" id="management_'
				+ this.collection_number
				+ '_level" value="'
				+ this.level
				+ '">'
				+ '<img class="ipCmsIcon" onclick="'
				+ this.my_name
				+ '.tmpLevel=1; menu_mod_title_select_level('
				+ this.collection_number
				+ ', 1 )" id="management'
				+ this.collection_number
				+ '_title_level_1" src="'
				+ global_config_modules_url
				+ 'standard/content_management/widgets/text_photos/title/design/mod_title_h1.gif"/>'
				+ '<img class="ipCmsIcon" onclick="'
				+ this.my_name
				+ '.tmpLevel=2; menu_mod_title_select_level('
				+ this.collection_number
				+ ', 2 )" id="management'
				+ this.collection_number
				+ '_title_level_2" src="'
				+ global_config_modules_url
				+ 'standard/content_management/widgets/text_photos/title/design/mod_title_h2.gif"/>'
				+ '<img class="ipCmsIcon" onclick="'
				+ this.my_name
				+ '.tmpLevel=3; menu_mod_title_select_level('
				+ this.collection_number
				+ ', 3 )" id="management'
				+ this.collection_number
				+ '_title_level_3" src="'
				+ global_config_modules_url
				+ 'standard/content_management/widgets/text_photos/title/design/mod_title_h3.gif"/>'
				+ '<div class="ipCmsInput"><input id="management_'
				+ this.collection_number + '_title" value="'
				+ this.title.replace(/"/g, "&quot;") + '"></div>'
				+ '<div class="ipCmsClear"></div>';

		return div;
	}
	;

	function manage_init() {
		var LayoutSelect = document.getElementById('mod_' + this.collection_number + '_layout').layout;
		for (index = 0; index < LayoutSelect.length; index++) {
			if (LayoutSelect[index].value == this.layout)
				LayoutSelect.selectedIndex = index;
		}

		menu_mod_title_select_level(this.collection_number, this.level);
	}

	function save() {
		this.layout = document.getElementById('mod_' + this.collection_number + '_layout').layout.value;
		
		if (document.getElementById('management_' + this.collection_number + '_title'))
			this.title = document.getElementById('management_' + this.collection_number + '_title').value;
		
		this.level = this.tmpLevel;
		
		this.menu_management.module_preview_save_response(this.collection_number);
	}

	function get_answer(notes) {
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
				fields.push( [ 'module_key', 'title' ]);
				fields.push( [ 'layout', this.layout ]);
				fields.push( [
						'title',
						this.title.replace(/</g, "&lt;").replace(/>/g, "&gt;")
								.replace(/"/g, "&quot;") ]);
				fields.push( [ 'level', this.level ]);
				fields.push( [ 'content_element_id', menu_element_id ]);
				fields.push( [ 'row_number', row_number ]);
				fields.push( [ 'visible', this.visible ]);
				fields.push( [ '', ]);
				return fields;
			} else {
				return [];
			}
		} else {
			if (this.deleted == 0) {
				var fields = [];
				fields.push( [ 'action', 'update_module' ]);
				fields.push( [ 'group_key', 'text_photos' ]);
				fields.push( [ 'module_key', 'title' ]);
				fields.push( [ 'layout', this.layout ]);
				fields.push( [
						'title',
						this.title.replace(/</g, "&lt;").replace(/>/g, "&gt;")
								.replace(/"/g, "&quot;") ]);
				fields.push( [ 'level', this.level ]);
				fields.push( [ 'id', this.id ]);
				fields.push( [ 'row_number', row_number ]);
				fields.push( [ 'visible', this.visible ]);
				return fields;
			} else {
				var fields = [];
				fields.push( [ 'action', 'delete_module' ]);
				fields.push( [ 'group_key', 'text_photos' ]);
				fields.push( [ 'module_key', 'title' ]);
				fields.push( [ 'layout', this.layout ]);
				fields.push( [ 'id', this.id ]);
				return fields;
			}
		}
		document.getElementById(worker_form).submit();
	}

	function set_title(title) {
		this.title = title;
	}

	function set_level(level) {
		if (level == '')
			level = 1;
		this.level = level;
		this.tmpLevel = level;
	}
	
}

/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

function content_mod_separator() {
  this.my_name = '';
  this.menu_management = '';

  this.preview = preview;
  this.manage = manage;
  this.save = save;
  this.init = init;
  this.store_to_db_fields = store_to_db_fields;
  this.get_answer = get_answer;
  this.close = close;
  this.manage_init = manage_init;
  this.empty = empty;

  var collection_number;
  var id;
  var visible;
  var deleted;
  var layout;
  var isEmpty;

  function init(collection_number, id, visible, my_name, menu_management) {
    this.my_name = my_name;
    this.collection_number = collection_number;
    this.id = id;
    this.visible = visible;
    this.deleted = 0;
    this.menu_management = menu_management;
    this.level = 1;
    this.isEmpty = 1;
  }

  function empty() {
    return this.isEmpty;
  }

  function preview(worker_form, return_script, collection_number) {

    document.getElementById(worker_form).innerHTML = ''
    + '<input name="action" value="make_preview" />'
    + '<input name="collection_number" value="' + collection_number
    + '" />' + '<input name="module_key" value="separator" />'
    + '<input name="layout" value="' + this.layout + '" />'
    + '<input name="group_key" value="text_photos" />'
    + '<input name="answer_function" value="' + return_script
    + '" />';
    document.getElementById(worker_form).submit();

  }
  ;

  function manage() {
    var div = document.createElement('div');
    div.setAttribute(document.all ? "className" : "class",
      'ipCmsManagement');
    div.innerHTML = ''
    + '<label class="ipCmsModuleName">'
    + widget_separator_widget_title
    + '</label> '
    + '<form id="mod_' + this.collection_number	+ '_layout" action="">'	+ mod_separator_layout + '</form>'
    + '<div class="ipCmsModuleSeparator"></div>'
    ;

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

  function save() {
    this.layout = document.getElementById('mod_' + this.collection_number + '_layout').layout.value;
		
    this.isEmpty = false;

    this.menu_management
    .module_preview_save_response(this.collection_number);
  }

  function get_answer(notes) {
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
        fields.push( [ 'module_key', 'separator' ]);
        fields.push( [ 'layout', this.layout ]);
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
        fields.push( [ 'module_key', 'separator' ]);
        fields.push( [ 'layout', this.layout ]);
        fields.push( [ 'id', this.id ]);
        fields.push( [ 'row_number', row_number ]);
        fields.push( [ 'visible', this.visible ]);
        return fields;
      } else {
        var fields = [];
        fields.push( [ 'action', 'delete_module' ]);
        fields.push( [ 'group_key', 'text_photos' ]);
        fields.push( [ 'module_key', 'separator' ]);
        fields.push( [ 'layout', this.layout ]);
        fields.push( [ 'id', this.id ]);

        return fields;
      }
    }
    document.getElementById(worker_form).submit();
  }

}

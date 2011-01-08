/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2011 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

function content_mod_faq() {
  this.title = '';
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
  this.set_title = set_title;
  this.close = close;
  this.manage_init = manage_init;
  this.empty = empty;
  this.auto_size = auto_size;

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
    if (this.title == ''
      && (this.text.length < 4 || this.text == '<p><br mce_bogus="1"></p>'))
      return true;
    else
      return false;

  }

  function preview(worker_form, return_script, collection_number) {
    document.getElementById(worker_form).innerHTML = ''
    + '<input name="action" value="make_preview" />'
    + '<input name="collection_number" value="'
    + collection_number
    + '" />'
    + '<input name="module_key" value="faq" />'
    + '<input name="group_key" value="text_photos" />'
    + '<input name="layout" value="' + this.layout + '" />'
    + '<input name="id" value="'
    + id
    + '" />'
    + '<input name="title" value="'
    + this.title.replace(/</g, "&lt;").replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;") + '" />'
    + '<textarea name="text" /></textarea>'
    + '<input name="answer_function" value="' + return_script
    + '" />';
    document.getElementById(worker_form).text.value = this.text;

    document.getElementById(worker_form).submit();

  }
  ;

  function manage() {
    var div = document.createElement('div');
    var tinyMCE_script;
    div.setAttribute('name', 'management');
    div.setAttribute('style', 'margin: 0px; padding: 0px;');

    div.innerHTML = '' + '<div id="management_' + this.collection_number
    + '_error" class="ipCmsError"></div>'
    + '<div class="ipCmsManagement">'
    + '<label class="ipCmsModuleName">' + widget_faq_faq
    + '</label>'
    + '<form id="mod_' + this.collection_number	+ '_layout" action="">'	+ mod_faq_layout + '</form>'
    + '<div class="ipCmsModuleSeparator"></div>'
    + '<label class="ipCmsTitle">' + widget_faq_title
    + '</label>' + '<div class="ipCmsInput"><input id="management_'
    + this.collection_number + '_title" value="'
    + this.title.replace(/"/g, "&quot;") + '"></div>'
    + '<label class="ipCmsTitle">' + widget_faq_text + '</label>'
    + '<textarea style="width: 100%;" id="management_'
    + this.collection_number + '_text"></textarea>'
    + '<div style="height: 5px;"></div>' + '</div>' + '';
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

    eval(configWidgetTextPhotosFaqMceInit);


    tinyMCE.execCommand('mceAddControl', true,
      'management_' + this.collection_number + '_text');

  }

  function auto_size(text) {
    var size;
    size = text.length / 4;
    if (size < 250)
      return 250;
    else {
      if (size > 500)
        return 500;
    }
    return size;
  }

  function close() {
    tinyMCE.execCommand('mceRemoveControl', true,
      'management_' + this.collection_number + '_text');

  }

  function save() {
    this.layout = document.getElementById('mod_' + this.collection_number + '_layout').layout.value;

    this.title = document
    .getElementById('management_' + this.collection_number + '_title').value;

    if(this.title == ''){
      this.title = 'Undefined';
    }

    //      this.text = tinyMCE.getContent();//document.getElementById('management_' + this.collection_number + '_text').value;
    this.text = tinyMCE.get(
      'management_' + this.collection_number + '_text').getContent();// or or tinyMCE.activeEditor.getContent()

    this.menu_management
    .module_preview_save_response(this.collection_number);
  }

  function get_answer(notes) {
    /*for(var i=0; i<notes.length; i++)
		   alert(notes[i]);*/
    return false;
  }

  function store_to_db_fields(row_number, menu_element_id) {
    if (this.id == null) {
      if (this.deleted == 0) {
        var fields = [];
        fields.push( [ 'action', 'new_module' ]);
        fields.push( [ 'group_key', 'text_photos' ]);
        fields.push( [ 'module_key', 'faq' ]);
        fields.push( [ 'layout', this.layout ]);
        fields.push( [
          'title',
          this.title.replace(/</g, "&lt;").replace(/>/g, "&gt;")
          .replace(/"/g, "&quot;") ]);
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
        fields.push( [ 'group_key', 'text_photos' ]);
        fields.push( [ 'module_key', 'faq' ]);
        fields.push( [ 'layout', this.layout ]);
        fields.push( [ 'text', this.text ]);
        fields.push( [
          'title',
          this.title.replace(/</g, "&lt;").replace(/>/g, "&gt;")
          .replace(/"/g, "&quot;") ]);
        fields.push( [ 'id', this.id ]);
        fields.push( [ 'row_number', row_number ]);
        fields.push( [ 'visible', this.visible ]);
        return fields;
      } else {
        var fields = [];
        fields.push( [ 'action', 'delete_module' ]);
        fields.push( [ 'group_key', 'text_photos' ]);
        fields.push( [ 'module_key', 'faq' ]);
        fields.push( [ 'layout', this.layout ]);
        fields.push( [ 'id', this.id ]);
        return fields;
      }
    }
    document.getElementById(worker_form).submit();
  }

  function set_text(text) {
    this.text = text;
  }

  function set_title(title) {
    this.title = title;
  }

}

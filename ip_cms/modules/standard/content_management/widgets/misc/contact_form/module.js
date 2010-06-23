/**
 * @package ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license GNU/GPL, see ip_license.html
 */

function content_mod_contact_form() {
  this.thank_you = '';
  this.button = '';
  this.email_to = '';
  this.email_subject = '';
  this.fields = new Array();
  this.field_count = 1;
  this.my_name = '';
  this.menu_management = '';

  this.preview = preview;
  this.manage = manage;
  this.save = save;
  this.init = init;
  this.store_to_db_fields = store_to_db_fields;
  this.get_answer = get_answer;
  this.set_contact_form = set_contact_form;
  this.close = close;
  this.manage_init = manage_init;
  this.empty = empty;
  this.management_contact_form_add_field = management_contact_form_add_field;
  this.management_contact_form_remove_field = management_contact_form_remove_field;

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
    if (this.button == '' && this.thank_you == '' && this.email_to == ''
      && this.email_subject == '' && this.fields.length == 0)
      return true;
    else
      return false;

  }

  function preview(worker_form, return_script, collection_number) {

    var fields_string = '';
    var i = 0;
    while (i < this.fields.length) {
      fields_string = fields_string + ' <textarea name="field_' + i
      + '_name" />' + this.fields[i][0] + '</textarea> ';
      fields_string = fields_string + ' <textarea name="field_' + i
      + '_type" />' + this.fields[i][1] + '</textarea> ';
      fields_string = fields_string + ' <textarea name="field_' + i
      + '_required" />' + this.fields[i][2] + '</textarea> ';
      i++;
    }

    document.getElementById(worker_form).innerHTML = ''
    + '<input name="collection_number" value="make_preview" />'
    + '<input name="action" value="make_preview" />'
    + '<input name="collection_number" value="' + collection_number
    + '" />' + '<input name="module_key" value="contact_form" />'
    + '<input name="group_key" value="misc" />'
    + '<input name="layout" value="' + this.layout + '" />'
    + '<textarea name="thank_you" />' + this.thank_you
    + '</textarea>' + '<textarea name="button" />' + this.button
    + '</textarea>' + '<textarea name="email_to" />'
    + this.email_to + '</textarea>'
    + '<textarea name="email_subject" />' + this.email_subject
    + '</textarea>' + fields_string +

    '<input name="answer_function" value="' + return_script
    + '" />';

    document.getElementById(worker_form).submit();

  }
  ;

  function manage() {
    this.field_count = this.fields.length;

    var div = document.createElement('div');

    var html = ''
    + '<div class="ipCmsError" id="management_'
    + this.collection_number
    + '_error"></div>'
    + '<div class="ipCmsManagement2" style="padding-bottom: 5px;">'
    + '<label class="ipCmsModuleName">'
    + widget_contact_form_contact_form
    + '</label>'
    + '<form id="mod_' + this.collection_number	+ '_layout" action="">'	+ mod_contact_form_layout + '</form>'
    + '<div class="ipCmsModuleSeparator"></div>'
    + '<table id="management_'
    + this.collection_number
    + '_fields" class="ipCmsContactForm" cellspacing="0" cellpadding="0"><tbody>';

    if (this.fields.length == 0) {
      html = html
      + '<tr id="management_'
      + this.collection_number
      + '_field_0">'
      + '<td >'
      + '<label class="ipCmsTitle">'
      + widget_contact_form_name
      + '</label>'
      + '<div class="ipCmsInput"><input id="management_'
      + this.collection_number
      + '_field_0_name" value=""></div>'
      + '</td>'
      + '<td >'
      + '<label class="ipCmsTitle">'
      + widget_contact_form_type
      + '</label>'
      + '<div class="ipCmsInput"><select id="management_'
      + this.collection_number
      + '_field_0_type" value="">'
      + '<option value="text">'
      + widget_contact_form_text_row
      + '</option>'
      + '<option value="email">'
      + widget_contact_form_email
      + '</option>'
      + '<option value="textarea">'
      + widget_contact_form_text_field
      + '</option>'
      + '<option value="file">'
      + widget_contact_form_file
      + '</option>'
      + '</select></div>'
      + '</td>'
      + '<td >'
      + '<label class="ipCmsTitle">'
      + widget_contact_form_required
      + '</label><input type="checkbox" id="management_'
      + this.collection_number
      + '_field_0_required" value="">'
      + '</td>'
      + '<td >'
      + '<label class="ipCmsTitle">&nbsp;</label>'
      + '<a><img class="ipCmsIcon" onclick="'
      + this.my_name
      + '.management_contact_form_remove_field(0)" src="'
      + global_config_modules_url
      + 'standard/content_management/design/icon_delete_tr.gif" /></a>'
      + '</td>' + '</tr>';
    } else {
      var i = 0;
      while (i < this.fields.length) {
        var name = '';
        var type = '';
        var required = '';
        var del = '';
        if (i == 0) {
          var name = '<label class="ipCmsTitle">' + widget_contact_form_name + '</label>';
          var type = '<label class="ipCmsTitle">' + widget_contact_form_type + '</label>';
          var required = '<label class="ipCmsTitle">' + widget_contact_form_required + '</label>';
          var del = '<label class="ipCmsTitle">&nbsp;</label>';
        }
        var checked = '';
        if (this.fields[i][2])
          checked = ' checked ';

        var type_text = '';
        var type_email = '';
        var type_textarea = '';
        var type_file = '';

        if (this.fields[i][1] == 'text')
          type_text = ' selected ';
        if (this.fields[i][1] == 'email')
          type_email = ' selected ';
        if (this.fields[i][1] == 'textarea')
          type_textarea = ' selected ';
        if (this.fields[i][1] == 'file')
          type_file = ' selected ';

        html = html
        + '<tr id="management_'
        + this.collection_number
        + '_field_'
        + i
        + '">'
        + '<td >'
        +

        ''
        + name
        + '<div class="ipCmsInput"><input id="management_'
        + this.collection_number
        + '_field_'
        + i
        + '_name" value="'
        + this.fields[i][0].replace(/"/g, "&quot;")
        + '"></div>'
        + '</td>'
        + '<td >'
        + ''
        + type
        + '<div class="ipCmsInput"><select id="management_'
        + this.collection_number
        + '_field_'
        + i
        + '_type" value="'
        + this.fields[i][1].replace(/"/g, "&quot;")
        + '">'
        + '<option value="text" '
        + type_text
        + '>'
        + widget_contact_form_text_row
        + '</option>'
        + '<option value="email" '
        + type_email
        + '>'
        + widget_contact_form_email
        + '</option>'
        + '<option value="textarea" '
        + type_textarea
        + '>'
        + widget_contact_form_text_field
        + '</option>'
        + '<option value="file" '
        + type_file
        + '>'
        + widget_contact_form_file
        + '</option>'
        + '</select></div>'
        + '</td>'
        + '<td >'
        + ''
        + required
        + '<input  type="checkbox" '
        + checked
        + 'id="management_'
        + this.collection_number
        + '_field_'
        + i
        + '_required" >'
        + '</td>'
        + '<td >'
        + del
        + '<a><img class="ipCmsIcon" onclick="'
        + this.my_name
        + '.management_contact_form_remove_field('
        + i
        + ')" src="'
        + global_config_modules_url
        + 'standard/content_management/design/icon_delete_tr.gif" /></a>'
        + '</td>' + '</tr>';
        i++;
      }

    }
    html = html + '</tbody></table>';

    html = html
    + '<a class="ipCmsButton2" style="cursor: pointer;" onclick="'
    + this.my_name
    + '.management_contact_form_add_field()"><img  src="'
    + global_config_modules_url
    + 'standard/content_management/design/icon_add_tr.gif" />&nbsp;'
    + widget_contact_form_new_field + '</a>'
    + '<div class="ipCmsClear"></div>' + '</div>';

    // '<div id="management_' + this.collection_number + '_fields">'+

    html = html
    + '<div class="ipCmsSeparator"></div>'
    + '<div class="ipCmsManagement">'
    +

    '<label class="ipCmsTitle">'
    + widget_contact_form_thank_you
    + '</label>'
    + '<div class="ipCmsInput"><input class="ipCmsInput" id="management_'
    + this.collection_number
    + '_thank_you" value="'
    + this.thank_you.replace(/"/g, "&quot;")
    + '"></div>'
    + '<label class="ipCmsTitle">'
    + widget_contact_form_button
    + '</label>'
    + '<div class="ipCmsInput"><input class="ipCmsInput"  id="management_'
    + this.collection_number
    + '_button" value="'
    + this.button.replace(/"/g, "&quot;")
    + '"></div>'
    + '<label class="ipCmsTitle">'
    + widget_contact_form_email_to
    + '</label>'
    + '<div class="ipCmsInput"><input class="ipCmsInput"  id="management_'
    + this.collection_number
    + '_email_to" value="'
    + this.email_to.replace(/"/g, "&quot;")
    + '"></div>'
    + '<label class="ipCmsTitle">'
    + widget_contact_form_email_subject
    + '</label>'
    + '<div class="ipCmsInput"><input class="ipCmsInput" id="management_'
    + this.collection_number + '_email_subject" value="'
    + this.email_subject.replace(/"/g, "&quot;") + '"></div>' +

    '</div>' + '' +

    '';
    div.innerHTML = html;
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
		
    var i = 0;
    this.fields = new Array();
    while (document.getElementById('management_' + this.collection_number
      + '_field_' + i + '_name')) {
      var field = new Array();
      field[0] = document.getElementById('management_'
        + this.collection_number + '_field_' + i + '_name').value;
      field[1] = document.getElementById('management_'
        + this.collection_number + '_field_' + i + '_type').value;
      if (document.getElementById('management_' + this.collection_number
        + '_field_' + i + '_required').checked)
        field[2] = 1;
      else
        field[2] = 0;
      if (field[0] != '') {
        this.fields.push(field);
      }
      i++;
    }

    this.thank_you = document
    .getElementById('management_' + this.collection_number + '_thank_you').value;
    this.button = document
    .getElementById('management_' + this.collection_number + '_button').value;
    this.email_to = document
    .getElementById('management_' + this.collection_number + '_email_to').value;
    this.email_subject = document
    .getElementById('management_' + this.collection_number + '_email_subject').value;

    if (!forced
      && (this.fields.length == 0 || this.thank_you == ''
        || this.button == '' || this.email_to == '' || this.email_subject == '')) {
      document
      .getElementById('management_' + this.collection_number + '_error').innerHTML = widget_contact_form_required_fields;
      document
      .getElementById('management_' + this.collection_number + '_error').style.display = 'block';
      this.menu_management
      .module_preview_save_response_error(this.collection_number);
    } else
      this.menu_management
      .module_preview_save_response(this.collection_number);

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

        var i = 0;
        while (i < this.fields.length) {
          fields.push( [ 'field_' + i + '_name', this.fields[i][0] ]);
          fields.push( [ 'field_' + i + '_type', this.fields[i][1] ]);

          if (this.fields[i][2])
            fields.push( [ 'field_' + i + '_required', 1 ]);
          else
            fields.push( [ 'field_' + i + '_required', 0 ]);
          i++;
        }

        fields.push( [ 'action', 'new_module' ]);
        fields.push( [ 'group_key', 'misc' ]);
        fields.push( [ 'module_key', 'contact_form' ]);
        fields.push( [ 'content_element_id', menu_element_id ]);
        fields.push( [ 'layout', this.layout ]);
        fields.push( [ 'thank_you', this.thank_you ]);
        fields.push( [ 'button', this.button ]);
        fields.push( [ 'email_to', this.email_to ]);
        fields.push( [ 'email_subject', this.email_subject ]);
        fields.push( [ 'row_number', row_number ]);
        fields.push( [ 'visible', this.visible ]);
        return fields;

      } else {
        return [];
      }
    } else {
      if (this.deleted == 0) {
        var fields = [];

        var i = 0;
        while (i < this.fields.length) {
          fields.push( [ 'field_' + i + '_name', this.fields[i][0] ]);
          fields.push( [ 'field_' + i + '_type', this.fields[i][1] ]);

          if (this.fields[i][2])
            fields.push( [ 'field_' + i + '_required', 1 ]);
          else
            fields.push( [ 'field_' + i + '_required', 0 ]);
          i++;
        }

        fields.push( [ 'action', 'update_module' ]);
        fields.push( [ 'group_key', 'misc' ]);
        fields.push( [ 'module_key', 'contact_form' ]);
        fields.push( [ 'thank_you', this.thank_you ]);
        fields.push( [ 'layout', this.layout ]);
        fields.push( [ 'button', this.button ]);
        fields.push( [ 'email_to', this.email_to ]);
        fields.push( [ 'email_subject', this.email_subject ]);
        fields.push( [ 'row_number', row_number ]);
        fields.push( [ 'visible', this.visible ]);
        fields.push( [ 'id', this.id ]);
        return fields;
      } else {
        var fields = [];
        fields.push( [ 'action', 'delete_module' ]);
        fields.push( [ 'group_key', 'misc' ]);
        fields.push( [ 'layout', this.layout ]);
        fields.push( [ 'module_key', 'contact_form' ]);
        fields.push( [ 'id', this.id ]);
        return fields;
      }
    }
    document.getElementById(worker_form).submit();
  }

  function set_contact_form(contact_form) {
    this.contact_form = contact_form;
  }

  function management_contact_form_remove_field(field_number) {
    var field = document.getElementById('management_'
      + this.collection_number + '_field_' + field_number);
    var field_name = document.getElementById('management_'
      + this.collection_number + '_field_' + field_number + '_name');
    field_name.value = '';
    field.style.display = "none";

    var i = 0;
    var all_hidden = true;
    while (document.getElementById('management_' + this.collection_number
      + '_field_' + i)) {
      if (document.getElementById('management_' + this.collection_number
        + '_field_' + i).style.display != 'none')
        all_hidden = false;
      i++;
    }
    if (all_hidden)
      this.management_contact_form_add_field();
  }

  function management_contact_form_add_field() {
    var fields_table = document
    .getElementById('management_' + this.collection_number + '_fields').childNodes[0];
    var tr = document.createElement('tr');
    tr.setAttribute('id', 'management_' + this.collection_number
      + '_field_' + fields_table.childNodes.length);

    var td1 = document.createElement('td');
    var td2 = document.createElement('td');
    var td3 = document.createElement('td');
    var td4 = document.createElement('td');
    var select = document.createElement('select');
    select.setAttribute('id', 'management_' + this.collection_number
      + '_field_' + fields_table.childNodes.length + '_type');

    td1.innerHTML = '<div class="ipCmsInput"><input style="width: 99%;" id="management_'
    + this.collection_number
    + '_field_'
    + fields_table.childNodes.length + '_name" value=""></div>';

    var option = document.createElement('option');
    option.setAttribute('value', 'text');
    option.appendChild(document
      .createTextNode(widget_contact_form_text_row));

    select.appendChild(option);

    var option = document.createElement('option');
    option.setAttribute('value', 'email');
    option.appendChild(document.createTextNode(widget_contact_form_email));

    select.appendChild(option);

    var option = document.createElement('option');
    option.setAttribute('value', 'textarea');
    option.appendChild(document
      .createTextNode(widget_contact_form_text_field));

    select.appendChild(option);

    var option = document.createElement('option');
    option.setAttribute('value', 'file');
    option.appendChild(document.createTextNode(widget_contact_form_file));

    select.appendChild(option);

    var tmp_div = document.createElement("div");
    tmp_div.setAttribute('class', 'ipCmsInput');

    tmp_div.appendChild(select);
    td2.appendChild(tmp_div);

    td3.innerHTML = '<input  type="checkbox" id="management_'
    + this.collection_number + '_field_'
    + fields_table.childNodes.length + '_required" value="">';
    td4.innerHTML = '<a><img onclick="'
    + this.my_name
    + '.management_contact_form_remove_field('
    + fields_table.childNodes.length
    + ')" src="'
    + global_config_modules_url
    + 'standard/content_management/design/icon_delete_tr.gif" /></a>';

    tr.appendChild(td1);
    tr.appendChild(td2);
    tr.appendChild(td3);
    tr.appendChild(td4);

    fields_table.appendChild(tr);
    module.field_count++;

  }

}

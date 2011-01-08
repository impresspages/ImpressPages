/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 JSC Apro media.
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

  this.values_show = values_show;
  this.values_close = values_close;
  this.values_save = values_save;
  this.change_type = change_type;

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
      fields_string = fields_string + ' <textarea name="field_' + i + '_name" />' + this.fields[i][0] + '</textarea> ';
      fields_string = fields_string + ' <textarea name="field_' + i + '_type" />' + this.fields[i][1] + '</textarea> ';
      fields_string = fields_string + ' <textarea name="field_' + i + '_required" />' + this.fields[i][2] + '</textarea> ';
      fields_string = fields_string + ' <textarea name="field_' + i + '_values" />' + this.fields[i][3] + '</textarea> ';
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
  

  function manage() {
    this.field_count = this.fields.length;

    var div = document.createElement('div');

    var html = ''

    + '<div id="ip_cms_contact_form_values" style="display: none;">'
    + '<div  onclick="LibDefault.cancelBubbling(event)" id="ip_cms_contact_form_values_border" class="ipCmsBorder">'
    + '<div class="ipCmsHead">'
    + '<img '
    + 'alt="Close"'
    + 'onmouseover="this.src=\'' + global_config_modules_url + 'standard/content_management/design/popup_close_hover.gif\'"'
    + 'onmouseout="this.src=\'' + global_config_modules_url + 'standard/content_management/design/popup_close.gif\'"'
    + 'onclick="' + this.my_name + '.values_close();" style="cursor: pointer; float: right;" src="' + global_config_modules_url + 'standard/content_management/design/popup_close.gif"/>'
    + '' + widget_contact_form_values_popup_title + ''
    + '</div>'
    + '<div class="ipCmsManagement" >'
    + '<form  id="f_contact_form_values" action="" onsubmit="content_mod_contact_form.values_save(); return false;">'
    + '<div>'
    + '<label class="ipCmsTitle">' + widget_contact_form_values_field_title + '</label>'
    + '<input type="hidden" name="field_number"  value="" />'
    + '<textarea id="" name="values"  value=""></textarea>'


    + '<input type="submit" style="width:0px; height: 0px; overflow: hidden; border: 0pt none;" />'
    + '</div>'
    + '</form>'
    + '</div>'
    + '<div class="ipCmsModuleControlButtons">'
    + '<a onclick="' + this.my_name + '.values_save();" class="ipCmsButton">Confirm</a>'
    + '<a onclick="' + this.my_name + '.values_close();"class="ipCmsButton">Cancel</a>'
    + '<div style="clear: both;"></div>'
    + '</div>'
    + '</div>'
    + '</div>'



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
      + '<tr id="management_' + this.collection_number + '_field_0">'
          + '<td >'
              + '<label class="ipCmsTitle">' + widget_contact_form_name + '</label>'
              + '<div class="ipCmsInput"><input id="management_' + this.collection_number + '_field_0_name" value=""></div>'
          + '</td>'
          + '<td>'
              + '<label class="ipCmsTitle">&nbsp;</label>'
              + '<a id="management_' + this.collection_number + '_field_0_list" href="#" onclick="' + this.my_name + '.values_show(0); return false;" style="display: none;">'
              + ' <img border="0" src="' + global_config_modules_url + 'standard/content_management/widgets/misc/contact_form/design/list.gif" alt="' + widget_contact_form_values_popup_title + '" title="' + widget_contact_form_values_popup_title + '" />'
              + '</a>'
              + '<div style="float: left; width: 0; height: 0; overflow: hidden;"><textarea id="management_' + this.collection_number + '_field_0_values" ></textarea></div>'
          + '</td>'
          + '<td >'
              + '<label class="ipCmsTitle">' + widget_contact_form_type + '</label>'
              + '<div class="ipCmsInput">'
                  + '<select onchange="' + this.my_name + '.change_type(this, 0, ' + this.collection_number + ');" id="management_' + this.collection_number + '_field_0_type" value="">'
                      + '<option value="text">' + widget_contact_form_text + '</option>'
                      + '<option value="text_multiline">' + widget_contact_form_text_multiline + '</option>'
                      + '<option value="email">' + widget_contact_form_email + '</option>'
                      + '<option value="file">' + widget_contact_form_file + '</option>'
                      + '<option value="select">' + widget_contact_form_select + '</option>'
                      + '<option value="checkbox">' + widget_contact_form_checkbox + '</option>'
                      + '<option value="radio">' + widget_contact_form_radio + '</option>'
                  + '</select>'
              + '</div>'
          + '</td>'
          + '<td >'
              + '<label class="ipCmsTitle">' + widget_contact_form_required + '</label>'
              + '<input type="checkbox" id="management_' + this.collection_number + '_field_0_required" value="">'
          + '</td>'
          + '<td >'
              + '<label class="ipCmsTitle">&nbsp;</label>'
              + '<a><img class="ipCmsIcon" onclick="' + this.my_name + '.management_contact_form_remove_field(0)" src="' + global_config_modules_url + 'standard/content_management/design/icon_delete_tr.gif" /></a>'
          + '</td>'
      + '</tr>';
    } else {
      var i = 0;
      while (i < this.fields.length) {
        var name = '';
        var values = '';
        var type = '';
        var required = '';
        var del = '';
        if (i == 0) {
          name = '<label class="ipCmsTitle">' + widget_contact_form_name + '</label>';
          values = '<label class="ipCmsTitle">&nbsp;</label>';
          type = '<label class="ipCmsTitle">' + widget_contact_form_type + '</label>';
          required = '<label class="ipCmsTitle">' + widget_contact_form_required + '</label>';
          del = '<label class="ipCmsTitle">&nbsp;</label>';
        }
        var checked = '';
        if (this.fields[i][2])
          checked = ' checked ';

        var type_text = '';
        var type_text_multiline = '';
        var type_email = '';
        var type_file = '';
        var type_select = '';
        var type_checkbox = '';
        var type_radio = '';

        var display_list = 'none'; //by default don't show values select icon

        if (this.fields[i][1] == 'text')
          type_text = ' selected ';

        if (this.fields[i][1] == 'text_multiline')
          type_text_multiline = ' selected ';

        if (this.fields[i][1] == 'email')
          type_email = ' selected ';

        if (this.fields[i][1] == 'file')
          type_file = ' selected ';

        if (this.fields[i][1] == 'select'){
          type_select = ' selected ';
          display_list = ''; //display values insert icon
        }

        if (this.fields[i][1] == 'checkbox'){
          type_checkbox = ' selected ';
        }

        if (this.fields[i][1] == 'radio'){
          type_radio = ' selected ';
          display_list = ''; //display values insert icon
        }

        html = html
        + '<tr id="management_' + this.collection_number + '_field_' + i + '">'
        + '<td >'
            + name
            + '<div class="ipCmsInput">'
            + '<input id="management_' + this.collection_number + '_field_' + i + '_name" value="' + this.fields[i][0].replace(/"/g, "&quot;") + '">'
            + '</div>'
        + '</td>'
        + '<td >'
          + values
          + '<a  id="management_' + this.collection_number + '_field_' + i + '_list" href="#" onclick="' + this.my_name + '.values_show(' + i + '); return false;" style="display: ' + display_list + '">'
          + ' <img border="0" src="' + global_config_modules_url + 'standard/content_management/widgets/misc/contact_form/design/list.gif" alt="' + widget_contact_form_values_popup_title + '" title="' + widget_contact_form_values_popup_title + '" />'
          + '</a>'
          + '<div style="float: left; width: 0; height: 0; overflow: hidden;"><textarea id="management_' + this.collection_number + '_field_' + i + '_values" >' + this.fields[i][3] + '</textarea></div>'
        + '</td>'
        + '<td >'
            + type
            + '<div class="ipCmsInput"><select onchange="' + this.my_name + '.change_type(this, ' + i + ', ' + this.collection_number + ');" id="management_' + this.collection_number + '_field_' + i + '_type" value="' + this.fields[i][1].replace(/"/g, "&quot;") + '">'
            + '<option value="text" ' + type_text + '>' + widget_contact_form_text + '</option>'
            + '<option value="text_multiline" ' + type_text_multiline + '>' + widget_contact_form_text_multiline + '</option>'
            + '<option value="email" ' + type_email + '>' + widget_contact_form_email + '</option>'
            + '<option value="file" ' + type_file + '>' + widget_contact_form_file + '</option>'
            + '<option value="select" ' + type_select + '>' + widget_contact_form_select + '</option>'
            + '<option value="checkbox" ' + type_checkbox + '>' + widget_contact_form_checkbox + '</option>'
            + '<option value="radio" ' + type_radio + '>' + widget_contact_form_radio + '</option>'
            + '</select></div>'
        + '</td>'
        + '<td >'
            + required
            + '<input  type="checkbox" ' + checked + 'id="management_' + this.collection_number + '_field_' + i + '_required" >'
        + '</td>'
        + '<td >'
            + del
            + '<a><img class="ipCmsIcon" onclick="' + this.my_name + '.management_contact_form_remove_field(' + i + ')" src="' + global_config_modules_url + 'standard/content_management/design/icon_delete_tr.gif" /></a>'
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
  


  function values_show(field_number) {
    var form = document.getElementById(('f_contact_form_values'));
//    var form_popup = document.getElementById(('ip_cms_contact_form_values'));

//    form_popup.values.value = 'test';
    form.field_number.value = field_number;

    var popup = document.getElementById('ip_cms_contact_form_values');
    var border = document.getElementById('ip_cms_contact_form_values_border');

    popup.style.display = 'block';
    form.values.focus();
    border.style.marginTop = Math.abs((LibWindow.getWindowHeight() - border.offsetHeight)/2) + 'px';

    form.values.value = document.getElementById('management_' + this.collection_number + '_field_' + field_number + '_values').value;
    //form.values.value = this.fields[field_number][3];
  }
  function values_close(){
    document.getElementById('ip_cms_contact_form_values').style.display = 'none';
  }

  function values_save() {
    var form = document.getElementById('f_contact_form_values');
    var values = form.values.value;
    //this.fields[form.field_number.value][3] = values;
    document.getElementById('management_' + this.collection_number + '_field_' + form.field_number.value + '_values').value = values
    values_close();
  }

/* In some situations we can't get collection_number using this */
  function change_type(select, field_number, collection_number) {
      switch(select.options[select.selectedIndex].value){
          case 'select':
          case 'radio':
             document.getElementById('management_' + collection_number + '_field_' + field_number + '_list').style.display = '';
          break;
          default:
             document.getElementById('management_' + collection_number + '_field_' + field_number + '_list').style.display = 'none';
          break;
      }
  }

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
    while (document.getElementById('management_' + this.collection_number + '_field_' + i + '_name')) {
      var field = new Array();
      field[0] = document.getElementById('management_' + this.collection_number + '_field_' + i + '_name').value;
      field[1] = document.getElementById('management_' + this.collection_number + '_field_' + i + '_type').value;
      if (document.getElementById('management_' + this.collection_number
        + '_field_' + i + '_required').checked)
        field[2] = 1;
      else
        field[2] = 0;
      field[3] = document.getElementById('management_' + this.collection_number + '_field_' + i + '_values').value;

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
          fields.push( [ 'field_' + i + '_values', this.fields[i][3] ]);

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
          fields.push( [ 'field_' + i + '_values', this.fields[i][3] ]);

          fields.push( [ 'field_' + i + '_values', this.fields[i][3] ]);

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

  function addChange(el, n, collection_number)
  {
    el.onchange = function() {change_type(el, n, collection_number);};
  }

  function management_contact_form_add_field() {
    var fields_table = document
    .getElementById('management_' + this.collection_number + '_fields').childNodes[0];
    var tr = document.createElement('tr');
    tr.setAttribute('id', 'management_' + this.collection_number
      + '_field_' + fields_table.childNodes.length);

    var td1 = document.createElement('td');
    var td1_1 = document.createElement('td');
    var td2 = document.createElement('td');
    var td3 = document.createElement('td');
    var td4 = document.createElement('td');
    var select = document.createElement('select');
    select.setAttribute('id', 'management_' + this.collection_number
      + '_field_' + fields_table.childNodes.length + '_type');
    addChange(select, fields_table.childNodes.length, this.collection_number);
    td1.innerHTML = '<div class="ipCmsInput"><input style="width: 99%;" id="management_'
    + this.collection_number
    + '_field_'
    + fields_table.childNodes.length + '_name" value=""></div>';


    td1_1.innerHTML = ''
        + '<td >'
          + '<a style="display: none;" id="management_' + this.collection_number + '_field_' + fields_table.childNodes.length + '_list" href="#" onclick="' + this.my_name + '.values_show(' + fields_table.childNodes.length + '); return false;">'
          + ' <img border="0" src="' + global_config_modules_url + 'standard/content_management/widgets/misc/contact_form/design/list.gif" alt="' + widget_contact_form_values_popup_title + '" title="' + widget_contact_form_values_popup_title + '" />'
          + '</a>'
          + '<div style="float: left; width: 0; height: 0; overflow: hidden;"><textarea style="visible: none"  id="management_' + this.collection_number + '_field_' + fields_table.childNodes.length + '_values" ></textarea></div>'
        + '</td>';




    var option = document.createElement('option');
    option.setAttribute('value', 'text');
    option.appendChild(document.createTextNode(widget_contact_form_text));
    select.appendChild(option);

    option = document.createElement('option');
    option.setAttribute('value', 'text_multiline');
    option.appendChild(document.createTextNode(widget_contact_form_text_multiline));
    select.appendChild(option);

    option = document.createElement('option');
    option.setAttribute('value', 'email');
    option.appendChild(document.createTextNode(widget_contact_form_email));
    select.appendChild(option);

    option = document.createElement('option');
    option.setAttribute('value', 'file');
    option.appendChild(document.createTextNode(widget_contact_form_file));
    select.appendChild(option);

    option = document.createElement('option');
    option.setAttribute('value', 'select');
    option.appendChild(document.createTextNode(widget_contact_form_select));
    select.appendChild(option);

    option = document.createElement('option');
    option.setAttribute('value', 'checkbox');
    option.appendChild(document.createTextNode(widget_contact_form_checkbox));
    select.appendChild(option);

    option = document.createElement('option');
    option.setAttribute('value', 'radio');
    option.appendChild(document.createTextNode(widget_contact_form_radio));
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
    tr.appendChild(td1_1);
    tr.appendChild(td2);
    tr.appendChild(td3);
    tr.appendChild(td4);

    fields_table.appendChild(tr);
    module.field_count++;

  }

}

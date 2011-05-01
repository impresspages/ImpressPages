/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */


$(document).ready( function () {
  initializeTreeManagement('tree');  
  
  $("#tree").bind("select_node.jstree", treeSelect);  

  $("#tree_popup").bind("select_node.jstree", treePopupSelect);  
  
  $("#page_properties").delegate("form", "submit", function() { savePage(); return false; } );  

} );


function savePage() {

  data = Object();
  data.pageId = $('#formGeneral input[name="pageId"]').val();
  data.zoneName = $('#formGeneral input[name="zoneName"]').val();
  data.buttonTitle = $('#formGeneral input[name="buttonTitle"]').val();
  data.visible = $('#formGeneral input[name="visible"]').attr('checked') ? 1 : 0;
  data.createdOn = $('#formGeneral input[name="createdOn"]').val();
  data.lastModified = $('#formGeneral input[name="lastModified"]').val();

  data.pageTitle = $('#formSEO input[name="pageTitle"]').val();
  data.keywords = $('#formSEO textarea[name="keywords"]').val();
  data.description = $('#formSEO textarea[name="description"]').val();
  data.url = $('#formSEO input[name="url"]').val();
  data.type = $('#formAdvanced input:checked[name="type"]').val();
  data.redirectURL = $('#formAdvanced input[name="redirectURL"]').val();
  
  data.action = 'updatePage';
  
  $.ajax({
    type: 'POST',
    url: postURL,
    data: data,
    success: savePageSuccess,
    dataType: 'json'
  });    
  
}

function savePageSuccess (response) {
  if (! response) {
    return;
  }
  
  $('#page_properties .error').hide();
  console.log(response);
  console.log(response.errors);
  if (response.errors) {
    for (var errorKey in response.errors) {
      var error = response.errors[errorKey];
      console.log(error); 
      $('#' + error.field + 'Error').text(error.message).show();
    }
  } else {
    
  }
  alert('done');
//  
//  if($errorCreatedOn)
//    $answer .= 'document.getElementById(\'property_created_on_error\').style.display = \'block\';';
//
//  if($errorLastModified)
//    $answer .= 'document.getElementById(\'property_last_modified_error\').style.display = \'block\';';
//
//  if($errorEmptyRedirectUrl)
//    $answer .= 'document.getElementById(\'property_type_error\').style.display = \'block\';';
//
//  $answer .= 'document.getElementById(\'loading\').style.display = \'none\';';
  

//  if (!$_POST['visible']) {
//    $icon = 'node.ui.addClass(\'x-tree-node-disabled \');';
//  } else {
//    $icon = '';
//  }
//
//  echo '
//  document.getElementById(\'loading\').style.display = \'none\';
//   
//  document.getElementById(\'property_last_modified_error\').style.display = \'none\';
//  document.getElementById(\'property_created_on_error\').style.display = \'none\';      
//  document.getElementById(\'property_type_error\').style.display = \'none\';       
//   
//   
//  var form = document.getElementById(\'property_form\');
//  form.property_url.value = \''.\Library\Php\Js\Functions::htmlToString($_POST['url']).'\';
//   
//   
//  var node = iTree.getTree().getSelectionModel().getSelectedNode();
//  node.setText(\''.\Library\Php\Js\Functions::htmlToString($_POST['buttonTitle']).'\');
//  node.ui.removeClass(\'x-tree-node-disabled\');
//    '.$icon.'
//  ';    
  
  
}


function treeSelect(event, data) {
  var tree = jQuery.jstree._reference ( '#tree' );
  var node = tree.get_selected();
  if (node.attr('rel') == 'page') {
    
    data = Object();
    data.id = node.attr('id');
    data.zoneName = node.attr('zoneName');
    data.websiteURL = node.attr('websiteURL');
    data.languageId = node.attr('languageId');
    data.zoneName = node.attr('zoneName');
    data.type = node.attr('rel');
    data.action = 'getPage';
    
    
    $.ajax({
      type: 'POST',
      url: postURL,
      data: data,
      success: treeSelectSuccess,
      dataType: 'json'
    });    
  }
}

function treeSelectSuccess (response) {
  if (response && response.html) {
    $('#page_properties').html(response.html);
  }
}

function treePopupSelect(event, data) {
  var tree = jQuery.jstree._reference ( '#tree_popup' );
  var node = tree.get_selected();
    
  data = Object();
  data.id = node.attr('id');
  data.zoneName = node.attr('zoneName');
  data.websiteURL = node.attr('websiteURL');
  data.languageId = node.attr('languageId');
  data.zoneName = node.attr('zoneName');
  data.type = node.attr('rel');
  data.action = 'getPageLink';
  
  
  $.ajax({
    type: 'POST',
    url: postURL,
    data: data,
    success: treePopupSelectSuccess,
    dataType: 'json'
  });    
}



function treePopupSelectSuccess (response) {  
  if (response && response.link) {
    $('#formAdvanced input[name="redirectURL"]').val(response.link);
    $('#formAdvanced input[name="type"][value="redirect"]').attr("checked","checked");
  }
}

function initializeTreeManagement(id) {
  
  var plugins = ['themes', 'json_data', 'types', 'ui', 'cookies'];
  if (id == 'tree') {
    plugins.push('dnd');
  }

  
  $("#" + id).jstree({ 
 
    
    'plugins' : plugins,
    'json_data' : { 
      'ajax' : {
        'url' : postURL,
        'data' : function (n) { 
          return  { 
            'action' : 'getChildren', 
            'id' : n.attr ? n.attr('id') : '',
            'type' : n.attr ? n.attr('rel') : '',
            'zoneName' : n.attr ? n.attr('zoneName') : '',
            'languageId' : n.attr ? n.attr('languageId') : '',
            'websiteURL' : n.attr ? n.attr('websiteURL') : ''
          }; 
        }
      }
    },
    
    'types' : {
      // -2 do not need depth and children count checking
      'max_depth' : -2,
      'max_children' : -2,
      // This will prevent moving or creating any other type as a root node
      'valid_children' : [ 'website' ],
      'types' : {
        // The default type
        'page' : {
          'valid_children' : ['page'],
          'icon' : {
            'image' : image_dir + 'file.png'
          }
        },

        'zone' : {
          'valid_children' : [ 'page' ],
          'icon' : {
            'image' : image_dir + 'folder.png'
          },
          'start_drag' : false,
          'move_node' : false,
          'delete_node' : false,
          'remove' : false
        },

        'language' : {
          'valid_children' : [ 'zone' ],
          'icon' : {
            'image' : image_dir + 'folder.png'
          },
          'start_drag' : false,
          'move_node' : false,
          'delete_node' : false,
          'remove' : false
        },

        'website' : {
          'valid_children' : [ 'language' ],
          'icon' : {
            'image' : image_dir + 'root.png'
          },
          'start_drag' : false,
          'move_node' : false,
          'delete_node' : false,
          'remove' : false
        }
      
      }
      

    
    },
    
    'ui' : {
      'select_limit' : 1,
      'select_multiple_modifier' : 'alt',
      'selected_parent_close' : 'select_parent'
    },
    
    'cookies' : {
      'save_opened' : 'mod_menu_' + id + '_open',
      'save_selected' : (id == 'tree') ? 'mod_menu_' + id + '_selected' : ''
    }
    
  })
  
  

  
}


function openInternalLinkingTree () {
  initializeTreeManagement('tree_popup'); 
}










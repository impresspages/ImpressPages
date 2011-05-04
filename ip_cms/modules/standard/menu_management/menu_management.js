/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */


$(document).ready( function () {
  initializeTreeManagement('tree');  
  
  $('#tree').bind('select_node.jstree', updatePageForm);  

  $('#tree_popup').bind('select_node.jstree', treePopupSelect);  
  
  $('#controlls').delegate('#buttonNewPage', 'click', createPageForm)
  $('#controlls').delegate('#buttonDeletePage', 'click', deletePageConfirm)

} );



/**
 * Initialize tree management
 * @param id id of div where management should be initialized
 */
function initializeTreeManagement(id) {
  
  var plugins = ['themes', 'json_data', 'types', 'ui', 'cookies'];
  if (id == 'tree') {
    plugins.push('dnd');
    plugins.push('crrm');
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
            'image' : imageDir + 'file.png'
          }/*,
          'move_node' : function (obj){alert($(this).attr("id")); alert(obj.attr("id")); }*/
        },

        'zone' : {
          'valid_children' : [ 'page' ],
          'icon' : {
            'image' : imageDir + 'folder.png'
          },
          'start_drag' : false,
          'move_node' : false,
          'delete_node' : false,
          'remove' : false
        },

        'language' : {
          'valid_children' : [ 'zone' ],
          'icon' : {
            'image' : imageDir + 'folder.png'
          },
          'start_drag' : false,
          'move_node' : false,
          'delete_node' : false,
          'remove' : false
        },

        'website' : {
          'valid_children' : [ 'language' ],
          'icon' : {
            'image' : imageDir + 'root.png'
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
    },
    
    'dnd' : {
      'open_timeout' : 0
    }
    
  });
  
  if (id == 'tree') {
    $("#" + id).bind("move_node.jstree", movePage);
  }

  
}





/**
 * Open new page form
 */
function createPageForm () {
  var tree = jQuery.jstree._reference ( '#tree' );
  var node = tree.get_selected();
  if (!node || (node.attr('rel') != 'page' && node.attr('rel') != 'zone')) {
    alert('select page');
    return;
  }
  
  var data = Object();
  data.id = node.attr('id');
  data.zoneName = node.attr('zoneName');
  data.websiteURL = node.attr('websiteURL');
  data.languageId = node.attr('languageId');
  data.zoneName = node.attr('zoneName');
  data.type = node.attr('rel');
  data.action = 'getCreatePageForm';

  $.ajax({
    type: 'POST',
    url: postURL,
    data: data,
    success: createPageFormResponse,
    dataType: 'json'
  });      
}

/**
 * Response to open new page form request
 */
function createPageFormResponse (response) {
  if (response && response.html) {
    $('#page_properties').html(response.html);
    
    var tree = jQuery.jstree._reference ( '#tree' );
    
    //store pageId to know whish page data being edited
    tree.selectedPageId = response.page.id; 
    tree.selectedPageZoneName = response.page.zoneName;
    tree.selectedParentId = response.parent.id;
    

    $('#formGeneral input[name="buttonTitle"]').val(response.page.buttonTitle);
    $('#formGeneral input[name="visible"]').attr('checked', response.page.visible ? 1 : 0);
    $('#formGeneral input[name="createdOn"]').val(response.page.createdOn);
    $('#formGeneral input[name="lastModified"]').val(response.page.lastModified);

    $('#formSEO input[name="pageTitle"]').val(response.page.pageTitle);
    $('#formSEO textarea[name="keywords"]').val(response.page.keywords);
    $('#formSEO textarea[name="description"]').val(response.page.description);
    $('#formSEO input[name="url"]').val(response.page.url);
    $('#formAdvanced input[name="type"][name="type"][value="' + response.page.type + '"]').attr('checked', 1);
    $('#formAdvanced input[name="redirectURL"]').val(response.page.redirectURL);
    
    $("#page_properties form").bind("submit", function() { createPage(); return false; } );
    $("#internalLinkingIcon").bind("click", openInternalLinkingTree );
  }
}

/**
 * Post data to create a new page
 */
function createPage () {
  var tree = jQuery.jstree._reference ( '#tree' );
  
  data = Object();

  data.parentId = tree.selectedParentId;
  data.pageId = tree.selectedPageId; //we have stored this ID before
  data.zoneName = tree.selectedPageZoneName; //we have stored this ID before
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
  
  data.action = 'createPage';
  
  $.ajax({
    type: 'POST',
    url: postURL,
    data: data,
    success: createPageResponse,
    dataType: 'json'
  });    
}


/**
 * Create page post response
 * @param response
 */
function createPageResponse (response) {
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
    var url = window.location.href.split('#');
    window.location.href = url[0];
  }
}


/**
 * Delete page request confirm
 */
function deletePageConfirm () {
  var tree = jQuery.jstree._reference ( '#tree' );
  var node = tree.get_selected();

  
  if (!node || (node.attr('rel') != 'page')) {
    alert('select page');
    return;
  }
  
  if (confirm(deleteConfirmText)) {
    data = Object();

    var tree = jQuery.jstree._reference ( '#tree' );
    var node = tree.get_selected();
      
    data = Object();
    data.id = node.attr('id');
    data.action = 'deletePage';
    
    $.ajax({
      type: 'POST',
      url: postURL,
      data: data,
      success: deletePageResponse,
      dataType: 'json'
    });       
  }
}



/**
 * Delete page request
 */
function deletePageResponse (response) {
  if (response && response.status == 'success') {
    var url = window.location.href.split('#');
    window.location.href = url[0];    
  } else {
    alert('Unexpected error');    
  }
}



/**
 * Send request for page update form
 * @param event
 * @param data
 */
function updatePageForm(event, data) {
  var tree = jQuery.jstree._reference ( '#tree' );
  var node = tree.get_selected();
  if (node.attr('rel') == 'page') {
    
    var data = Object();
    data.id = node.attr('id');
    data.zoneName = node.attr('zoneName');
    data.websiteURL = node.attr('websiteURL');
    data.languageId = node.attr('languageId');
    data.type = node.attr('rel');
    data.action = 'getUpdatePageForm';
    
    
    $.ajax({
      type: 'POST',
      url: postURL,
      data: data,
      success: updatePageFormResponse,
      dataType: 'json'
    });    
  }
}

/**
 * Select node request response.
 * @param response
 */
function updatePageFormResponse (response) {
  if (response && response.html) {
    $('#page_properties').html(response.html);
    
    var tree = jQuery.jstree._reference ( '#tree' );
    
    //store pageId to know whish page data being edited
    tree.selectedPageId = response.page.id; 
    tree.selectedPageZoneName = response.page.zoneName; 

    $('#formGeneral input[name="buttonTitle"]').val(response.page.buttonTitle);
    $('#formGeneral input[name="visible"]').attr('checked', response.page.visible ? 1 : 0);
    $('#formGeneral input[name="createdOn"]').val(response.page.createdOn.substr(0, 10));
    $('#formGeneral input[name="lastModified"]').val(response.page.lastModified.substr(0, 10));

    $('#formSEO input[name="pageTitle"]').val(response.page.pageTitle);
    $('#formSEO textarea[name="keywords"]').val(response.page.keywords);
    $('#formSEO textarea[name="description"]').val(response.page.description);
    $('#formSEO input[name="url"]').val(response.page.url);
    $('#formAdvanced input[name="type"][name="type"][value="' + response.page.type + '"]').attr('checked', 1);
    $('#formAdvanced input[name="redirectURL"]').val(response.page.redirectURL);
    
    $("#page_properties form").bind("submit", function() { updatePage(); return false; } );
    $("#internalLinkingIcon").bind("click", openInternalLinkingTree );
  }
}


/**
 * Save selected and modified page
 */
function updatePage() {
  var tree = jQuery.jstree._reference ( '#tree' );
  
  data = Object();

  data.pageId = tree.selectedPageId; //we have stored this ID before
  data.zoneName = tree.selectedPageZoneName; //we have stored this ID before
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
    success: updatePageResponse,
    dataType: 'json'
  });    
  
}

/**
 * Save updated page response
 * @param response
 */
function updatePageResponse (response) {
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


/**
 * 
 * @param e
 * @param data
 */
function movePage(e, data) {
  data.rslt.o.each(function (i) {
    var data = Object(); 
    
    data.pageId = $(this).attr("id");
    data.zoneName = $(this).attr('zoneName');
    data.languageId = n$(this)ode.attr('languageId');
    data.websiteURL = $(this).attr('websiteURL');
    data.type = $(this).attr('rel');
    data.destinationPageId = data.rslt.np.attr("id");
    data.destinationPosition = data.rslt.cp + i;
    data.action = 'movePage';
    
    $.ajax({
      type: 'POST',
      url: postURL,
      data: data,
      success: movePageResponse,
      dataType: 'json'
    });   
  });
  
  
    
  //example:    
  //$.ajax({
  //  async : false,
  //  type: 'POST',
  //  url: "/static/v.1.0rc2/_demo/server.php",
  //  data : { 
  //    "operation" : "move_node", 
  //    "id" : $(this).attr("id").replace("node_",""), 
  //    "ref" : data.rslt.np.attr("id").replace("node_",""), 
  //    "position" : data.rslt.cp + i,
  //    "title" : data.rslt.name,
  //    "copy" : data.rslt.cy ? 1 : 0
  //  },
  //  success : function (r) {
  //    if(!r.status) {
  //      $.jstree.rollback(data.rlbk);
  //    }
  //    else {
  //      $(data.rslt.oc).attr("id", "node_" + r.id);
  //      if(data.rslt.cy && $(data.rslt.oc).children("UL").length) {
  //        data.inst.refresh(data.inst._get_parent(data.rslt.oc));
  //      }
  //    }
  //    $("#analyze").click();
  //  }
  //});  
  
};



function movePageResponse (response) {
  if(response && response.status == 'success') {
    var url = window.location.href.split('#');
    window.location.href = url[0];
  }
}

/**
 * Select page on internal linking popup
 * @param event
 * @param data
 */
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
    success: treePopupSelectResponse,
    dataType: 'json'
  });    
}


/**
 * Select page on internal linking popup response
 * @param response
 */
function treePopupSelectResponse (response) {  
  if (response && response.link) {
    $('#formAdvanced input[name="redirectURL"]').val(response.link);
    $('#formAdvanced input[name="type"][value="redirect"]').attr("checked","checked");
  }
}




function openInternalLinkingTree () {
  initializeTreeManagement('tree_popup'); 
}










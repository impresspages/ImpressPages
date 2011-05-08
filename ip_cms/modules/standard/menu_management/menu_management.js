/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

$(document).ready(function() {

  
  $('#sideBar').resizable({ alsoResize: '#tree' });
  $('#sideBar').bind('resize', fixLayout);
  
  
  $(window).bind('resize', fixLayout);

  
  initializeTreeManagement('tree');
  
  $('#tree').bind('select_node.jstree', updatePageForm);

  $('#treeopup').bind('select_node.jstree', treePopupSelect);

  $('#controlls').delegate('#buttonNewPage', 'click', createPageForm);
  $('#controlls').delegate('#buttonDeletePage', 'click', deletePageConfirm);
  $('#controlls').delegate('#buttonCopyPage', 'click', copyPage);
  $('#controlls').delegate('#buttonPastePage', 'click', pastePage);

  
  
  fixLayout();
  
});

/**
 * Initialize tree management
 * 
 * @param id
 *          id of div where management should be initialized
 */
function initializeTreeManagement(id) {

  var plugins = [ 'themes', 'json_data', 'types', 'ui', 'cookies' ];
  if (id == 'tree') {
    plugins.push('dnd');
    plugins.push('crrm');
    plugins.push('contextmenu');
  }

  $("#" + id).jstree({

    'plugins' : plugins,
    'json_data' : {
      'ajax' : {
        'url' : postURL,
        'data' : function(n) {
          return {
            'action' : 'getChildren',
            'id' : n.attr ? n.attr('id') : '',
            'pageId' : n.attr ? n.attr('pageId') : '',
            'type' : n.attr ? n.attr('rel') : '',
            'zoneName' : n.attr ? n.attr('zoneName') : '',
            'languageId' : n.attr ? n.attr('languageId') : '',
            'websiteId' : n.attr ? n.attr('websiteId') : ''
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
          'valid_children' : [ 'page' ],
          'icon' : {
            'image' : imageDir + 'file.png'
          }
        /*
         * , 'move_node' : function (obj){alert($(this).attr("id"));
         * alert(obj.attr("id")); }
         */
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
    },

    'contextmenu' : {
      'items' : {

      }
    }

  });

  if (id == 'tree') {
    $("#" + id).bind("move_node.jstree", movePage);
  }

}

/**
 * Open new page form
 */
function createPageForm() {
  var tree = jQuery.jstree._reference('#tree');
  var node = tree.get_selected();
  if (!node || (node.attr('rel') != 'page' && node.attr('rel') != 'zone')) {
    alert('select page');
    return;
  }

  var data = Object();
  data.id = node.attr('id');
  data.pageId = node.attr('pageId');
  data.zoneName = node.attr('zoneName');
  data.websiteId = node.attr('websiteId');
  data.languageId = node.attr('languageId');
  data.type = node.attr('rel');
  data.action = 'getCreatePageForm';

  $.ajax({
    type : 'POST',
    url : postURL,
    data : data,
    success : createPageFormResponse,
    dataType : 'json'
  });
}

/**
 * Response to open new page form request
 */
function createPageFormResponse(response) {
  if (response && response.html) {
    $('#pageroperties').html(response.html);

    var tree = jQuery.jstree._reference('#tree');

    // store pageId to know whish page data being edited
    tree.selectedPageId = response.page.pageId;
    tree.selectedPageZoneName = response.page.zoneName;
    tree.selectedParentId = response.parent.id;

    $('#formGeneral input[name="buttonTitle"]').val(response.page.buttonTitle);
    $('#formGeneral input[name="visible"]').attr('checked',
        response.page.visible ? 1 : 0);
    $('#formGeneral input[name="createdOn"]').val(response.page.createdOn);
    $('#formGeneral input[name="lastModified"]')
        .val(response.page.lastModified);

    $('#formSEO input[name="pageTitle"]').val(response.page.pageTitle);
    $('#formSEO textarea[name="keywords"]').val(response.page.keywords);
    $('#formSEO textarea[name="description"]').val(response.page.description);
    $('#formSEO input[name="url"]').val(response.page.url);
    $(
        '#formAdvanced input[name="type"][name="type"][value="'
            + response.page.type + '"]').attr('checked', 1);
    $('#formAdvanced input[name="redirectURL"]').val(response.page.redirectURL);

    $("#pageProperties form").bind("submit", function() {
      createPage();
      return false;
    });
    $("#internalLinkingIcon").bind("click", openInternalLinkingTree);
  }
}

/**
 * Post data to create a new page
 */
function createPage() {
  var tree = jQuery.jstree._reference('#tree');

  data = Object();

  data.parentId = tree.selectedParentId;
  data.pageId = tree.selectedPageId; // we have stored this ID before
  data.zoneName = tree.selectedPageZoneName; // we have stored this ID before
  data.buttonTitle = $('#formGeneral input[name="buttonTitle"]').val();
  data.visible = $('#formGeneral input[name="visible"]').attr('checked') ? 1
      : 0;
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
    type : 'POST',
    url : postURL,
    data : data,
    success : createPageResponse,
    dataType : 'json'
  });
}

/**
 * Create page post response
 * 
 * @param response
 */
function createPageResponse(response) {
  if (!response) {
    return;
  }

  $('#pageProperties .error').hide();

  if (response.errors) {
    for ( var errorKey in response.errors) {
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
function deletePageConfirm() {
  var tree = jQuery.jstree._reference('#tree');
  var node = tree.get_selected();

  if (!node || (node.attr('rel') != 'page')) {
    alert('select page');
    return;
  }

  if (confirm(deleteConfirmText)) {
    data = Object();

    var tree = jQuery.jstree._reference('#tree');
    var node = tree.get_selected();

    data = Object();
    data.id = node.attr('id');
    data.pageId = node.attr('pageId');
    data.zoneName = node.attr('zoneName');
    data.websiteId = node.attr('websiteId');
    data.languageId = node.attr('languageId');
    data.type = node.attr('rel');
    data.action = 'deletePage';

    $.ajax({
      type : 'POST',
      url : postURL,
      data : data,
      success : deletePageResponse,
      dataType : 'json'
    });
  }
}

/**
 * Delete page request
 */
function deletePageResponse(response) {
  if (response && response.status == 'success') {
    var tree = jQuery.jstree._reference('#tree');
    var selectedNode = tree.get_selected()
    var path = tree.get_path(selectedNode, true);

    tree.refresh('#' + path[path.length - 2]);
  } else {
    alert('Unexpected error');
  }
}

/**
 * Send request for page update form
 * 
 * @param event
 * @param data
 */
function updatePageForm(event, data) {
  var tree = jQuery.jstree._reference('#tree');
  var node = tree.get_selected();


  if (node.attr('rel') == 'page') {

    var data = Object();
    data.id = node.attr('id');
    data.pageId = node.attr('pageId');
    data.zoneName = node.attr('zoneName');
    data.websiteId = node.attr('websiteId');
    data.languageId = node.attr('languageId');
    data.type = node.attr('rel');
    data.action = 'getUpdatePageForm';

    $.ajax({
      type : 'POST',
      url : postURL,
      data : data,
      success : updatePageFormResponse,
      dataType : 'json'
    });
  } else {
    $('#pageProperties').html('');
  }
}

/**
 * Select node request response.
 * 
 * @param response
 */
function updatePageFormResponse(response) {
  if (response && response.html) {
    $('#pageProperties').html(response.html);

    var tree = jQuery.jstree._reference('#tree');

    // store pageId to know whish page data being edited
    tree.selectedPageId = response.page.pageId;
    tree.selectedPageZoneName = response.page.zoneName;

    $('#formGeneral input[name="buttonTitle"]').val(response.page.buttonTitle);
    $('#formGeneral input[name="visible"]').attr('checked',
        response.page.visible ? 1 : 0);
    $('#formGeneral input[name="createdOn"]').val(
        response.page.createdOn.substr(0, 10));
    $('#formGeneral input[name="lastModified"]').val(
        response.page.lastModified.substr(0, 10));

    $('#formSEO input[name="pageTitle"]').val(response.page.pageTitle);
    $('#formSEO textarea[name="keywords"]').val(response.page.keywords);
    $('#formSEO textarea[name="description"]').val(response.page.description);
    $('#formSEO input[name="url"]').val(response.page.url);
    $(
        '#formAdvanced input[name="type"][name="type"][value="'
            + response.page.type + '"]').attr('checked', 1);
    $('#formAdvanced input[name="redirectURL"]').val(response.page.redirectURL);

    $("#pageProperties form").bind("submit", function() {
      updatePage();
      return false;
    });
    $("#internalLinkingIcon").bind("click", openInternalLinkingTree);
    
    $('#pageProperties').tabs('destroy');
    $('#pageProperties').tabs();
    
    
  }
}

/**
 * Save selected and modified page
 */
function updatePage() {
  var tree = jQuery.jstree._reference('#tree');

  data = Object();

  data.pageId = tree.selectedPageId; // we have stored this ID before
  data.zoneName = tree.selectedPageZoneName; // we have stored this ID before
  data.buttonTitle = $('#formGeneral input[name="buttonTitle"]').val();
  data.visible = $('#formGeneral input[name="visible"]').attr('checked') ? 1
      : 0;
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
    type : 'POST',
    url : postURL,
    data : data,
    success : updatePageResponse,
    dataType : 'json'
  });

}

/**
 * Save updated page response
 * 
 * @param response
 */
function updatePageResponse(response) {
  if (!response) {
    return;
  }

  $('#pageProperties .error').hide();
  if (response.status == 'success') {
    var tree = jQuery.jstree._reference('#tree');
    var selectedNode = tree.get_selected()
    var path = tree.get_path(selectedNode, true);

    tree.refresh('#' + path[path.length - 2]);
  } else {
    if (response.errors) {
      for ( var errorKey in response.errors) {
        var error = response.errors[errorKey];
        $('#' + error.field + 'Error').text(error.message).show();
      }
    }
  }

  //  
  // if($errorCreatedOn)
  // $answer .=
  // 'document.getElementById(\'property_created_on_error\').style.display =
  // \'block\';';
  //
  // if($errorLastModified)
  // $answer .=
  // 'document.getElementById(\'property_last_modified_error\').style.display =
  // \'block\';';
  //
  // if($errorEmptyRedirectUrl)
  // $answer .= 'document.getElementById(\'property_type_error\').style.display
  // = \'block\';';
  //
  // $answer .= 'document.getElementById(\'loading\').style.display =
  // \'none\';';

  // if (!$_POST['visible']) {
  // $icon = 'node.ui.addClass(\'x-tree-node-disabled \');';
  // } else {
  // $icon = '';
  // }
  //
  // echo '
  // document.getElementById(\'loading\').style.display = \'none\';
  //   
  // document.getElementById(\'property_last_modified_error\').style.display =
  // \'none\';
  // document.getElementById(\'property_created_on_error\').style.display =
  // \'none\';
  // document.getElementById(\'property_type_error\').style.display = \'none\';
  //   
  //   
  // var form = document.getElementById(\'property_form\');
  // form.property_url.value =
  // \''.\Library\Php\Js\Functions::htmlToString($_POST['url']).'\';
  //   
  //   
  // var node = iTree.getTree().getSelectionModel().getSelectedNode();
  // node.setText(\''.\Library\Php\Js\Functions::htmlToString($_POST['buttonTitle']).'\');
  // node.ui.removeClass(\'x-tree-node-disabled\');
  // '.$icon.'
  // ';

}

/**
 * 
 * @param e
 * @param data
 */
function movePage(e, moveData) {
  moveData.rslt.o.each(function(i) {
    var data = Object();

    data.pageId = $(this).attr("pageId");
    data.zoneName = $(this).attr('zoneName');
    data.languageId = $(this).attr('languageId');
    data.websiteId = $(this).attr('websiteId');
    data.type = $(this).attr('rel');
    data.destinationPageId = moveData.rslt.np.attr("pageId");
    data.destinationId = moveData.rslt.np.attr("id");
    data.destinationPosition = moveData.rslt.cp + i;
    data.action = 'movePage';
    
    var tree = jQuery.jstree._reference('#tree');
    tree.destinationId = moveData.rslt.np.attr("id");

    $.ajax({
      type : 'POST',
      url : postURL,
      data : data,
      success : movePageResponse,
      dataType : 'json'
    });
  });

  // example:
  // $.ajax({
  // async : false,
  // type: 'POST',
  // url: "/static/v.1.0rc2/_demo/server.php",
  // data : {
  // "operation" : "move_node",
  // "id" : $(this).attr("id").replace("node_",""),
  // "ref" : data.rslt.np.attr("id").replace("node_",""),
  // "position" : data.rslt.cp + i,
  // "title" : data.rslt.name,
  // "copy" : data.rslt.cy ? 1 : 0
  // },
  // success : function (r) {
  // if(!r.status) {
  // $.jstree.rollback(data.rlbk);
  // }
  // else {
  // $(data.rslt.oc).attr("id", "node_" + r.id);
  // if(data.rslt.cy && $(data.rslt.oc).children("UL").length) {
  // data.inst.refresh(data.inst._get_parent(data.rslt.oc));
  // }
  // }
  // $("#analyze").click();
  // }
  // });

};

function movePageResponse(response) {
  if (response && response.status == 'success') {
    var tree = jQuery.jstree._reference('#tree');
    tree.refresh('#' + tree.destinationId);
  }
}

/**
 * Mark current page as copied
 */
function copyPage () {
  var tree = jQuery.jstree._reference('#tree');
  tree.copiedNode = tree.get_selected();  
}

/**
 * Duplicate and move the page, selected as copied
 */
function pastePage () {
  var tree = jQuery.jstree._reference('#tree');
  var selectedNode = tree.get_selected();  
  if (!selectedNode || selectedNode.attr('rel') != 'zone' && selectedNode.attr('rel') != 'page') {
    alert('Please select the page');
    return;
  }  
  
  var copiedNode = tree.copiedNode;
  
  var data = Object(); 
  
  data.pageId = copiedNode.attr('pageId');
  data.zoneName = copiedNode.attr('zoneName');
  data.languageId = copiedNode.attr('languageId');
  data.websiteId = copiedNode.attr('websiteId');
  data.type = copiedNode.attr('rel');
  data.destinationPageId = selectedNode.attr("pageId");
  data.action = 'copyPage';

  tree.destinationId = selectedNode.attr('id'); 
  
  $.ajax({
    type : 'POST',
    url : postURL,
    data : data,
    success : pastePageResponse,
    dataType : 'json'
  });  
  
  

}


function pastePageResponse (response) {
  if (response && response.status == 'success') {
    var tree = jQuery.jstree._reference('#tree');    
    tree.refresh('#' + tree.destinationId);
    //known bug. If page is pasted into empty folder, it can't be opened automatically, because refresh function does not provide a call back after refresh.
  }  
}


/**
 * Select page on internal linking popup
 * 
 * @param event
 * @param data
 */
function treePopupSelect(event, data) {
  var tree = jQuery.jstree._reference('#treePopup');
  var node = tree.get_selected();

  data = Object();
  data.id = node.attr('id');
  data.pageId = node.attr('pageId');
  data.zoneName = node.attr('zoneName');
  data.websiteId = node.attr('websiteId');
  data.languageId = node.attr('languageId');
  data.zoneName = node.attr('zoneName');
  data.type = node.attr('rel');
  data.action = 'getPageLink';

  $.ajax({
    type : 'POST',
    url : postURL,
    data : data,
    success : treePopupSelectResponse,
    dataType : 'json'
  });
}

/**
 * Select page on internal linking popup response
 * 
 * @param response
 */
function treePopupSelectResponse(response) {
  if (response && response.link) {
    $('#formAdvanced input[name="redirectURL"]').val(response.link);
    $('#formAdvanced input[name="type"][value="redirect"]').attr("checked",
        "checked");
  }
  
  closeInternalLinkingTree();
}

function openInternalLinkingTree() {
  var tree = jQuery.jstree._reference('#treePopup');
  if (!tree) {
    initializeTreeManagement('treePopup');
  }
  $('#treePopup').dialog({ autoOpen: true, modal: true })
  $('.ui-widget-overlay').bind('click', closeInternalLinkingTree )
  
}

function closeInternalLinkingTree() {
  $('.ui-widget-overlay').unbind('click');
  $('#treePopup').dialog('close');
}


function fixLayout () {
  $('#pageProperties').width($(window).width() - $('#sideBar').width() - 30);
  $('#pageProperties').height($(window).height() - 25);
  $('#tree').height($(window).height() - 21 - 81);
  $('#sideBar').height($(window).height() - 21);
  $('#sideBar').resizable('option', 'maxHeight', $(window).height() - 25);
  $('#sideBar').resizable('option', 'minHeight', $(window).height() - 25);
  $('#sideBar').resizable('option', 'minWidth', 250);
  $('#sideBar').resizable('option', 'maxWidth', 1600);
}
/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

$(document).ready(function() {

  $('#sideBar').resizable({
    alsoResize : '#tree'
  });
  $('#sideBar').bind('resize', fixLayout);

  $(window).bind('resize', fixLayout);

  initializeTreeManagement('tree');

  $('#tree').bind('select_node.jstree', updatePageForm);
  $('#tree').bind('close_node.jstree', closeNode);

  $('#controlls').delegate('#buttonNewPage', 'click', createPageForm);
  $('#controlls').delegate('#buttonDeletePage', 'click', deletePageConfirm);
  $('#controlls').delegate('#buttonCopyPage', 'click', copyPage);
  $('#controlls').delegate('#buttonPastePage', 'click', pastePage);

  $('#tree').width($('#sideBar').width() - 43);

  $('#formCreatePage').bind('submit', function () { createPage (); return false;} );

  
  fixLayout();

});

/**
 * Initialize tree management
 * 
 * @param id
 *            id of div where management should be initialized
 */
function initializeTreeManagement(id) {

  var plugins = [ 'themes', 'json_data', 'types', 'ui'];
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
      'selected_parent_close' : 'select_parent',
      'select_prev_on_delete' : true
    },

    'cookies' : {
      'save_opened' : 'mod_menu_' + id + '_open',
      'save_selected' : (id == 'tree') ? 'mod_menu_' + id + '_selected' : ''
    },

    'dnd' : {
      'open_timeout' : 0
    },

    'contextmenu' : {
      'show_at_node' : false,
      'select_node' : true,
      'items' : jsTreeCustomMenu
    }
    
    
  });

  if (id == 'tree') {
    $("#" + id).bind("move_node.jstree", movePage);
  }
  
  if (id == 'treePopup') {
      $('#treePopup').bind('select_node.jstree', treePopupSelect);
  }

}


/**
 * geneate context menu
 * @param node selected menu item
 * @returns array context menu items
 */
function jsTreeCustomMenu(node) {
    items = {
        "rename" : false,
        "create" : false,
        "remove" : false,
        "ccp" : false
    }
    

    
    if ($(node).attr('rel') == 'page' && $(node).attr('websiteId') == 0) {
        items.edit = {
            "label"             : textEdit,
            "action"            : function (obj) { editPage(); },
            "_class"            : "class",  // class is applied to the item LI node
            "icon"              : false
        };
    };
     
    
    if (($(node).attr('rel') == 'page' || $(node).attr('rel') == 'zone') && $(node).attr('websiteId') == 0) {
        items.newPage = {
            "label"             : textNewPage,
            "action"            : function (obj) { createPageForm(); },
            "_class"            : "class",  // class is applied to the item LI node
            "icon"              : false
        };
    };
    
    if ($(node).attr('rel') == 'page') {
        items.copy = {
            "label"             : textCopy,
            "action"            : function (obj) { copyPage(); },
            "_class"            : "class",  // class is applied to the item LI node
            "icon"              : false
        };
    };
     
    
    var tree = jQuery.jstree._reference('#tree');
    if (($(node).attr('rel') == 'page' || $(node).attr('rel') == 'zone') && tree.copiedNode && $(node).attr('websiteId') == 0) {
        items.paste = {
            "label"             : textPaste,
            "action"            : function (obj) { pastePage(); },
            "_class"            : "class",  // class is applied to the item LI node
            "icon"              : false
        };
    };
         
    
    if ($(node).attr('rel') == 'page' && $(node).attr('websiteId') == 0) {
        items.del = {
            "label"             : textDelete,
            "action"            : function (obj) { deletePageConfirm(); },
            "_class"            : "class",  // class is applied to the item LI node
            "icon"              : false
        };    
    };


    return items;

}


function editPage () {
    var tree = jQuery.jstree._reference('#tree');
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
      success : editPageResponse,
      dataType : 'json'
    });
}

function editPageResponse (response) {
    if (!response || !response.link) {
        return;
    }
    
    document.location = response.link + '?cms_action=manage';
    
}

function closeNode (event, data) {
    
    node = $(data.rslt.obj[0]);
    var data = new Object; 


    data.languageId = node.attr('languageId');
    data.rel = node.attr('rel');
    data.pageId = node.attr('pageId');
    data.type = node.attr('rel');
    data.zoneName = node.attr('zoneName');
    data.languageId = node.attr('languageId');
    data.websiteId = node.attr('websiteId');   
        
    data.action = 'closePage';
    
    $.ajax({
      type : 'POST',
      url : postURL,
      data : data,
      dataType : 'json'
    });    
}

/**
 * Open new page form
 */
function createPageForm() {
  
    var node = treeSelectedNode('#tree');

  
    var buttons = new Array;
      
    buttons.push({ text : textSave, click : createPage});
    buttons.push({ text : textCancel, click : function () {$(this).dialog("close")} });
    
      
    $('#createPageForm').dialog({
        autoOpen : true,
        modal : true,
        resizable : false,
        buttons : buttons
    });
    
    return;

}



/**
 * Post data to create a new page
 */
function createPage() {

  $('#createPageForm').dialog('close');
  
  
  data = Object();
  
  var node = treeSelectedNode('#tree');


  if (node) {
    data.languageId = node.attr('languageId');
    data.rel = node.attr('rel');
    data.pageId = node.attr('pageId');
    data.type = node.attr('rel');
    data.zoneName = node.attr('zoneName');
    data.languageId = node.attr('languageId');
    data.websiteId = node.attr('websiteId');   
  }    
  data.buttonTitle = $('#createPageButtonTitle').val();

  
  $('#createPageForm input').val(''); //remove value from input field
  
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
  
  if (response.refreshId) {
    var tree = jQuery.jstree._reference('#tree');
    tree.refresh('#' + response.refreshId);
  }
}

/**
 * Delete page request confirm
 */
function deletePageConfirm() {
  var tree = jQuery.jstree._reference('#tree');
  var node = tree.get_selected();

  if (!node || (node.attr('rel') != 'page')) {
    return;
  }

  if (confirm(deleteConfirmText)) {

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
    var selectedNode = tree.get_selected();
    tree.deselect_all(); //without it get_selected returns the same deleted page

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

  switch (node.attr('rel')) {
    case 'page':
        $('#buttonDeletePage').removeClass('ui-state-disabled');
        $('#buttonCopyPage').removeClass('ui-state-disabled');
        
        if (tree.copiedNode) {
            $('#buttonPastePage').removeClass('ui-state-disabled');
        } else {
            $('#buttonPastePage').addClass('ui-state-disabled');
        }
      break;
    case 'website':
    case 'language':
        $('#buttonDeletePage').addClass('ui-state-disabled');
        $('#buttonCopyPage').addClass('ui-state-disabled');
        $('#buttonPastePage').addClass('ui-state-disabled');
      break;
    case 'zone':
        $('#buttonDeletePage').addClass('ui-state-disabled');
        $('#buttonCopyPage').addClass('ui-state-disabled');
        if (tree.copiedNode) {
            $('#buttonPastePage').removeClass('ui-state-disabled');
        } else {
            $('#buttonPastePage').addClass('ui-state-disabled');
        }
      break;  
  }
  
  if (node.attr('websiteId') != 0) {
      $('#buttonNewPage').addClass('ui-state-disabled');
      $('#buttonDeletePage').addClass('ui-state-disabled');
      $('#buttonPastePage').addClass('ui-state-disabled');
      $('#pageProperties').html('');
      return;
  }
  
  
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
        $('#' + error.field + 'Error').text(error.message).css('display',
            'block');
      }
    }
  }

  // if (!$_POST['visible']) {
  // $icon = 'node.ui.addClass(\'x-tree-node-disabled \');';
  // } else {
  // $icon = '';
  // }
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
function copyPage() {
    
  var tree = jQuery.jstree._reference('#tree');
  var node = tree.get_selected();
  
  if (!node || (node.attr('rel') != 'page')) {
      return;
  }    
  
  tree.copiedNode = node; 
  $('#buttonPastePage').removeClass('ui-state-disabled');
}

/**
 * Duplicate and move the page, selected as copied
 */
function pastePage() {
  var tree = jQuery.jstree._reference('#tree');
  var selectedNode = tree.get_selected();
  if (!tree.copiedNode || !selectedNode || selectedNode.attr('rel') != 'zone' && selectedNode.attr('rel') != 'page') {
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

function pastePageResponse(response) {
  if (response && response.status == 'success') {
    var tree = jQuery.jstree._reference('#tree');
    tree.refresh('#' + tree.destinationId);
    // known bug. If page is pasted into empty folder, it can't be opened
    // automatically, because refresh function does not provide a call back
    // after refresh.
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
  $('#treePopup').dialog({
    autoOpen : true,
    modal : true,
    height : ($(window).height() - 200),
    width : 300
  })
  $('.ui-widget-overlay').bind('click', closeInternalLinkingTree)

}

function closeInternalLinkingTree() {
  $('.ui-widget-overlay').unbind('click');
  $('#treePopup').dialog('close');
}

/**
 * Custom function to overcome some jsTree bug.
 * @param treeId
 * @returns
 */
function treeSelectedNode(treeId) {
    var tree = jQuery.jstree._reference(treeId);
    var node = tree.get_selected();    
    if (node.attr('id'))  {
        return node;
    } else {
        return false;
    }
}

function fixLayout() {
  $('#pageProperties').width($(window).width() - $('#sideBar').width() - 30);
  $('#pageProperties').height($(window).height() - 25);
  $('#tree').height($(window).height() - 21 - 81);
  $('#sideBar').height($(window).height() - 21);
  $('#sideBar').resizable('option', 'maxHeight', $(window).height() - 25);
  $('#sideBar').resizable('option', 'minHeight', $(window).height() - 25);
  $('#sideBar').resizable('option', 'minWidth', 354);
  $('#sideBar').resizable('option', 'maxWidth', 1600);
}
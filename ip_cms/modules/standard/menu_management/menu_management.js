/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */


$(document).ready( function () {
  initializeTreeManagement();  
} );


function initializeTreeManagement() {
  $("#mod_menu_management_tree")
  .jstree({ 
    // the list of plugins to include
    "plugins" : ["themes", "json_data"],
    // Plugin configuration

    // I usually configure the plugin that handles the data first - in this case JSON as it is most common
    "json_data" : { 
      // I chose an ajax enabled tree - again - as this is most common, and maybe a bit more complex
      // All the options are the same as jQuery's except for `data` which CAN (not should) be a function
      "ajax" : {
        // the URL to fetch the data
        "url" : postURL,
        // this function is executed in the instance's scope (this refers to the tree instance)
        // the parameter is the node being loaded (may be -1, 0, or undefined when loading the root nodes)
        "data" : function (n) { 
          // the result is fed to the AJAX request `data` option
          return { 
            "operation" : "get_children", 
            "id" : n.attr ? n.attr("id") : null,
          }; 
        }
      }
    },
  })  
}
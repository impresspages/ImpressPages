/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

Ext.BLANK_IMAGE_URL = 'ip_libs/js/ext/resources/images/aero/s.gif';


iTree = function(){
    var root, tree;  //this part is grabbed from the web. And the variable "tree" seems to bee unused
    var depth; //custom variable. Determines maximal depth of tree;
    return {
        init : function(){
            // yui-ext tree
            var Tree = Ext.tree;    
            tree = new Tree.TreePanel('tree-div', {
                animate:true, 
                loader: new Tree.TreeLoader(),
                enableDD:true,
                containerScroll: true,
                rootVisible: false
            }); 
            // set the root node
 
          
            
            var root = new Tree.AsyncTreeNode({
                text: ''+current_menu_title,
                draggable:false,
                id:''+root_id,
                children: json
            });



            
            tree.setRootNode(root);

            // render the tree
            tree.render();

            tree.on('contextmenu', this.menuShow, this);
            tree.on('click', this.selectNode, this);
            root.expand();
            tree.on("beforemove",function (tree, node, oldParent, newParent, index){

 
              var worker_form = document.getElementById("worker_form");
              worker_form.innerHTML = ''+
              '<input type="hidden" name="action" value="menu_move" />'+
              '<input type="hidden" name="zone_name" value="'+ zoneName +'" />'+
              '<input type="hidden" name="node" value="'+ node.id +'" />'+
              '<input type="hidden" name="new_index" value="'+ index +'" />'+
              '<input type="hidden" name="old_index" value="'+ oldParent.indexOf(node) +'" />'+
              '<input type="hidden" name="old_parent" value="'+ oldParent.id +'" />'+
              '<input type="hidden" name="new_parent" value="'+ newParent.id +'" />';
              worker_form.submit();
  
          })
          
    
    
          
        },
        
        
        getTree : function(){
          return tree;
        },
        
        publishNode : function(arg){            
            // This works
            //tree.root.select();
            
            // This doesn't work
            //var r = tree.getRoot();
            var r = tree.getRootNode();
            //r.select();
            
            //This doesn't work either
            //alert (tree.root.isRoot);
            
            // What I really need is is the selected node...
        },
        
        
        deleteNode : function(arg){            
           var node;
           node = tree.getSelectionModel().getSelectedNode();
           
           var parent;
           parent = node.parentNode;
           if (confirm(translation_are_you_sure_you_wish_to_delete)){
             var worker_form = document.getElementById("worker_form");
              worker_form.innerHTML = ''+
              '<input type="hidden" name="action" value="menu_delete" />'+
              '<input type="hidden" name="node" value="'+ node.id +'" />'+
              '<input type="hidden" name="index" value="'+ parent.indexOf(node) +'" />'+
              '<input type="hidden" name="parent" value="'+ parent.id +'" />';
              worker_form.submit();
							parent.removeChild(node);

              document.getElementById('content').style.display = 'none';
           }
        },        

				renameNode : function(arg){
          Ext.MessageBox.prompt(translation_title, translation_please_enter_title, renameNodePromted);
				},
				
        newNode : function(arg){
          //iTree.getTree().root.select();
          iTree.newSubNode();
          //Ext.MessageBox.prompt(translation_title, translation_please_enter_title, newNodePromted);
          //var new_title = prompt(translation_please_enter_title, '');
        },        
        

        selectNode : function(arg){
          var node;
          node = tree.getSelectionModel().getSelectedNode();
          LibDefault.ajaxMessage(cmsLink, 'action=get_page&id=' + node.id + '&zone_name=' + zoneName);
          document.getElementById('backgrace_path_update').style.display = 'block'; 
          document.getElementById('backgrace_path_new').style.display = 'none'; 
          
                    
        },        

    
        newSubNode : function(arg){                  
          //Ext.MessageBox.prompt(translation_title, translation_please_enter_title, newSubNodePromted);
          var node;
          node = tree.getSelectionModel().getSelectedNode();          
          
          var form = document.getElementById('property_form');
          form.action.value = 'new';
          form.property_id.value = node.id;
          form.property_button_title.value = '';
          form.property_page_title.value = '';
          form.property_keywords.value = '';
          form.property_description.value = '';
          form.property_url.value = '';
          form.property_redirect_url.value = '';
          
          document.getElementById('backgrace_path_update').style.display = 'none'; 
          document.getElementById('backgrace_path_new').style.display = 'block'; 
          
          if (current_menu_auto_rss) {
            form.property_rss.checked = true;
          } else {
            form.property_rss.checked = false;
          }

          if (current_menu_hide_new_pages) {
            form.property_visible.checked = false;
          } else {
            form.property_visible.checked = true;
          }


          
          var d = new Date();
          var year = d.getFullYear()
          var month = '' + (d.getMonth() + 1);          
          if(month.length == 1)
            month = '0' + month;
          var day = '' + d.getDate();
          if(day.length == 1)
            day = '0' + day;
            
          var dateStr = year + '-' + month + '-' + day;
          
          form.property_created_on.value = dateStr;
          form.property_last_modified.value = dateStr;
          document.getElementById('property_type_default').checked = true;
          document.getElementById('url_prefix').innerHTML = zoneLink;
          document.getElementById('url_suffix').innerHTML = '';      
          document.getElementById('content').style.display = 'block';
           
          //promt(translation_title, translation_please_enter_title, newSubNodePromted);
        },            
  
        hideNode : function(arg){                  
          node = tree.getSelectionModel().getSelectedNode();
          var worker_form = document.getElementById("worker_form");
          worker_form.innerHTML = ''+
          '<input type="hidden" name="action" value="menu_hide" />'+
          '<input type="hidden" name="node" value="'+ node.id +'" />';
          worker_form.submit();
            
          //node.disable();
			    node.ui.addClass('x-tree-node-disabled');

        },     

        showNode : function(arg){                  
          node = tree.getSelectionModel().getSelectedNode();          
          var worker_form = document.getElementById("worker_form");
          worker_form.innerHTML = ''+
          '<input type="hidden" name="action" value="menu_show" />'+
          '<input type="hidden" name="node" value="'+ node.id +'" />';
          worker_form.submit();

          node.enable();
          node.ui.removeClass('x-tree-node-disabled');

        },     
        
        editNode : function(arg){
           var node;
           node = tree.getSelectionModel().getSelectedNode();
          var worker_form = document.getElementById("worker_form");
          worker_form.innerHTML = ''+
          '<input type="hidden" name="action" value="manage_element" />'+
          '<input type="hidden" name="answer_function" value="manage_element" />'+          
          '<input type="hidden" name="node" value="'+ node.id +'" />';
          worker_form.submit();           
                      
          // document.location="?action=edit_element&id=" + node.id;
        },


	    onBeforeDrag : function(data, e){
	      var n = data.node;
				return true;
 	      return n && n.draggable;
 	    },
 	
 	    
 	    onEndDrag : function(data, e){
 	        this.tree.eventModel.enable.defer(100, this.tree.eventModel);
 	        this.tree.fireEvent("enddrag", this.tree, data.node, e);
 	    },		

        
        menuShow : function( node ){
            node.select();
            var menuC = new Ext.menu.Menu('mainContext');            
            
//            menuC.add(new Ext.menu.CheckItem({text: 'Publiceren',checkHandler:this.publishNode}));            
//            menuC.add(new Ext.menu.Item({text: translation_rename,handler:this.renameNode}));            
            menuC.add(new Ext.menu.Item({text: translation_edit_content,handler:this.editNode}));            
            menuC.add(new Ext.menu.Item({text: translation_new_page,handler:this.newNode}));            
           // menuC.add(new Ext.menu.Item({text: translation_new_sub_page,handler:this.newSubNode}));            
/*            if (!node.disabled)
             menuC.add(new Ext.menu.Item({text: translation_hide,handler:this.hideNode}));            
            else            
             menuC.add(new Ext.menu.Item({text: translation_show,handler:this.showNode}));
*/            
            menuC.add(new Ext.menu.Item({text: translation_delete,handler:this.deleteNode}));            
            menuC.show(node.ui.getAnchor());
        }
		
		
		
    };
}();


function renameNodePromted(btn, text){
  if(text != ""  && btn != 'cancel'){
     var node;
     node = iTree.getTree().getSelectionModel().getSelectedNode();
     var parent;
     parent = node.parentNode;
     
      var worker_form = document.getElementById("worker_form");
      worker_form.innerHTML = ''+
      '<input type="hidden" name="action" value="menu_rename_page" />'+
      '<input type="hidden" name="answer_function" value="tree_rename_node" />'+
      '<textarea name="title">' + text + '</textarea>'+
      '<input type="hidden" name="node" value="' + node.id + '" />';

      worker_form.submit();
   }        
}


   
   
function manage_element(notes, errors, variables){
//alert(variables[0]);
  document.location = variables[0];
}   

Ext.onReady(function(){    
    iTree.init();
//    iTree.depth = zone_depth;
});







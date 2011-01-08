/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2011 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

ModuleStandardMenuManagement = {



  save : function (){
    var form;
    form = document.getElementById('property_form');
    if (form.action.value == 'new') {
      document.getElementById('loading').style.display = 'block';
      var params = 'action=new_page&parent_id=' + encodeURI(form.property_id.value);
      params = params + '&zone_name=' + encodeURI(form.property_zone_name.value);
      params = params + '&button_title=' + encodeURI(form.property_button_title.value);
      params = params + '&page_title=' + encodeURI(form.property_page_title.value);
      params = params + '&keywords=' + encodeURI(form.property_keywords.value);
      params = params + '&description=' + encodeURI(form.property_description.value);
      params = params + '&url=' + encodeURI(form.property_url.value);
      params = params + '&created_on=' + encodeURI(form.property_created_on.value);
      params = params + '&last_modified=' + encodeURI(form.property_last_modified.value);
      
      if(form.property_rss.checked)
        params = params + '&rss=1';
      else
        params = params + '&rss=0';
      if(form.property_visible.checked)
        params = params + '&visible=1';
      else
        params = params + '&visible=0';

      var type = 'default';
      if(document.getElementById('property_type_inactive').checked)
        type = 'inactive';
      if(document.getElementById('property_type_subpage').checked)
        type = 'subpage';
      if(document.getElementById('property_type_redirect').checked)
        type = 'redirect';
      
      params = params + '&type=' + type;
      params = params + '&redirect_url=' + encodeURI(form.property_redirect_url.value);
      LibDefault.ajaxMessage(cmsLink, params); //cmsLink declared in manager.php
    }
    if (form.action.value == 'update') {
      document.getElementById('loading').style.display = 'block';
      var params = 'action=update_page&page_id=' + form.property_id.value;
      params = params + '&zone_name=' + encodeURI(form.property_zone_name.value);
      params = params + '&button_title=' + encodeURI(form.property_button_title.value);
      params = params + '&page_title=' + encodeURI(form.property_page_title.value);
      params = params + '&keywords=' + encodeURI(form.property_keywords.value);
      params = params + '&description=' + encodeURI(form.property_description.value);
      params = params + '&url=' + encodeURI(form.property_url.value);
      params = params + '&created_on=' + encodeURI(form.property_created_on.value);
      params = params + '&last_modified=' + encodeURI(form.property_last_modified.value);

      if(form.property_rss.checked)
        params = params + '&rss=1';
      else
        params = params + '&rss=0';
      if(form.property_visible.checked)
        params = params + '&visible=1';
      else
        params = params + '&visible=0';
      
      var type = 'default';
      if(document.getElementById('property_type_inactive').checked)
        type = 'inactive';
      if(document.getElementById('property_type_subpage').checked)
        type = 'subpage';
      if(document.getElementById('property_type_redirect').checked)
        type = 'redirect';
      
      params = params + '&type=' + type;
      params = params + '&redirect_url=' + encodeURI(form.property_redirect_url.value);
      LibDefault.ajaxMessage(cmsLink, params); //cmsLink declared in manager.php
    }
        
  },
  
  
  addNode : function (id, title, parentNodeId, visible) {
       var newNode;
       var parent = iTree.getTree().getNodeById(parentNodeId);
    
       var allowDrop;
       /*if(parent.getDepth() >= iTree.depth - 1)
        allowDrop = false;
       else
        allowDrop = true;*/
       allowDrop = true;  
      
      
        if(visible == '1')
          hidden = false;
        else
          hidden = true;
    
       newNode = new Ext.tree.TreeNode({
         "text" : title,
         "leaf" : false,
         "id" : id,
         "disabled" : hidden,
         "allowDrop" : allowDrop
         });
    
    
     
       //parent.appendChild(new_node);
       //parent.insertBefore(new_node, parent.firstChild);
       parent.appendChild(newNode);
       parent.leaf = false;
       parent.toggle();
       parent.expand();
       newNode.select();
       iTree.selectNode();
     // new_node.disable();
    
    
    
       //new_node.leaf = false;	  
  
  },
  
  setUrlSuffix : function (value) {
    var suffixSpan = document.getElementById('url_suffix');
    suffixSpan.innerHTML = value.replace("/", "-");
    document.getElementById('property_type_default').checked= true;
    var redirectInput = document.getElementById('property_type_redirect_input');

    if(redirectInput.value == 'http://')
    {
       redirectInput.value = '';
    }
  },

  updateIPlinks : function(content) {
    if (content.indexOf("ipSitemap")!=-1) {
      // ok
      var html = '';
      html += '<div>';
      html += content;
      html += '</div>';
      document.getElementById('ipbrowsercontainer').innerHTML = html;
      document.getElementById('ipbrowsercontainer').style.display = '';
      ModuleStandardMenuManagement.sitemapFunctions("ipSitemap");
      ModuleStandardMenuManagement.sitemapStyler("ipSitemap");
    } else {
      // rezultatas nedave listo
      document.getElementById('ipbrowsercontainer').innerHTML = 'Something bad happened.';
      alert(content);
    }
  },

  sitemapFunctions : function (objID) {
    var sitemap = document.getElementById(objID);
    if(sitemap){

      var anchors = sitemap.getElementsByTagName("a");
      for(var i=0;i<anchors.length;i++){
        anchors[i].onclick = function(){
          document.getElementById('property_type_redirect_input').value = this.href;
          document.getElementById('property_type_redirect').checked = true;

          return false;
        }
      }

    }
  },

  sitemapStyler : function(objID) {
    // Author: Alen Grakalic

    var sitemap = document.getElementById(objID);
    if(sitemap){

      this.listItem = function(li){
        if(li.getElementsByTagName("ul").length > 0){
          var ul = li.getElementsByTagName("ul")[0];
          ul.style.display = "none";
          var span = document.createElement("span");
          span.className = "collapsed";
          span.onclick = function(){
            ul.style.display = (ul.style.display == "none") ? "block" : "none";
            this.className = (ul.style.display == "none") ? "collapsed" : "expanded";
          }
          li.appendChild(span);
        }
      }

      var items = sitemap.getElementsByTagName("li");
      for(var i=0;i<items.length;i++){
        this.listItem(items[i]);
      }

    }
  },


  openInternalLinkingTree : function(){
     LibDefault.ajaxMessage(document.location, 'module_group=standard&module_name=content_management&action=sitemap_list&current_href=', ModuleStandardMenuManagement.updateIPlinks);
     document.getElementById('property_type_redirect').checked= true;
     var redirectInput = document.getElementById('property_type_redirect_input');
     if(redirectInput.value == '')
     {
        redirectInput.value = 'http://';
     }
  }

}
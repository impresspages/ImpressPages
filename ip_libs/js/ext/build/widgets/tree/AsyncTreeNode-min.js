/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.tree.AsyncTreeNode=function(_1){this.loaded=false;this.loading=false;Ext.tree.AsyncTreeNode.superclass.constructor.apply(this,arguments);this.addEvents({"beforeload":true,"load":true});};Ext.extend(Ext.tree.AsyncTreeNode,Ext.tree.TreeNode,{expand:function(_2,_3,_4){if(this.loading){var _5;var f=function(){if(!this.loading){clearInterval(_5);this.expand(_2,_3,_4);}}.createDelegate(this);_5=setInterval(f,200);return;}if(!this.loaded){if(this.fireEvent("beforeload",this)===false){return;}this.loading=true;this.ui.beforeLoad(this);var _7=this.loader||this.attributes.loader||this.getOwnerTree().getLoader();if(_7){_7.load(this,this.loadComplete.createDelegate(this,[_2,_3,_4]));return;}}Ext.tree.AsyncTreeNode.superclass.expand.call(this,_2,_3,_4);},isLoading:function(){return this.loading;},loadComplete:function(_8,_9,_a){this.loading=false;this.loaded=true;this.ui.afterLoad(this);this.fireEvent("load",this);this.expand(_8,_9,_a);},isLoaded:function(){return this.loaded;},hasChildNodes:function(){if(!this.isLeaf()&&!this.loaded){return true;}else{return Ext.tree.AsyncTreeNode.superclass.hasChildNodes.call(this);}},reload:function(_b){this.collapse(false,false);while(this.firstChild){this.removeChild(this.firstChild);}this.childrenRendered=false;this.loaded=false;if(this.isHiddenRoot()){this.expanded=false;}this.expand(false,false,_b);}});
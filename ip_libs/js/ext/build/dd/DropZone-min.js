/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.dd.DropZone=function(el,_2){Ext.dd.DropZone.superclass.constructor.call(this,el,_2);};Ext.extend(Ext.dd.DropZone,Ext.dd.DropTarget,{getTargetFromEvent:function(e){return Ext.dd.Registry.getTargetFromEvent(e);},onNodeEnter:function(n,dd,e,_7){},onNodeOver:function(n,dd,e,_b){return this.dropAllowed;},onNodeOut:function(n,dd,e,_f){},onNodeDrop:function(n,dd,e,_13){return false;},onContainerOver:function(dd,e,_16){return this.dropNotAllowed;},onContainerDrop:function(dd,e,_19){return false;},notifyEnter:function(dd,e,_1c){return this.dropNotAllowed;},notifyOver:function(dd,e,_1f){var n=this.getTargetFromEvent(e);if(!n){if(this.lastOverNode){this.onNodeOut(this.lastOverNode,dd,e,_1f);this.lastOverNode=null;}return this.onContainerOver(dd,e,_1f);}if(this.lastOverNode!=n){if(this.lastOverNode){this.onNodeOut(this.lastOverNode,dd,e,_1f);}this.onNodeEnter(n,dd,e,_1f);this.lastOverNode=n;}return this.onNodeOver(n,dd,e,_1f);},notifyOut:function(dd,e,_23){if(this.lastOverNode){this.onNodeOut(this.lastOverNode,dd,e,_23);this.lastOverNode=null;}},notifyDrop:function(dd,e,_26){if(this.lastOverNode){this.onNodeOut(this.lastOverNode,dd,e,_26);this.lastOverNode=null;}var n=this.getTargetFromEvent(e);return n?this.onNodeDrop(n,dd,e,_26):this.onContainerDrop(dd,e,_26);},triggerCacheRefresh:function(){Ext.dd.DDM.refreshCache(this.groups);}});
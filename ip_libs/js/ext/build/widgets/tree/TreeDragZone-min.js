/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


if(Ext.dd.DragZone){Ext.tree.TreeDragZone=function(_1,_2){Ext.tree.TreeDragZone.superclass.constructor.call(this,_1.getTreeEl(),_2);this.tree=_1;};Ext.extend(Ext.tree.TreeDragZone,Ext.dd.DragZone,{ddGroup:"TreeDD",onBeforeDrag:function(_3,e){var n=_3.node;return n&&n.draggable&&!n.disabled;},onInitDrag:function(e){var _7=this.dragData;this.tree.getSelectionModel().select(_7.node);this.proxy.update("");_7.node.ui.appendDDGhost(this.proxy.ghost.dom);this.tree.fireEvent("startdrag",this.tree,_7.node,e);},getRepairXY:function(e,_9){return _9.node.ui.getDDRepairXY();},onEndDrag:function(_a,e){this.tree.fireEvent("enddrag",this.tree,_a.node,e);},onValidDrop:function(dd,e,id){this.tree.fireEvent("dragdrop",this.tree,this.dragData.node,dd,e);this.hideProxy();},beforeInvalidDrop:function(e,id){var sm=this.tree.getSelectionModel();sm.clearSelections();sm.select(this.dragData.node);}});}
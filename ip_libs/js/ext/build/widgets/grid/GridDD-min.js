/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.grid.GridDragZone=function(_1,_2){this.view=_1.getView();Ext.grid.GridDragZone.superclass.constructor.call(this,this.view.mainBody.dom,_2);if(this.view.lockedBody){this.setHandleElId(Ext.id(this.view.mainBody.dom));this.setOuterHandleElId(Ext.id(this.view.lockedBody.dom));}this.scroll=false;this.grid=_1;this.ddel=document.createElement("div");this.ddel.className="x-grid-dd-wrap";};Ext.extend(Ext.grid.GridDragZone,Ext.dd.DragZone,{ddGroup:"GridDD",getDragData:function(e){var t=Ext.lib.Event.getTarget(e);var _5=this.view.findRowIndex(t);if(_5!==false){var sm=this.grid.selModel;if(!sm.isSelected(_5)||e.hasModifier()){sm.handleMouseDown(e,t);}return{grid:this.grid,ddel:this.ddel,rowIndex:_5,selections:sm.getSelections()};}return false;},onInitDrag:function(e){var _8=this.dragData;this.ddel.innerHTML=this.grid.getDragDropText();this.proxy.update(this.ddel);},afterRepair:function(){this.dragging=false;},getRepairXY:function(e,_a){return false;},onEndDrag:function(_b,e){},onValidDrop:function(dd,e,id){this.hideProxy();},beforeInvalidDrop:function(e,id){}});
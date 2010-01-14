/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.grid.SplitDragZone=function(_1,hd,_3){this.grid=_1;this.view=_1.getView();this.proxy=this.view.resizeProxy;Ext.grid.SplitDragZone.superclass.constructor.call(this,hd,"gridSplitters"+this.grid.getGridEl().id,{dragElId:Ext.id(this.proxy.dom),resizeFrame:false});this.setHandleElId(Ext.id(hd));this.setOuterHandleElId(Ext.id(_3));this.scroll=false;};Ext.extend(Ext.grid.SplitDragZone,Ext.dd.DDProxy,{fly:Ext.Element.fly,b4StartDrag:function(x,y){this.view.headersDisabled=true;this.proxy.setHeight(this.view.mainWrap.getHeight());var w=this.cm.getColumnWidth(this.cellIndex);var _7=Math.max(w-this.grid.minColumnWidth,0);this.resetConstraints();this.setXConstraint(_7,1000);this.setYConstraint(0,0);this.minX=x-_7;this.maxX=x+1000;this.startPos=x;Ext.dd.DDProxy.prototype.b4StartDrag.call(this,x,y);},handleMouseDown:function(e){ev=Ext.EventObject.setEvent(e);var t=this.fly(ev.getTarget());if(t.hasClass("x-grid-split")){this.cellIndex=this.view.getCellIndex(t.dom);this.split=t.dom;this.cm=this.grid.colModel;if(this.cm.isResizable(this.cellIndex)&&!this.cm.isFixed(this.cellIndex)){Ext.grid.SplitDragZone.superclass.handleMouseDown.apply(this,arguments);}}},endDrag:function(e){this.view.headersDisabled=false;var _b=Math.max(this.minX,Ext.lib.Event.getPageX(e));var _c=_b-this.startPos;this.view.onColumnSplitterMoved(this.cellIndex,this.cm.getColumnWidth(this.cellIndex)+_c);},autoOffset:function(){this.setDelta(0,0);}});
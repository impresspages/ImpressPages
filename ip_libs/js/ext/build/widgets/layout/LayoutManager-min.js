/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.LayoutManager=function(_1,_2){Ext.LayoutManager.superclass.constructor.call(this);this.el=Ext.get(_1);if(this.el.dom==document.body&&Ext.isIE&&!_2.allowScroll){document.body.scroll="no";}else{if(this.el.dom!=document.body&&this.el.getStyle("position")=="static"){this.el.position("relative");}}this.id=this.el.id;this.el.addClass("x-layout-container");this.monitorWindowResize=true;this.regions={};this.addEvents({"layout":true,"regionresized":true,"regioncollapsed":true,"regionexpanded":true});this.updating=false;Ext.EventManager.onWindowResize(this.onWindowResize,this,true);};Ext.extend(Ext.LayoutManager,Ext.util.Observable,{isUpdating:function(){return this.updating;},beginUpdate:function(){this.updating=true;},endUpdate:function(_3){this.updating=false;if(!_3){this.layout();}},layout:function(){},onRegionResized:function(_4,_5){this.fireEvent("regionresized",_4,_5);this.layout();},onRegionCollapsed:function(_6){this.fireEvent("regioncollapsed",_6);},onRegionExpanded:function(_7){this.fireEvent("regionexpanded",_7);},getViewSize:function(){var _8;if(this.el.dom!=document.body){_8=this.el.getSize();}else{_8={width:Ext.lib.Dom.getViewWidth(),height:Ext.lib.Dom.getViewHeight()};}_8.width-=this.el.getBorderWidth("lr")-this.el.getPadding("lr");_8.height-=this.el.getBorderWidth("tb")-this.el.getPadding("tb");return _8;},getEl:function(){return this.el;},getRegion:function(_9){return this.regions[_9.toLowerCase()];},onWindowResize:function(){if(this.monitorWindowResize){this.layout();}}});
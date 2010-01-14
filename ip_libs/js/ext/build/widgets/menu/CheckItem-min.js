/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.menu.CheckItem=function(_1){Ext.menu.CheckItem.superclass.constructor.call(this,_1);this.addEvents({"beforecheckchange":true,"checkchange":true});if(this.checkHandler){this.on("checkchange",this.checkHandler,this.scope);}};Ext.extend(Ext.menu.CheckItem,Ext.menu.Item,{itemCls:"x-menu-item x-menu-check-item",groupClass:"x-menu-group-item",checked:false,ctype:"Ext.menu.CheckItem",onRender:function(c){Ext.menu.CheckItem.superclass.onRender.apply(this,arguments);if(this.group){this.el.addClass(this.groupClass);}Ext.menu.MenuMgr.registerCheckable(this);if(this.checked){this.checked=false;this.setChecked(true,true);}},destroy:function(){if(this.rendered){Ext.menu.MenuMgr.unregisterCheckable(this);}Ext.menu.CheckItem.superclass.destroy.apply(this,arguments);},setChecked:function(_3,_4){if(this.checked!=_3&&this.fireEvent("beforecheckchange",this,_3)!==false){if(this.container){this.container[_3?"addClass":"removeClass"]("x-menu-item-checked");}this.checked=_3;if(_4!==true){this.fireEvent("checkchange",this,_3);}}},handleClick:function(e){if(!this.disabled&&!(this.checked&&this.group)){this.setChecked(!this.checked);}Ext.menu.CheckItem.superclass.handleClick.apply(this,arguments);}});
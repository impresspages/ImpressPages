/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.menu.DateItem=function(_1){Ext.menu.DateItem.superclass.constructor.call(this,new Ext.DatePicker(_1),_1);this.picker=this.component;this.addEvents({select:true});this.picker.on("render",function(_2){_2.getEl().swallowEvent("click");_2.container.addClass("x-menu-date-item");});this.picker.on("select",this.onSelect,this);};Ext.extend(Ext.menu.DateItem,Ext.menu.Adapter,{onSelect:function(_3,_4){this.fireEvent("select",this,_4,_3);Ext.menu.DateItem.superclass.handleClick.call(this);}});
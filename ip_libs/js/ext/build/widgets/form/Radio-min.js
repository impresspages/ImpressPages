/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.form.Radio=function(){Ext.form.Radio.superclass.constructor.apply(this,arguments);};Ext.extend(Ext.form.Radio,Ext.form.Checkbox,{inputType:"radio",getGroupValue:function(){return this.el.up("form").child("input[name="+this.el.dom.name+"]:checked",true).value;}});
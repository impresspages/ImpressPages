/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.data.MemoryProxy=function(_1){Ext.data.MemoryProxy.superclass.constructor.call(this);this.data=_1;};Ext.extend(Ext.data.MemoryProxy,Ext.data.DataProxy,{load:function(_2,_3,_4,_5,_6){_2=_2||{};var _7;try{_7=_3.readRecords(this.data);}catch(e){this.fireEvent("loadexception",this,_6,null,e);_4.call(_5,null,_6,false);return;}_4.call(_5,_7,_6,true);},update:function(_8,_9){}});
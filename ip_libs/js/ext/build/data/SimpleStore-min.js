/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.data.SimpleStore=function(_1){Ext.data.SimpleStore.superclass.constructor.call(this,{reader:new Ext.data.ArrayReader({id:_1.id},Ext.data.Record.create(_1.fields)),proxy:new Ext.data.MemoryProxy(_1.data)});this.load();};Ext.extend(Ext.data.SimpleStore,Ext.data.Store);
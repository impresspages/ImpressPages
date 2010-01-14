/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.data.JsonStore=function(c){Ext.data.JsonStore.superclass.constructor.call(this,Ext.apply(c,{proxy:!c.data?new Ext.data.HttpProxy({url:c.url}):undefined,reader:new Ext.data.JsonReader(c,c.fields)}));};Ext.extend(Ext.data.JsonStore,Ext.data.Store);
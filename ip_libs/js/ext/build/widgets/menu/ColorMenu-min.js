/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.menu.ColorMenu=function(_1){Ext.menu.ColorMenu.superclass.constructor.call(this,_1);this.plain=true;var ci=new Ext.menu.ColorItem(_1);this.add(ci);this.palette=ci.palette;this.relayEvents(ci,["select"]);};Ext.extend(Ext.menu.ColorMenu,Ext.menu.Menu);
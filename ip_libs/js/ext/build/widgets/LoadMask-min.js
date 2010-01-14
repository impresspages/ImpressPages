/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.LoadMask=function(el,_2){this.el=Ext.get(el);Ext.apply(this,_2);if(this.store){this.store.on("beforeload",this.onBeforeLoad,this);this.store.on("load",this.onLoad,this);this.store.on("loadexception",this.onLoad,this);this.removeMask=false;}else{var um=this.el.getUpdateManager();um.showLoadIndicator=false;um.on("beforeupdate",this.onBeforeLoad,this);um.on("update",this.onLoad,this);um.on("failure",this.onLoad,this);this.removeMask=true;}};Ext.LoadMask.prototype={msg:"Loading...",msgCls:"x-mask-loading",disabled:false,disable:function(){this.disabled=true;},enable:function(){this.disabled=false;},onLoad:function(){this.el.unmask(this.removeMask);},onBeforeLoad:function(){if(!this.disabled){this.el.mask(this.msg,this.msgCls);}},destroy:function(){if(this.store){this.store.un("beforeload",this.onBeforeLoad,this);this.store.un("load",this.onLoad,this);this.store.un("loadexception",this.onLoad,this);}else{var um=this.el.getUpdateManager();um.un("beforeupdate",this.onBeforeLoad,this);um.un("update",this.onLoad,this);um.un("failure",this.onLoad,this);}}};
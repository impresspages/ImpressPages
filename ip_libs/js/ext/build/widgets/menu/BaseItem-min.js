/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.menu.BaseItem=function(_1){Ext.menu.BaseItem.superclass.constructor.call(this,_1);this.addEvents({click:true,activate:true,deactivate:true});if(this.handler){this.on("click",this.handler,this.scope,true);}};Ext.extend(Ext.menu.BaseItem,Ext.Component,{canActivate:false,activeClass:"x-menu-item-active",hideOnClick:true,hideDelay:100,ctype:"Ext.menu.BaseItem",actionMode:"container",render:function(_2,_3){this.parentMenu=_3;Ext.menu.BaseItem.superclass.render.call(this,_2);this.container.menuItemId=this.id;},onRender:function(_4,_5){this.el=Ext.get(this.el);_4.dom.appendChild(this.el.dom);},onClick:function(e){if(!this.disabled&&this.fireEvent("click",this,e)!==false&&this.parentMenu.fireEvent("itemclick",this,e)!==false){this.handleClick(e);}else{e.stopEvent();}},activate:function(){if(this.disabled){return false;}var li=this.container;li.addClass(this.activeClass);this.region=li.getRegion().adjust(2,2,-2,-2);this.fireEvent("activate",this);return true;},deactivate:function(){this.container.removeClass(this.activeClass);this.fireEvent("deactivate",this);},shouldDeactivate:function(e){return!this.region||!this.region.contains(e.getPoint());},handleClick:function(e){if(this.hideOnClick){this.parentMenu.hide.defer(this.hideDelay,this.parentMenu,[true]);}},expandMenu:function(_a){},hideMenu:function(){}});
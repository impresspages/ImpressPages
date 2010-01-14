/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.dd.Registry=function(){var _1={};var _2={};var _3=0;var _4=function(el,_6){if(typeof el=="string"){return el;}var id=el.id;if(!id&&_6!==false){id="extdd-"+(++_3);el.id=id;}return id;};return{register:function(el,_9){_9=_9||{};if(typeof el=="string"){el=document.getElementById(el);}_9.ddel=el;_1[_4(el)]=_9;if(_9.isHandle!==false){_2[_9.ddel.id]=_9;}if(_9.handles){var hs=_9.handles;for(var i=0,_c=hs.length;i<_c;i++){_2[_4(hs[i])]=_9;}}},unregister:function(el){var id=_4(el,false);var _f=_1[id];if(_f){delete _1[id];if(_f.handles){var hs=_f.handles;for(var i=0,len=hs.length;i<len;i++){delete _2[_4(hs[i],false)];}}}},getHandle:function(id){if(typeof id!="string"){id=id.id;}return _2[id];},getHandleFromEvent:function(e){var t=Ext.lib.Event.getTarget(e);return t?_2[t.id]:null;},getTarget:function(id){if(typeof id!="string"){id=id.id;}return _1[id];},getTargetFromEvent:function(e){var t=Ext.lib.Event.getTarget(e);return t?_1[t.id]||_2[t.id]:null;}};}();
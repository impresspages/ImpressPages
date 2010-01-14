/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.KeyMap=function(el,_2,_3){this.el=Ext.get(el);this.eventName=_3||"keydown";this.bindings=[];if(_2){this.addBinding(_2);}this.enable();};Ext.KeyMap.prototype={stopEvent:false,addBinding:function(_4){if(_4 instanceof Array){for(var i=0,_6=_4.length;i<_6;i++){this.addBinding(_4[i]);}return;}var _7=_4.key,_8=_4.shift,_9=_4.ctrl,_a=_4.alt,fn=_4.fn,_c=_4.scope;if(typeof _7=="string"){var ks=[];var _e=_7.toUpperCase();for(var j=0,_6=_e.length;j<_6;j++){ks.push(_e.charCodeAt(j));}_7=ks;}var _10=_7 instanceof Array;var _11=function(e){if((!_8||e.shiftKey)&&(!_9||e.ctrlKey)&&(!_a||e.altKey)){var k=e.getKey();if(_10){for(var i=0,_6=_7.length;i<_6;i++){if(_7[i]==k){if(this.stopEvent){e.stopEvent();}fn.call(_c||window,k,e);return;}}}else{if(k==_7){if(this.stopEvent){e.stopEvent();}fn.call(_c||window,k,e);}}}};this.bindings.push(_11);},on:function(key,fn,_17){var _18,_19,_1a,alt;if(typeof key=="object"&&!(key instanceof Array)){_18=key.key;_19=key.shift;_1a=key.ctrl;alt=key.alt;}else{_18=key;}this.addBinding({key:_18,shift:_19,ctrl:_1a,alt:alt,fn:fn,scope:_17});},handleKeyDown:function(e){if(this.enabled){var b=this.bindings;for(var i=0,len=b.length;i<len;i++){b[i].call(this,e);}}},isEnabled:function(){return this.enabled;},enable:function(){if(!this.enabled){this.el.on(this.eventName,this.handleKeyDown,this);this.enabled=true;}},disable:function(){if(this.enabled){this.el.removeListener(this.eventName,this.handleKeyDown,this);this.enabled=false;}}};
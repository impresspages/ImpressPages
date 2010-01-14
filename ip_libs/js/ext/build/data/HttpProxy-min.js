/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.data.HttpProxy=function(_1){Ext.data.HttpProxy.superclass.constructor.call(this);this.conn=_1;this.useAjax=!_1||!_1.events;};Ext.extend(Ext.data.HttpProxy,Ext.data.DataProxy,{getConnection:function(){return this.useAjax?Ext.Ajax:this.conn;},load:function(_2,_3,_4,_5,_6){if(this.fireEvent("beforeload",this,_2)!==false){var o={params:_2||{},request:{callback:_4,scope:_5,arg:_6},reader:_3,callback:this.loadResponse,scope:this};if(this.useAjax){Ext.applyIf(o,this.conn);if(this.activeRequest){Ext.Ajax.abort(this.activeRequest);}this.activeRequest=Ext.Ajax.request(o);}else{this.conn.request(o);}}else{_4.call(_5||this,null,_6,false);}},loadResponse:function(o,_9,_a){delete this.activeRequest;if(!_9){this.fireEvent("loadexception",this,o,_a);o.request.callback.call(o.request.scope,null,o.request.arg,false);return;}var _b;try{_b=o.reader.read(_a);}catch(e){this.fireEvent("loadexception",this,o,_a,e);o.request.callback.call(o.request.scope,null,o.request.arg,false);return;}this.fireEvent("load",this,o,o.request.arg);o.request.callback.call(o.request.scope,_b,o.request.arg,true);},update:function(_c){},updateResponse:function(_d){}});
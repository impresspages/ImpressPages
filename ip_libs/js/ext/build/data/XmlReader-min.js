/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.data.XmlReader=function(_1,_2){_1=_1||{};Ext.data.XmlReader.superclass.constructor.call(this,_1,_2||_1.fields);};Ext.extend(Ext.data.XmlReader,Ext.data.DataReader,{read:function(_3){var _4=_3.responseXML;if(!_4){throw{message:"XmlReader.read: XML Document not available"};}return this.readRecords(_4);},readRecords:function(_5){this.xmlData=_5;var _6=_5.documentElement||_5;var q=Ext.DomQuery;var _8=this.recordType,_9=_8.prototype.fields;var _a=this.meta.id;var _b=0,_c=true;if(this.meta.totalRecords){_b=q.selectNumber(this.meta.totalRecords,_6,0);}if(this.meta.success){var sv=q.selectValue(this.meta.success,_6,true);_c=sv!==false&&sv!=="false";}var _e=[];var ns=q.select(this.meta.record,_6);for(var i=0,len=ns.length;i<len;i++){var n=ns[i];var _13={};var id=_a?q.selectValue(_a,n):undefined;for(var j=0,_16=_9.length;j<_16;j++){var f=_9.items[j];var v=q.selectValue(f.mapping||f.name,n,f.defaultValue);v=f.convert(v);_13[f.name]=v;}var _19=new _8(_13,id);_19.node=n;_e[_e.length]=_19;}return{success:_c,records:_e,totalRecords:_b||_e.length};}});
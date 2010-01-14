/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.data.Field=function(_1){if(typeof _1=="string"){_1={name:_1};}Ext.apply(this,_1);if(!this.type){this.type="auto";}var st=Ext.data.SortTypes;if(typeof this.sortType=="string"){this.sortType=st[this.sortType];}if(!this.sortType){switch(this.type){case"string":this.sortType=st.asUCString;break;case"date":this.sortType=st.asDate;break;default:this.sortType=st.none;}}var _3=/[\$,%]/g;if(!this.convert){var cv,_5=this.dateFormat;switch(this.type){case"":case"auto":case undefined:cv=function(v){return v;};break;case"string":cv=function(v){return(v===undefined||v===null)?"":String(v);};break;case"int":cv=function(v){return v!==undefined&&v!==null&&v!==""?parseInt(String(v).replace(_3,""),10):"";};break;case"float":cv=function(v){return v!==undefined&&v!==null&&v!==""?parseFloat(String(v).replace(_3,""),10):"";};break;case"bool":case"boolean":cv=function(v){return v===true||v==="true"||v==1;};break;case"date":cv=function(v){if(!v){return"";}if(v instanceof Date){return v;}if(_5){if(_5=="timestamp"){return new Date(v*1000);}return Date.parseDate(v,_5);}var _c=Date.parse(v);return _c?new Date(_c):null;};break;}this.convert=cv;}};Ext.data.Field.prototype={dateFormat:null,defaultValue:"",mapping:null,sortType:null,sortDir:"ASC"};
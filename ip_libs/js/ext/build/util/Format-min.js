/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.util.Format=function(){var _1=/^\s+|\s+$/g;return{ellipsis:function(_2,_3){if(_2&&_2.length>_3){return _2.substr(0,_3-3)+"...";}return _2;},undef:function(_4){return typeof _4!="undefined"?_4:"";},htmlEncode:function(_5){return!_5?_5:String(_5).replace(/&/g,"&amp;").replace(/>/g,"&gt;").replace(/</g,"&lt;").replace(/"/g,"&quot;");},htmlDecode:function(_6){return!_6?_6:String(_6).replace(/&amp;/g,"&").replace(/&gt;/g,">").replace(/&lt;/g,"<").replace(/&quot;/g,"\"");},trim:function(_7){return String(_7).replace(_1,"");},substr:function(_8,_9,_a){return String(_8).substr(_9,_a);},lowercase:function(_b){return String(_b).toLowerCase();},uppercase:function(_c){return String(_c).toUpperCase();},capitalize:function(_d){return!_d?_d:_d.charAt(0).toUpperCase()+_d.substr(1).toLowerCase();},call:function(_e,fn){if(arguments.length>2){var _10=Array.prototype.slice.call(arguments,2);_10.unshift(_e);return eval(fn).apply(window,_10);}else{return eval(fn).call(window,_e);}},usMoney:function(v){v=(Math.round((v-0)*100))/100;v=(v==Math.floor(v))?v+".00":((v*10==Math.floor(v*10))?v+"0":v);v=String(v);var ps=v.split(".");var _13=ps[0];var sub=ps[1]?"."+ps[1]:".00";var r=/(\d+)(\d{3})/;while(r.test(_13)){_13=_13.replace(r,"$1"+","+"$2");}return"$"+_13+sub;},date:function(v,_17){if(!v){return"";}if(!(v instanceof Date)){v=new Date(Date.parse(v));}return v.dateFormat(_17||"m/d/Y");},dateRenderer:function(_18){return function(v){return Ext.util.Format.date(v,_18);};},stripTagsRE:/<\/?[^>]+>/gi,stripTags:function(v){return!v?v:String(v).replace(this.stripTagsRE,"");}};}();
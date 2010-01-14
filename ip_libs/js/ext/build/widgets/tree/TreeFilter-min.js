/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.tree.TreeFilter=function(_1,_2){this.tree=_1;this.filtered={};Ext.apply(this,_2);};Ext.tree.TreeFilter.prototype={clearBlank:false,reverse:false,autoClear:false,remove:false,filter:function(_3,_4,_5){_4=_4||"text";var f;if(typeof _3=="string"){var _7=_3.length;if(_7==0&&this.clearBlank){this.clear();return;}_3=_3.toLowerCase();f=function(n){return n.attributes[_4].substr(0,_7).toLowerCase()==_3;};}else{if(_3.exec){f=function(n){return _3.test(n.attributes[_4]);};}else{throw"Illegal filter type, must be string or regex";}}this.filterBy(f,null,_5);},filterBy:function(fn,_b,_c){_c=_c||this.tree.root;if(this.autoClear){this.clear();}var af=this.filtered,rv=this.reverse;var f=function(n){if(n==_c){return true;}if(af[n.id]){return false;}var m=fn.call(_b||n,n);if(!m||rv){af[n.id]=n;n.ui.hide();return false;}return true;};_c.cascade(f);if(this.remove){for(var id in af){if(typeof id!="function"){var n=af[id];if(n&&n.parentNode){n.parentNode.removeChild(n);}}}}},clear:function(){var t=this.tree;var af=this.filtered;for(var id in af){if(typeof id!="function"){var n=af[id];if(n){n.ui.show();}}}this.filtered={};}};
/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.tree.TreeSorter=function(_1,_2){Ext.apply(this,_2);_1.on("beforechildrenrendered",this.doSort,this);_1.on("append",this.updateSort,this);_1.on("insert",this.updateSort,this);var _3=this.dir&&this.dir.toLowerCase()=="desc";var p=this.property||"text";var _5=this.sortType;var fs=this.folderSort;var cs=this.caseSensitive===true;var _8=this.leafAttr||"leaf";this.sortFn=function(n1,n2){if(fs){if(n1.attributes[_8]&&!n2.attributes[_8]){return 1;}if(!n1.attributes[_8]&&n2.attributes[_8]){return-1;}}var v1=_5?_5(n1):(cs?n1.attributes[p]:n1.attributes[p].toUpperCase());var v2=_5?_5(n2):(cs?n2.attributes[p]:n2.attributes[p].toUpperCase());if(v1<v2){return _3?+1:-1;}else{if(v1>v2){return _3?-1:+1;}else{return 0;}}};};Ext.tree.TreeSorter.prototype={doSort:function(_d){_d.sort(this.sortFn);},compareNodes:function(n1,n2){return(n1.text.toUpperCase()>n2.text.toUpperCase()?1:-1);},updateSort:function(_10,_11){if(_11.childrenRendered){this.doSort.defer(1,this,[_11]);}}};
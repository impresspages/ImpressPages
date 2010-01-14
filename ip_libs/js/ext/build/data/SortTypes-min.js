/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.data.SortTypes={none:function(s){return s;},stripTagsRE:/<\/?[^>]+>/gi,asText:function(s){return String(s).replace(this.stripTagsRE,"");},asUCText:function(s){return String(s).toUpperCase().replace(this.stripTagsRE,"");},asUCString:function(s){return String(s).toUpperCase();},asDate:function(s){if(!s){return 0;}if(s instanceof Date){return s.getTime();}return Date.parse(String(s));},asFloat:function(s){var _7=parseFloat(String(s).replace(/,/g,""));if(isNaN(_7)){_7=0;}return _7;},asInt:function(s){var _9=parseInt(String(s).replace(/,/g,""));if(isNaN(_9)){_9=0;}return _9;}};
/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.util.TextMetrics=function(){var _1;return{measure:function(el,_3,_4){if(!_1){_1=Ext.util.TextMetrics.Instance(el,_4);}_1.bind(el);_1.setFixedWidth(_4||"auto");return _1.getSize(_3);},createInstance:function(el,_6){return Ext.util.TextMetrics.Instance(el,_6);}};}();Ext.util.TextMetrics.Instance=function(_7,_8){var ml=new Ext.Element(document.createElement("div"));document.body.appendChild(ml.dom);ml.position("absolute");ml.setLeftTop(-1000,-1000);ml.hide();if(_8){ml.setWidth(_8);}var _a={getSize:function(_b){ml.update(_b);var s=ml.getSize();ml.update("");return s;},bind:function(el){ml.setStyle(Ext.fly(el).getStyles("font-size","font-style","font-weight","font-family","line-height"));},setFixedWidth:function(_e){ml.setWidth(_e);},getWidth:function(_f){ml.dom.style.width="auto";return this.getSize(_f).width;},getHeight:function(_10){return this.getSize(_10).height;}};_a.bind(_7);return _a;};Ext.Element.measureText=Ext.util.TextMetrics.measure;
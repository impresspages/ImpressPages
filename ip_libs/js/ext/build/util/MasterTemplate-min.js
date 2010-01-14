/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.MasterTemplate=function(){Ext.MasterTemplate.superclass.constructor.apply(this,arguments);this.originalHtml=this.html;var st={};var m,re=this.subTemplateRe;re.lastIndex=0;var _4=0;while(m=re.exec(this.html)){var _5=m[1],_6=m[2];st[_4]={name:_5,index:_4,buffer:[],tpl:new Ext.Template(_6)};if(_5){st[_5]=st[_4];}st[_4].tpl.compile();st[_4].tpl.call=this.call.createDelegate(this);_4++;}this.subCount=_4;this.subs=st;};Ext.extend(Ext.MasterTemplate,Ext.Template,{subTemplateRe:/<tpl(?:\sname="([\w-]+)")?>((?:.|\n)*?)<\/tpl>/gi,add:function(_7,_8){if(arguments.length==1){_8=arguments[0];_7=0;}var s=this.subs[_7];s.buffer[s.buffer.length]=s.tpl.apply(_8);return this;},fill:function(_a,_b,_c){var a=arguments;if(a.length==1||(a.length==2&&typeof a[1]=="boolean")){_b=a[0];_a=0;_c=a[1];}if(_c){this.reset();}for(var i=0,_f=_b.length;i<_f;i++){this.add(_a,_b[i]);}return this;},reset:function(){var s=this.subs;for(var i=0;i<this.subCount;i++){s[i].buffer=[];}return this;},applyTemplate:function(_12){var s=this.subs;var _14=-1;this.html=this.originalHtml.replace(this.subTemplateRe,function(m,_16){return s[++_14].buffer.join("");});return Ext.MasterTemplate.superclass.applyTemplate.call(this,_12);},apply:function(){return this.applyTemplate.apply(this,arguments);},compile:function(){return this;}});Ext.MasterTemplate.prototype.addAll=Ext.MasterTemplate.prototype.fill;Ext.MasterTemplate.from=function(el,_18){el=Ext.getDom(el);return new Ext.MasterTemplate(el.value||el.innerHTML,_18||"");};
/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.util.TaskRunner=function(_1){_1=_1||10;var _2=[],_3=[];var id=0;var _5=false;var _6=function(){_5=false;clearInterval(id);id=0;};var _7=function(){if(!_5){_5=true;id=setInterval(_8,_1);}};var _9=function(_a){_3.push(_a);if(_a.onStop){_a.onStop();}};var _8=function(){if(_3.length>0){for(var i=0,_c=_3.length;i<_c;i++){_2.remove(_3[i]);}_3=[];if(_2.length<1){_6();return;}}var _d=new Date().getTime();for(var i=0,_c=_2.length;i<_c;++i){var t=_2[i];var _f=_d-t.taskRunTime;if(t.interval<=_f){var rt=t.run.apply(t.scope||t,t.args||[++t.taskRunCount]);t.taskRunTime=_d;if(rt===false||t.taskRunCount===t.repeat){_9(t);return;}}if(t.duration&&t.duration<=(_d-t.taskStartTime)){_9(t);}}};this.start=function(_11){_2.push(_11);_11.taskStartTime=new Date().getTime();_11.taskRunTime=0;_11.taskRunCount=0;_7();return _11;};this.stop=function(_12){_9(_12);return _12;};this.stopAll=function(){_6();for(var i=0,len=_2.length;i<len;i++){if(_2[i].onStop){_2[i].onStop();}}_2=[];_3=[];};};Ext.TaskMgr=new Ext.util.TaskRunner();
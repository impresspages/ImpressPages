/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.LayoutStateManager=function(_1){this.state={north:{},south:{},east:{},west:{}};};Ext.LayoutStateManager.prototype={init:function(_2,_3){this.provider=_3;var _4=_3.get(_2.id+"-layout-state");if(_4){var _5=_2.isUpdating();if(!_5){_2.beginUpdate();}for(var _6 in _4){if(typeof _4[_6]!="function"){var _7=_4[_6];var r=_2.getRegion(_6);if(r&&_7){if(_7.size){r.resizeTo(_7.size);}if(_7.collapsed==true){r.collapse(true);}else{r.expand(null,true);}}}}if(!_5){_2.endUpdate();}this.state=_4;}this.layout=_2;_2.on("regionresized",this.onRegionResized,this);_2.on("regioncollapsed",this.onRegionCollapsed,this);_2.on("regionexpanded",this.onRegionExpanded,this);},storeState:function(){this.provider.set(this.layout.id+"-layout-state",this.state);},onRegionResized:function(_9,_a){this.state[_9.getPosition()].size=_a;this.storeState();},onRegionCollapsed:function(_b){this.state[_b.getPosition()].collapsed=true;this.storeState();},onRegionExpanded:function(_c){this.state[_c.getPosition()].collapsed=false;this.storeState();}};
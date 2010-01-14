/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/**
 * @class Ext.LayoutManager
 * @extends Ext.util.Observable
 * Base class for layout managers.
 */
Ext.LayoutManager = function(container, config){
    Ext.LayoutManager.superclass.constructor.call(this);
    this.el = Ext.get(container);
    // ie scrollbar fix
    if(this.el.dom == document.body && Ext.isIE && !config.allowScroll){
        document.body.scroll = "no";
    }else if(this.el.dom != document.body && this.el.getStyle('position') == 'static'){
        this.el.position('relative');
    }
    this.id = this.el.id;
    this.el.addClass("x-layout-container");
    /** false to disable window resize monitoring @type Boolean */
    this.monitorWindowResize = true;
    this.regions = {};
    this.addEvents({
        /**
         * @event layout
         * Fires when a layout is performed. 
         * @param {Ext.LayoutManager} this
         */
        "layout" : true,
        /**
         * @event regionresized
         * Fires when the user resizes a region. 
         * @param {Ext.LayoutRegion} region The resized region
         * @param {Number} newSize The new size (width for east/west, height for north/south)
         */
        "regionresized" : true,
        /**
         * @event regioncollapsed
         * Fires when a region is collapsed. 
         * @param {Ext.LayoutRegion} region The collapsed region
         */
        "regioncollapsed" : true,
        /**
         * @event regionexpanded
         * Fires when a region is expanded.  
         * @param {Ext.LayoutRegion} region The expanded region
         */
        "regionexpanded" : true
    });
    this.updating = false;
    Ext.EventManager.onWindowResize(this.onWindowResize, this, true);
};

Ext.extend(Ext.LayoutManager, Ext.util.Observable, {
    /**
     * Returns true if this layout is currently being updated
     * @return {Boolean}
     */
    isUpdating : function(){
        return this.updating; 
    },
    
    /**
     * Suspend the LayoutManager from doing auto-layouts while
     * making multiple add or remove calls
     */
    beginUpdate : function(){
        this.updating = true;    
    },
    
    /**
     * Restore auto-layouts and optionally disable the manager from performing a layout
     * @param {Boolean} noLayout true to disable a layout update 
     */
    endUpdate : function(noLayout){
        this.updating = false;
        if(!noLayout){
            this.layout();
        }    
    },
    
    layout: function(){
        
    },
    
    onRegionResized : function(region, newSize){
        this.fireEvent("regionresized", region, newSize);
        this.layout();
    },
    
    onRegionCollapsed : function(region){
        this.fireEvent("regioncollapsed", region);
    },
    
    onRegionExpanded : function(region){
        this.fireEvent("regionexpanded", region);
    },
        
    /**
     * Returns the size of the current view. This method normalizes document.body and element embedded layouts and
     * performs box-model adjustments.
     * @return {Object} The size as an object {width: (the width), height: (the height)}
     */
    getViewSize : function(){
        var size;
        if(this.el.dom != document.body){
            size = this.el.getSize();
        }else{
            size = {width: Ext.lib.Dom.getViewWidth(), height: Ext.lib.Dom.getViewHeight()};
        }
        size.width -= this.el.getBorderWidth("lr")-this.el.getPadding("lr");
        size.height -= this.el.getBorderWidth("tb")-this.el.getPadding("tb");
        return size;
    },
    
    /**
     * Returns the Element this layout is bound to.
     * @return {Ext.Element}
     */
    getEl : function(){
        return this.el;
    },
    
    /**
     * Returns the specified region.
     * @param {String} target The region key ('center', 'north', 'south', 'east' or 'west')
     * @return {Ext.LayoutRegion}
     */
    getRegion : function(target){
        return this.regions[target.toLowerCase()];
    },
    
    onWindowResize : function(){
        if(this.monitorWindowResize){
            this.layout();
        }
    }
});
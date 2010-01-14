/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/**
 * @class Ext.ContentPanel
 * @extends Ext.util.Observable
 * A basic ContentPanel element.
 * @cfg {Boolean} fitToFrame True for this panel to adjust its size to fit when the region resizes  (defaults to false)
 * @cfg {Boolean} fitContainer When using {@link #fitToFrame} and {@link #resizeEl}, you can also fit the parent container  (defaults to false)
 * @cfg {Boolean/Object} autoCreate True to auto generate the DOM element for this panel, or a {@link Ext.DomHelper} config of the element to create
 * @cfg {Boolean} closable True if the panel can be closed/removed
 * @cfg {Boolean} background True if the panel should not be activated when it is added (defaults to false)
 * @cfg {String/HTMLElement/Element} resizeEl An element to resize if {@link #fitToFrame} is true (instead of this panel's element)
 * @cfg {Toolbar} toolbar A toolbar for this panel
 * @cfg {Boolean} autoScroll True to scroll overflow in this panel (use with {@link #fitToFrame})
 * @cfg {String} title The title for this panel
 * @cfg {Array} adjustments Values to <b>add</b> to the width/height when doing a {@link #fitToFrame} (default is [0, 0])
 * @cfg {String} url Calls {@link #setUrl} with this value
 * @cfg {String/Object} params When used with {@link #url}, calls {@link #setUrl} with this value
 * @cfg {Boolean} loadOnce When used with {@link #url}, calls {@link #setUrl} with this value
 * @constructor
 * Create a new ContentPanel.
 * @param {String/HTMLElement/Ext.Element} el The container element for this panel
 * @param {String/Object} config A string to set only the title or a config object
 * @param {String} content (optional) Set the HTML content for this panel
 */
Ext.ContentPanel = function(el, config, content){
    if(el.autoCreate){
        config = el;
        el = Ext.id();
    }
    this.el = Ext.get(el);
    if(!this.el && config && config.autoCreate){
        if(typeof config.autoCreate == "object"){
            if(!config.autoCreate.id){
                config.autoCreate.id = config.id||el;
            }
            this.el = Ext.DomHelper.append(document.body,
                        config.autoCreate, true);
        }else{
            this.el = Ext.DomHelper.append(document.body,
                        {tag: "div", cls: "x-layout-inactive-content", id: config.id||el}, true);
        }
    }
    this.closable = false;
    this.loaded = false;
    this.active = false;
    if(typeof config == "string"){
        this.title = config;
    }else{
        Ext.apply(this, config);
    }
    if(this.resizeEl){
        this.resizeEl = Ext.get(this.resizeEl, true);
    }else{
        this.resizeEl = this.el;
    }
    this.addEvents({
        /**
         * @event activate
         * Fires when this panel is activated. 
         * @param {Ext.ContentPanel} this
         */
        "activate" : true,
        /**
         * @event deactivate
         * Fires when this panel is activated. 
         * @param {Ext.ContentPanel} this
         */
        "deactivate" : true,

        /**
         * @event resize
         * Fires when this panel is resized if fitToFrame is true.
         * @param {Ext.ContentPanel} this
         * @param {Number} width The width after any component adjustments
         * @param {Number} height The height after any component adjustments
         */
        "resize" : true
    });
    if(this.autoScroll){
        this.resizeEl.setStyle("overflow", "auto");
    }
    content = content || this.content;
    if(content){
        this.setContent(content);
    }
    if(config && config.url){
        this.setUrl(this.url, this.params, this.loadOnce);
    }
    Ext.ContentPanel.superclass.constructor.call(this);
};

Ext.extend(Ext.ContentPanel, Ext.util.Observable, {
    tabTip:'',
    setRegion : function(region){
        this.region = region;
        if(region){
           this.el.replaceClass("x-layout-inactive-content", "x-layout-active-content");
        }else{
           this.el.replaceClass("x-layout-active-content", "x-layout-inactive-content");
        } 
    },
    
    /**
     * Returns the toolbar for this Panel if one was configured. 
     * @return {Ext.Toolbar} 
     */
    getToolbar : function(){
        return this.toolbar;
    },
    
    setActiveState : function(active){
        this.active = active;
        if(!active){
            this.fireEvent("deactivate", this);
        }else{
            this.fireEvent("activate", this);
        }
    },
    /**
     * Updates this panel's element
     * @param {String} content The new content
     * @param {Boolean} loadScripts (optional) true to look for and process scripts
    */
    setContent : function(content, loadScripts){
        this.el.update(content, loadScripts);
    },

    ignoreResize : function(w, h){
        if(this.lastSize && this.lastSize.width == w && this.lastSize.height == h){
            return true;
        }else{
            this.lastSize = {width: w, height: h};
            return false;
        }
    },
    /**
     * Get the {@link Ext.UpdateManager} for this panel. Enables you to perform Ajax updates.
     * @return {Ext.UpdateManager} The UpdateManager
     */
    getUpdateManager : function(){
        return this.el.getUpdateManager();
    },
     /**
     * Loads this content panel immediately with content from XHR. Note: to delay loading until the panel is activated, use {@link #setUrl}.
     * @param {Object/String/Function} url The url for this request or a function to call to get the url or a config object containing any of the following options:
<pre><code>
panel.load({
    url: "your-url.php",
    params: {param1: "foo", param2: "bar"}, // or a URL encoded string
    callback: yourFunction,
    scope: yourObject, //(optional scope)
    discardUrl: false,
    nocache: false,
    text: "Loading...",
    timeout: 30,
    scripts: false
});
</code></pre>
     * The only required property is <i>url</i>. The optional properties <i>nocache</i>, <i>text</i> and <i>scripts</i>
     * are shorthand for <i>disableCaching</i>, <i>indicatorText</i> and <i>loadScripts</i> and are used to set their associated property on this panel UpdateManager instance.
     * @param {String/Object} params (optional) The parameters to pass as either a URL encoded string "param1=1&amp;param2=2" or an object {param1: 1, param2: 2}
     * @param {Function} callback (optional) Callback when transaction is complete -- called with signature (oElement, bSuccess, oResponse)
     * @param {Boolean} discardUrl (optional) By default when you execute an update the defaultUrl is changed to the last used URL. If true, it will not store the URL.
     * @return {Ext.ContentPanel} this
     */
    load : function(){
        var um = this.el.getUpdateManager();
        um.update.apply(um, arguments);
        return this;
    },


    /**
     * Set a URL to be used to load the content for this panel. When this panel is activated, the content will be loaded from that URL.
     * @param {String/Function} url The URL to load the content from or a function to call to get the URL
     * @param {String/Object} params (optional) The string params for the update call or an object of the params. See {@link Ext.UpdateManager#update} for more details. (Defaults to null)
     * @param {Boolean} loadOnce (optional) Whether to only load the content once. If this is false it makes the Ajax call every time this panel is activated. (Defaults to false)
     * @return {Ext.UpdateManager} The UpdateManager
     */
    setUrl : function(url, params, loadOnce){
        if(this.refreshDelegate){
            this.removeListener("activate", this.refreshDelegate);
        }
        this.refreshDelegate = this._handleRefresh.createDelegate(this, [url, params, loadOnce]);
        this.on("activate", this.refreshDelegate);
        return this.el.getUpdateManager();
    },
    
    _handleRefresh : function(url, params, loadOnce){
        if(!loadOnce || !this.loaded){
            var updater = this.el.getUpdateManager();
            updater.update(url, params, this._setLoaded.createDelegate(this));
        }
    },
    
    _setLoaded : function(){
        this.loaded = true;
    }, 
    
    /**
     * Returns this panel's id
     * @return {String} 
     */
    getId : function(){
        return this.el.id;
    },
    
    /**
     * Returns this panel's element
     * @return {Ext.Element} 
     */
    getEl : function(){
        return this.el;
    },
    
    adjustForComponents : function(width, height){
        if(this.resizeEl != this.el){
            width -= this.el.getFrameWidth('lr');
            height -= this.el.getFrameWidth('tb');
        }
        if(this.toolbar){
            var te = this.toolbar.getEl();
            height -= te.getHeight();
            te.setWidth(width);
        }
        if(this.adjustments){
            width += this.adjustments[0];
            height += this.adjustments[1];
        }
        return {"width": width, "height": height};
    },
    
    setSize : function(width, height){
        if(this.fitToFrame && !this.ignoreResize(width, height)){
            if(this.fitContainer && this.resizeEl != this.el){
                this.el.setSize(width, height);
            }
            var size = this.adjustForComponents(width, height);
            this.resizeEl.setSize(this.autoWidth ? "auto" : size.width, this.autoHeight ? "auto" : size.height);
            this.fireEvent('resize', this, size.width, size.height);
        }
    },
    
    /**
     * Returns this panel's title
     * @return {String} 
     */
    getTitle : function(){
        return this.title;
    },
    
    /**
     * Set this panel's title
     * @param {String} title
     */
    setTitle : function(title){
        this.title = title;
        if(this.region){
            this.region.updatePanelTitle(this, title);
        }
    },
    
    /**
     * Returns true is this panel was configured to be closable
     * @return {Boolean} 
     */
    isClosable : function(){
        return this.closable;
    },
    
    beforeSlide : function(){
        this.el.clip();
        this.resizeEl.clip();
    },
    
    afterSlide : function(){
        this.el.unclip();
        this.resizeEl.unclip();
    },
    
    /**
     *   Force a content refresh from the URL specified in the {@link #setUrl} method.
     *   Will fail silently if the {@link #setUrl} method has not been called.
     *   This does not activate the panel, just updates its content.
     */
    refresh : function(){
        if(this.refreshDelegate){
           this.loaded = false;
           this.refreshDelegate();
        }
    },
    
    /**
     * Destroys this panel
     */
    destroy : function(){
        this.el.removeAllListeners();
        var tempEl = document.createElement("span");
        tempEl.appendChild(this.el.dom);
        tempEl.innerHTML = "";
        this.el.remove();
        this.el = null;
    }
});

/**
 * @class Ext.GridPanel
 * @extends Ext.ContentPanel
 * @constructor
 * Create a new GridPanel.
 * @param {Ext.grid.Grid} grid The grid for this panel
 * @param {String/Object} config A string to set only the panel's title, or a config object
 */
Ext.GridPanel = function(grid, config){
    this.wrapper = Ext.DomHelper.append(document.body, // wrapper for IE7 strict & safari scroll issue
        {tag: "div", cls: "x-layout-grid-wrapper x-layout-inactive-content"}, true);
    this.wrapper.dom.appendChild(grid.getGridEl().dom);
    Ext.GridPanel.superclass.constructor.call(this, this.wrapper, config);
    if(this.toolbar){
        this.toolbar.el.insertBefore(this.wrapper.dom.firstChild);
    }
    grid.monitorWindowResize = false; // turn off autosizing
    grid.autoHeight = false;
    grid.autoWidth = false;
    this.grid = grid;
    this.grid.getGridEl().replaceClass("x-layout-inactive-content", "x-layout-component-panel");
};

Ext.extend(Ext.GridPanel, Ext.ContentPanel, {
    getId : function(){
        return this.grid.id;
    },
    
    /**
     * Returns the grid for this panel
     * @return {Ext.grid.Grid} 
     */
    getGrid : function(){
        return this.grid;    
    },
    
    setSize : function(width, height){
        if(!this.ignoreResize(width, height)){
            var grid = this.grid;
            var size = this.adjustForComponents(width, height);
            grid.getGridEl().setSize(size.width, size.height);
            grid.autoSize();
        }
    },
    
    beforeSlide : function(){
        this.grid.getView().scroller.clip();
    },
    
    afterSlide : function(){
        this.grid.getView().scroller.unclip();
    },
    
    destroy : function(){
        this.grid.destroy();
        delete this.grid;
        Ext.GridPanel.superclass.destroy.call(this); 
    }
});


/**
 * @class Ext.NestedLayoutPanel
 * @extends Ext.ContentPanel
 * @constructor
 * Create a new NestedLayoutPanel.
 * @param {Ext.BorderLayout} layout The layout for this panel
 * @param {String/Object} config A string to set only the title or a config object
 */
Ext.NestedLayoutPanel = function(layout, config){
    Ext.NestedLayoutPanel.superclass.constructor.call(this, layout.getEl(), config);
    layout.monitorWindowResize = false; // turn off autosizing
    this.layout = layout;
    this.layout.getEl().addClass("x-layout-nested-layout");
};

Ext.extend(Ext.NestedLayoutPanel, Ext.ContentPanel, {

    setSize : function(width, height){
        if(!this.ignoreResize(width, height)){
            var size = this.adjustForComponents(width, height);
            var el = this.layout.getEl();
            el.setSize(size.width, size.height);
            var touch = el.dom.offsetWidth;
            this.layout.layout();
            // ie requires a double layout on the first pass
            if(Ext.isIE && !this.initialized){
                this.initialized = true;
                this.layout.layout();
            }
        }
    },
    
    /**
     * Returns the nested BorderLayout for this panel
     * @return {Ext.BorderLayout} 
     */
    getLayout : function(){
        return this.layout;
    }
});

Ext.ScrollPanel = function(el, config, content){
    config = config || {};
    config.fitToFrame = true;
    Ext.ScrollPanel.superclass.constructor.call(this, el, config, content);
    
    this.el.dom.style.overflow = "hidden";
    var wrap = this.el.wrap({cls: "x-scroller x-layout-inactive-content"});
    this.el.removeClass("x-layout-inactive-content");
    this.el.on("mousewheel", this.onWheel, this);

    var up = wrap.createChild({cls: "x-scroller-up", html: "&#160;"}, this.el.dom);
    var down = wrap.createChild({cls: "x-scroller-down", html: "&#160;"});
    up.unselectable(); down.unselectable();
    up.on("click", this.scrollUp, this);
    down.on("click", this.scrollDown, this);
    up.addClassOnOver("x-scroller-btn-over");
    down.addClassOnOver("x-scroller-btn-over");
    up.addClassOnClick("x-scroller-btn-click");
    down.addClassOnClick("x-scroller-btn-click");
    this.adjustments = [0, -(up.getHeight() + down.getHeight())];

    this.resizeEl = this.el;
    this.el = wrap; this.up = up; this.down = down;
};

Ext.extend(Ext.ScrollPanel, Ext.ContentPanel, {
    increment : 100,
    wheelIncrement : 5,
    scrollUp : function(){
        this.resizeEl.scroll("up", this.increment, {callback: this.afterScroll, scope: this});
    },

    scrollDown : function(){
        this.resizeEl.scroll("down", this.increment, {callback: this.afterScroll, scope: this});
    },

    afterScroll : function(){
        var el = this.resizeEl;
        var t = el.dom.scrollTop, h = el.dom.scrollHeight, ch = el.dom.clientHeight;
        this.up[t == 0 ? "addClass" : "removeClass"]("x-scroller-btn-disabled");
        this.down[h - t <= ch ? "addClass" : "removeClass"]("x-scroller-btn-disabled");
    },

    setSize : function(){
        Ext.ScrollPanel.superclass.setSize.apply(this, arguments);
        this.afterScroll();
    },

    onWheel : function(e){
        var d = e.getWheelDelta();
        this.resizeEl.dom.scrollTop -= (d*this.wheelIncrement);
        this.afterScroll();
        e.stopEvent();
    },

    setContent : function(content, loadScripts){
        this.resizeEl.update(content, loadScripts);
    }

});
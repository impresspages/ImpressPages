/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/**
 * @class Ext.BasicDialog
 * @extends Ext.util.Observable
 * Lightweight Dialog Class.  The code below shows the creation of a typical dialog using existing HTML markup:
 * <pre><code>
var dlg = new Ext.BasicDialog("my-dlg", {
    height: 200,
    width: 300,
    minHeight: 100,
    minWidth: 150,
    modal: true,
    proxyDrag: true,
    shadow: true
});
dlg.addKeyListener(27, dlg.hide, dlg); // ESC can also close the dialog
dlg.addButton('OK', dlg.hide, dlg);    // Could call a save function instead of hiding
dlg.addButton('Cancel', dlg.hide, dlg);
dlg.show();
</code></pre>
  <b>A Dialog should always be a direct child of the body element.</b>
 * @cfg {Boolean/DomHelper} autoCreate True to auto create from scratch, or using a DomHelper Object (defaults to false)
 * @cfg {String} title Default text to display in the title bar (defaults to null)
 * @cfg {Number} width Width of the dialog in pixels (can also be set via CSS).  Determined by browser if unspecified.
 * @cfg {Number} height Height of the dialog in pixels (can also be set via CSS).  Determined by browser if unspecified.
 * @cfg {Number} x The default left page coordinate of the dialog (defaults to center screen)
 * @cfg {Number} y The default top page coordinate of the dialog (defaults to center screen)
 * @cfg {String/Element} animateTarget Id or element from which the dialog should animate while opening
 * (defaults to null with no animation)
 * @cfg {Boolean} resizable False to disable manual dialog resizing (defaults to true)
 * @cfg {String} resizeHandles Which resize handles to display - see the {@link Ext.Resizable} handles config
 * property for valid values (defaults to 'all')
 * @cfg {Number} minHeight The minimum allowable height for a resizable dialog (defaults to 80)
 * @cfg {Number} minWidth The minimum allowable width for a resizable dialog (defaults to 200)
 * @cfg {Boolean} modal True to show the dialog modally, preventing user interaction with the rest of the page (defaults to false)
 * @cfg {Boolean} autoScroll True to allow the dialog body contents to overflow and display scrollbars (defaults to false)
 * @cfg {Boolean} closable False to remove the built-in top-right corner close button (defaults to true)
 * @cfg {Boolean} collapsible False to remove the built-in top-right corner collapse button (defaults to true)
 * @cfg {Boolean} constraintoviewport True to keep the dialog constrained within the visible viewport boundaries (defaults to true)
 * @cfg {Boolean} syncHeightBeforeShow True to cause the dimensions to be recalculated before the dialog is shown (defaults to false)
 * @cfg {Boolean} draggable False to disable dragging of the dialog within the viewport (defaults to true)
 * @cfg {Boolean} autoTabs If true, all elements with class 'x-dlg-tab' will get automatically converted to tabs (defaults to false)
 * @cfg {String} tabTag The tag name of tab elements, used when autoTabs = true (defaults to 'div')
 * @cfg {Boolean} proxyDrag True to drag a lightweight proxy element rather than the dialog itself, used when
 * draggable = true (defaults to false)
 * @cfg {Boolean} fixedcenter True to ensure that anytime the dialog is shown or resized it gets centered (defaults to false)
 * @cfg {Boolean/String} shadow True or "sides" for the default effect, "frame" for 4-way shadow, and "drop" for bottom-right
 * shadow (defaults to false)
 * @cfg {Number} shadowOffset The number of pixels to offset the shadow if displayed (defaults to 5)
 * @cfg {String} buttonAlign Valid values are "left," "center" and "right" (defaults to "right")
 * @cfg {Number} minButtonWidth Minimum width of all dialog buttons (defaults to 75)
 * @cfg {Boolean} shim True to create an iframe shim that prevents selects from showing through (defaults to false)
 * @constructor
 * Create a new BasicDialog.
 * @param {String/HTMLElement/Ext.Element} el The container element or DOM node, or its id
 * @param {Object} config Configuration options
 */
Ext.BasicDialog = function(el, config){
    this.el = Ext.get(el);
    var dh = Ext.DomHelper;
    if(!this.el && config && config.autoCreate){
        if(typeof config.autoCreate == "object"){
            if(!config.autoCreate.id){
                config.autoCreate.id = el;
            }
            this.el = dh.append(document.body,
                        config.autoCreate, true);
        }else{
            this.el = dh.append(document.body,
                        {tag: "div", id: el, style:'visibility:hidden;'}, true);
        }
    }
    el = this.el;
    el.setDisplayed(true);
    el.hide = this.hideAction;
    this.id = el.id;
    el.addClass("x-dlg");

    Ext.apply(this, config);

    this.proxy = el.createProxy("x-dlg-proxy");
    this.proxy.hide = this.hideAction;
    this.proxy.setOpacity(.5);
    this.proxy.hide();

    if(config.width){
        el.setWidth(config.width);
    }
    if(config.height){
        el.setHeight(config.height);
    }
    this.size = el.getSize();
    if(typeof config.x != "undefined" && typeof config.y != "undefined"){
        this.xy = [config.x,config.y];
    }else{
        this.xy = el.getCenterXY(true);
    }
    /** The header element @type Ext.Element */
    this.header = el.child("> .x-dlg-hd");
    /** The body element @type Ext.Element */
    this.body = el.child("> .x-dlg-bd");
    /** The footer element @type Ext.Element */
    this.footer = el.child("> .x-dlg-ft");

    if(!this.header){
        this.header = el.createChild({tag: "div", cls:"x-dlg-hd", html: "&#160;"}, this.body ? this.body.dom : null);
    }
    if(!this.body){
        this.body = el.createChild({tag: "div", cls:"x-dlg-bd"});
    }

    this.header.unselectable();
    if(this.title){
        this.header.update(this.title);
    }
    // this element allows the dialog to be focused for keyboard event
    this.focusEl = el.createChild({tag: "a", href:"#", cls:"x-dlg-focus", tabIndex:"-1"});
    this.focusEl.swallowEvent("click", true);

    this.header.wrap({cls:"x-dlg-hd-right"}).wrap({cls:"x-dlg-hd-left"}, true);

    // wrap the body and footer for special rendering
    this.bwrap = this.body.wrap({tag: "div", cls:"x-dlg-dlg-body"});
    if(this.footer){
        this.bwrap.dom.appendChild(this.footer.dom);
    }

    this.bg = this.el.createChild({
        tag: "div", cls:"x-dlg-bg",
        html: '<div class="x-dlg-bg-left"><div class="x-dlg-bg-right"><div class="x-dlg-bg-center">&#160;</div></div></div>'
    });
    this.centerBg = this.bg.child("div.x-dlg-bg-center");


    if(this.autoScroll !== false && !this.autoTabs){
        this.body.setStyle("overflow", "auto");
    }

    this.toolbox = this.el.createChild({cls: "x-dlg-toolbox"});

    if(this.closable !== false){
        this.el.addClass("x-dlg-closable");
        this.close = this.toolbox.createChild({cls:"x-dlg-close"});
        this.close.on("click", this.closeClick, this);
        this.close.addClassOnOver("x-dlg-close-over");
    }
    if(this.collapsible !== false){
        this.collapseBtn = this.toolbox.createChild({cls:"x-dlg-collapse"});
        this.collapseBtn.on("click", this.collapseClick, this);
        this.collapseBtn.addClassOnOver("x-dlg-collapse-over");
        this.header.on("dblclick", this.collapseClick, this);
    }
    if(this.resizable !== false){
        this.el.addClass("x-dlg-resizable");
        this.resizer = new Ext.Resizable(el, {
            minWidth: this.minWidth || 80,
            minHeight:this.minHeight || 80,
            handles: this.resizeHandles || "all",
            pinned: true
        });
        this.resizer.on("beforeresize", this.beforeResize, this);
        this.resizer.on("resize", this.onResize, this);
    }
    if(this.draggable !== false){
        el.addClass("x-dlg-draggable");
        if (!this.proxyDrag) {
            var dd = new Ext.dd.DD(el.dom.id, "WindowDrag");
        }
        else {
            var dd = new Ext.dd.DDProxy(el.dom.id, "WindowDrag", {dragElId: this.proxy.id});
        }
        dd.setHandleElId(this.header.id);
        dd.endDrag = this.endMove.createDelegate(this);
        dd.startDrag = this.startMove.createDelegate(this);
        dd.onDrag = this.onDrag.createDelegate(this);
        dd.scroll = false;
        this.dd = dd;
    }
    if(this.modal){
        this.mask = dh.append(document.body, {tag: "div", cls:"x-dlg-mask"}, true);
        this.mask.enableDisplayMode("block");
        this.mask.hide();
        this.el.addClass("x-dlg-modal");
    }
    if(this.shadow){
        this.shadow = new Ext.Shadow({
            mode : typeof this.shadow == "string" ? this.shadow : "sides",
            offset : this.shadowOffset
        });
    }else{
        this.shadowOffset = 0;
    }
    if(Ext.useShims && this.shim !== false){
        this.shim = this.el.createShim();
        this.shim.hide = this.hideAction;
        this.shim.hide();
    }else{
        this.shim = false;
    }
    if(this.autoTabs){
        this.initTabs();
    }
    this.addEvents({
        /**
         * @event keydown
         * Fires when a key is pressed
         * @param {Ext.BasicDialog} this
         * @param {Ext.EventObject} e
         */
        "keydown" : true,
        /**
         * @event move
         * Fires when this dialog is moved by the user.
         * @param {Ext.BasicDialog} this
         * @param {Number} x The new page X
         * @param {Number} y The new page Y
         */
        "move" : true,
        /**
         * @event resize
         * Fires when this dialog is resized by the user.
         * @param {Ext.BasicDialog} this
         * @param {Number} width The new width
         * @param {Number} height The new height
         */
        "resize" : true,
        /**
         * @event beforehide
         * Fires before this dialog is hidden.
         * @param {Ext.BasicDialog} this
         */
        "beforehide" : true,
        /**
         * @event hide
         * Fires when this dialog is hidden.
         * @param {Ext.BasicDialog} this
         */
        "hide" : true,
        /**
         * @event beforeshow
         * Fires before this dialog is shown.
         * @param {Ext.BasicDialog} this
         */
        "beforeshow" : true,
        /**
         * @event show
         * Fires when this dialog is shown.
         * @param {Ext.BasicDialog} this
         */
        "show" : true
    });
    el.on("keydown", this.onKeyDown, this);
    el.on("mousedown", this.toFront, this);
    Ext.EventManager.onWindowResize(this.adjustViewport, this, true);
    this.el.hide();
    Ext.DialogManager.register(this);
    Ext.BasicDialog.superclass.constructor.call(this);
};

Ext.extend(Ext.BasicDialog, Ext.util.Observable, {
    shadowOffset: Ext.isIE ? 6 : 5,
    minHeight: 80,
    minWidth: 200,
    minButtonWidth: 75,
    defaultButton: null,
    buttonAlign: "right",
    tabTag: 'div',
    firstShow: true,

    /**
     * Sets the dialog title text
     * @param {String} text The title text to display
     * @return {Ext.BasicDialog} this
     */
    setTitle : function(text){
        this.header.update(text);
        return this;
    },

    // private
    closeClick : function(){
        this.hide();
    },

    // private
    collapseClick : function(){
        this[this.collapsed ? "expand" : "collapse"]();
    },

    /**
     * Collapses the dialog to its minimized state (only the title bar is visible).
     * Equivalent to the user clicking the collapse dialog button.
     */
    collapse : function(){
        if(!this.collapsed){
            this.collapsed = true;
            this.el.addClass("x-dlg-collapsed");
            this.restoreHeight = this.el.getHeight();
            this.resizeTo(this.el.getWidth(), this.header.getHeight());
        }
    },

    /**
     * Expands a collapsed dialog back to its normal state.  Equivalent to the user
     * clicking the expand dialog button.
     */
    expand : function(){
        if(this.collapsed){
            this.collapsed = false;
            this.el.removeClass("x-dlg-collapsed");
            this.resizeTo(this.el.getWidth(), this.restoreHeight);
        }
    },

    /**
     * Reinitializes the tabs component, clearing out old tabs and finding new ones.
     * @return {Ext.TabPanel} The tabs component
     */
    initTabs : function(){
        var tabs = this.getTabs();
        while(tabs.getTab(0)){
            tabs.removeTab(0);
        }
        this.el.select(this.tabTag+'.x-dlg-tab').each(function(el){
            var dom = el.dom;
            tabs.addTab(Ext.id(dom), dom.title);
            dom.title = "";
        });
        tabs.activate(0);
        return tabs;
    },

    // private
    beforeResize : function(){
        this.resizer.minHeight = Math.max(this.minHeight, this.getHeaderFooterHeight(true)+40);
    },

    // private
    onResize : function(){
        this.refreshSize();
        this.syncBodyHeight();
        this.adjustAssets();
        this.focus();
        this.fireEvent("resize", this, this.size.width, this.size.height);
    },

    // private
    onKeyDown : function(e){
        if(this.isVisible()){
            this.fireEvent("keydown", this, e);
        }
    },

    /**
     * Resizes the dialog.
     * @param {Number} width
     * @param {Number} height
     * @return {Ext.BasicDialog} this
     */
    resizeTo : function(width, height){
        this.el.setSize(width, height);
        this.size = {width: width, height: height};
        this.syncBodyHeight();
        if(this.fixedcenter){
            this.center();
        }
        if(this.isVisible()){
            this.constrainXY();
            this.adjustAssets();
        }
        this.fireEvent("resize", this, width, height);
        return this;
    },


    /**
     * Resizes the dialog to fit the specified content size.
     * @param {Number} width
     * @param {Number} height
     * @return {Ext.BasicDialog} this
     */
    setContentSize : function(w, h){
        h += this.getHeaderFooterHeight() + this.body.getMargins("tb");
        w += this.body.getMargins("lr") + this.bwrap.getMargins("lr") + this.centerBg.getPadding("lr");
        //if(!this.el.isBorderBox()){
            h +=  this.body.getPadding("tb") + this.bwrap.getBorderWidth("tb") + this.body.getBorderWidth("tb") + this.el.getBorderWidth("tb");
            w += this.body.getPadding("lr") + this.bwrap.getBorderWidth("lr") + this.body.getBorderWidth("lr") + this.bwrap.getPadding("lr") + this.el.getBorderWidth("lr");
        //}
        if(this.tabs){
            h += this.tabs.stripWrap.getHeight() + this.tabs.bodyEl.getMargins("tb") + this.tabs.bodyEl.getPadding("tb");
            w += this.tabs.bodyEl.getMargins("lr") + this.tabs.bodyEl.getPadding("lr");
        }
        this.resizeTo(w, h);
        return this;
    },

    /**
     * Adds a key listener for when this dialog is displayed.  This allows you to hook in a function that will be
     * executed in response to a particular key being pressed while the dialog is active.
     * @param {Number/Array/Object} key Either the numeric key code, array of key codes or an object with the following options:
     *                                  {key: (number or array), shift: (true/false), ctrl: (true/false), alt: (true/false)}
     * @param {Function} fn The function to call
     * @param {Object} scope (optional) The scope of the function
     * @return {Ext.BasicDialog} this
     */
    addKeyListener : function(key, fn, scope){
        var keyCode, shift, ctrl, alt;
        if(typeof key == "object" && !(key instanceof Array)){
            keyCode = key["key"];
            shift = key["shift"];
            ctrl = key["ctrl"];
            alt = key["alt"];
        }else{
            keyCode = key;
        }
        var handler = function(dlg, e){
            if((!shift || e.shiftKey) && (!ctrl || e.ctrlKey) &&  (!alt || e.altKey)){
                var k = e.getKey();
                if(keyCode instanceof Array){
                    for(var i = 0, len = keyCode.length; i < len; i++){
                        if(keyCode[i] == k){
                          fn.call(scope || window, dlg, k, e);
                          return;
                        }
                    }
                }else{
                    if(k == keyCode){
                        fn.call(scope || window, dlg, k, e);
                    }
                }
            }
        };
        this.on("keydown", handler);
        return this;
    },

    /**
     * Returns the TabPanel component (creates it if it doesn't exist).
     * Note: If you wish to simply check for the existence of tabs without creating them,
     * check for a null 'tabs' property.
     * @return {Ext.TabPanel} The tabs component
     */
    getTabs : function(){
        if(!this.tabs){
            this.el.addClass("x-dlg-auto-tabs");
            this.body.addClass(this.tabPosition == "bottom" ? "x-tabs-bottom" : "x-tabs-top");
            this.tabs = new Ext.TabPanel(this.body.dom, this.tabPosition == "bottom");
        }
        return this.tabs;
    },

    /**
     * Adds a button to the footer section of the dialog.
     * @param {String/Object} config A string becomes the button text, an object can either be a Button config
     * object or a valid Ext.DomHelper element config
     * @param {Function} handler The function called when the button is clicked
     * @param {Object} scope (optional) The scope of the handler function
     * @return {Ext.Button} The new button
     */
    addButton : function(config, handler, scope){
        var dh = Ext.DomHelper;
        if(!this.footer){
            this.footer = dh.append(this.bwrap, {tag: "div", cls:"x-dlg-ft"}, true);
        }
        if(!this.btnContainer){
            var tb = this.footer.createChild({

                cls:"x-dlg-btns x-dlg-btns-"+this.buttonAlign,
                html:'<table cellspacing="0"><tbody><tr></tr></tbody></table><div class="x-clear"></div>'
            }, null, true);
            this.btnContainer = tb.firstChild.firstChild.firstChild;
        }
        var bconfig = {
            handler: handler,
            scope: scope,
            minWidth: this.minButtonWidth,
            hideParent:true
        };
        if(typeof config == "string"){
            bconfig.text = config;
        }else{
            if(config.tag){
                bconfig.dhconfig = config;
            }else{
                Ext.apply(bconfig, config);
            }
        }
        var btn = new Ext.Button(
            this.btnContainer.appendChild(document.createElement("td")),
            bconfig
        );
        this.syncBodyHeight();
        if(!this.buttons){
            /**
             * Array of all the buttons that have been added to this dialog via addButton
             * @type Array
             */
            this.buttons = [];
        }
        this.buttons.push(btn);
        return btn;
    },

    /**
     * Sets the default button to be focused when the dialog is displayed.
     * @param {Ext.BasicDialog.Button} btn The button object returned by {@link #addButton}
     * @return {Ext.BasicDialog} this
     */
    setDefaultButton : function(btn){
        this.defaultButton = btn;
        return this;
    },

    // private
    getHeaderFooterHeight : function(safe){
        var height = 0;
        if(this.header){
           height += this.header.getHeight();
        }
        if(this.footer){
           var fm = this.footer.getMargins();
            height += (this.footer.getHeight()+fm.top+fm.bottom);
        }
        height += this.bwrap.getPadding("tb")+this.bwrap.getBorderWidth("tb");
        height += this.centerBg.getPadding("tb");
        return height;
    },

    // private
    syncBodyHeight : function(){
        var bd = this.body, cb = this.centerBg, bw = this.bwrap;
        var height = this.size.height - this.getHeaderFooterHeight(false);
        bd.setHeight(height-bd.getMargins("tb"));
        var hh = this.header.getHeight();
        var h = this.size.height-hh;
        cb.setHeight(h);
        bw.setLeftTop(cb.getPadding("l"), hh+cb.getPadding("t"));
        bw.setHeight(h-cb.getPadding("tb"));
        bw.setWidth(this.el.getWidth(true)-cb.getPadding("lr"));
        bd.setWidth(bw.getWidth(true));
        if(this.tabs){
            this.tabs.syncHeight();
            if(Ext.isIE){
                this.tabs.el.repaint();
            }
        }
    },

    /**
     * Restores the previous state of the dialog if Ext.state is configured.
     * @return {Ext.BasicDialog} this
     */
    restoreState : function(){
        var box = Ext.state.Manager.get(this.stateId || (this.el.id + "-state"));
        if(box && box.width){
            this.xy = [box.x, box.y];
            this.resizeTo(box.width, box.height);
        }
        return this;
    },

    // private
    beforeShow : function(){
        this.expand();
        if(this.fixedcenter){
            this.xy = this.el.getCenterXY(true);
        }
        if(this.modal){
            Ext.get(document.body).addClass("x-body-masked");
            this.mask.setSize(Ext.lib.Dom.getViewWidth(true), Ext.lib.Dom.getViewHeight(true));
            this.mask.show();
        }
        this.constrainXY();
    },

    // private
    animShow : function(){
        var b = Ext.get(this.animateTarget, true).getBox();
        this.proxy.setSize(b.width, b.height);
        this.proxy.setLocation(b.x, b.y);
        this.proxy.show();
        this.proxy.setBounds(this.xy[0], this.xy[1], this.size.width, this.size.height,
                    true, .35, this.showEl.createDelegate(this));
    },

    /**
     * Shows the dialog.
     * @param {String/HTMLElement/Ext.Element} animateTarget (optional) Reset the animation target
     * @return {Ext.BasicDialog} this
     */
    show : function(animateTarget){
        if (this.fireEvent("beforeshow", this) === false){
            return;
        }
        if(this.syncHeightBeforeShow){
            this.syncBodyHeight();
        }else if(this.firstShow){
            this.firstShow = false;
            this.syncBodyHeight(); // sync the height on the first show instead of in the constructor
        }
        this.animateTarget = animateTarget || this.animateTarget;
        if(!this.el.isVisible()){
            this.beforeShow();
            if(this.animateTarget){
                this.animShow();
            }else{
                this.showEl();
            }
        }
        return this;
    },

    // private
    showEl : function(){
        this.proxy.hide();
        this.el.setXY(this.xy);
        this.el.show();
        this.adjustAssets(true);
        this.toFront();
        this.focus();
        // IE peekaboo bug - fix found by Dave Fenwick
        if(Ext.isIE){
            this.el.repaint();
        }
        this.fireEvent("show", this);
    },

    /**
     * Focuses the dialog.  If a defaultButton is set, it will receive focus, otherwise the
     * dialog itself will receive focus.
     */
    focus : function(){
        if(this.defaultButton){
            this.defaultButton.focus();
        }else{
            this.focusEl.focus();
        }
    },

    // private
    constrainXY : function(){
        if(this.constraintoviewport !== false){
            if(!this.viewSize){
                if(this.container){
                    var s = this.container.getSize();
                    this.viewSize = [s.width, s.height];
                }else{
                    this.viewSize = [Ext.lib.Dom.getViewWidth(),Ext.lib.Dom.getViewHeight()];
                }
            }
            var s = Ext.get(this.container||document).getScroll();

            var x = this.xy[0], y = this.xy[1];
            var w = this.size.width, h = this.size.height;
            var vw = this.viewSize[0], vh = this.viewSize[1];
            // only move it if it needs it
            var moved = false;
            // first validate right/bottom
            if(x + w > vw+s.left){
                x = vw - w;
                moved = true;
            }
            if(y + h > vh+s.top){
                y = vh - h;
                moved = true;
            }
            // then make sure top/left isn't negative
            if(x < s.left){
                x = s.left;
                moved = true;
            }
            if(y < s.top){
                y = s.top;
                moved = true;
            }
            if(moved){
                // cache xy
                this.xy = [x, y];
                if(this.isVisible()){
                    this.el.setLocation(x, y);
                    this.adjustAssets();
                }
            }
        }
    },

    // private
    onDrag : function(){
        if(!this.proxyDrag){
            this.xy = this.el.getXY();
            this.adjustAssets();
        }
    },

    // private
    adjustAssets : function(doShow){
        var x = this.xy[0], y = this.xy[1];
        var w = this.size.width, h = this.size.height;
        if(doShow === true){
            if(this.shadow){
                this.shadow.show(this.el);
            }
            if(this.shim){
                this.shim.show();
            }
        }
        if(this.shadow && this.shadow.isVisible()){
            this.shadow.show(this.el);
        }
        if(this.shim && this.shim.isVisible()){
            this.shim.setBounds(x, y, w, h);
        }
    },

    // private
    adjustViewport : function(w, h){
        if(!w || !h){
            w = Ext.lib.Dom.getViewWidth();
            h = Ext.lib.Dom.getViewHeight();
        }
        // cache the size
        this.viewSize = [w, h];
        if(this.modal && this.mask.isVisible()){
            this.mask.setSize(w, h); // first make sure the mask isn't causing overflow
            this.mask.setSize(Ext.lib.Dom.getViewWidth(true), Ext.lib.Dom.getViewHeight(true));
        }
        if(this.isVisible()){
            this.constrainXY();
        }
    },

    /**
     * Destroys this dialog and all its supporting elements (including any tabs, shim,
     * shadow, proxy, mask, etc.)  Also removes all event listeners.
     * @param {Boolean} removeEl (optional) true to remove the element from the DOM
     */
    destroy : function(removeEl){
        if(this.isVisible()){
            this.animateTarget = null;
            this.hide();
        }
        Ext.EventManager.removeResizeListener(this.adjustViewport, this);
        if(this.tabs){
            this.tabs.destroy(removeEl);
        }
        Ext.destroy(
             this.shim,
             this.proxy,
             this.resizer,
             this.close,
             this.mask
        );
        if(this.dd){
            this.dd.unreg();
        }
        if(this.buttons){
           for(var i = 0, len = this.buttons.length; i < len; i++){
               this.buttons[i].destroy();
           }
        }
        this.el.removeAllListeners();
        if(removeEl === true){
            this.el.update("");
            this.el.remove();
        }
        Ext.DialogManager.unregister(this);
    },

    // private
    startMove : function(){
        if(this.proxyDrag){
            this.proxy.show();
        }
        if(this.constraintoviewport !== false){
            this.dd.constrainTo(document.body, {right: this.shadowOffset, bottom: this.shadowOffset});
        }
    },

    // private
    endMove : function(){
        if(!this.proxyDrag){
            Ext.dd.DD.prototype.endDrag.apply(this.dd, arguments);
        }else{
            Ext.dd.DDProxy.prototype.endDrag.apply(this.dd, arguments);
            this.proxy.hide();
        }
        this.refreshSize();
        this.adjustAssets();
        this.focus();
        this.fireEvent("move", this, this.xy[0], this.xy[1]);
    },

    /**
     * Brings this dialog to the front of any other visible dialogs
     * @return {Ext.BasicDialog} this
     */
    toFront : function(){
        Ext.DialogManager.bringToFront(this);
        return this;
    },

    /**
     * Sends this dialog to the back (under) of any other visible dialogs
     * @return {Ext.BasicDialog} this
     */
    toBack : function(){
        Ext.DialogManager.sendToBack(this);
        return this;
    },

    /**
     * Centers this dialog in the viewport
     * @return {Ext.BasicDialog} this
     */
    center : function(){
        var xy = this.el.getCenterXY(true);
        this.moveTo(xy[0], xy[1]);
        return this;
    },

    /**
     * Moves the dialog's top-left corner to the specified point
     * @param {Number} x
     * @param {Number} y
     * @return {Ext.BasicDialog} this
     */
    moveTo : function(x, y){
        this.xy = [x,y];
        if(this.isVisible()){
            this.el.setXY(this.xy);
            this.adjustAssets();
        }
        return this;
    },

    /**
     * Aligns the dialog to the specified element
     * @param {String/HTMLElement/Ext.Element} element The element to align to.
     * @param {String} position The position to align to (see {@link Ext.Element#alignTo} for more details).
     * @param {Array} offsets (optional) Offset the positioning by [x, y]
     * @return {Ext.BasicDialog} this
     */
    alignTo : function(element, position, offsets){
        this.xy = this.el.getAlignToXY(element, position, offsets);
        if(this.isVisible()){
            this.el.setXY(this.xy);
            this.adjustAssets();
        }
        return this;
    },

    /**
     * Anchors an element to another element and realigns it when the window is resized.
     * @param {String/HTMLElement/Ext.Element} element The element to align to.
     * @param {String} position The position to align to (see {@link Ext.Element#alignTo} for more details)
     * @param {Array} offsets (optional) Offset the positioning by [x, y]
     * @param {Boolean/Number} monitorScroll (optional) true to monitor body scroll and reposition. If this parameter
     * is a number, it is used as the buffer delay (defaults to 50ms).
     * @return {Ext.BasicDialog} this
     */
    anchorTo : function(el, alignment, offsets, monitorScroll){
        var action = function(){
            this.alignTo(el, alignment, offsets);
        };
        Ext.EventManager.onWindowResize(action, this);
        var tm = typeof monitorScroll;
        if(tm != 'undefined'){
            Ext.EventManager.on(window, 'scroll', action, this,
                {buffer: tm == 'number' ? monitorScroll : 50});
        }
        action.call(this);
        return this;
    },

    /**
     * Returns true if the dialog is visible
     * @return {Boolean}
     */
    isVisible : function(){
        return this.el.isVisible();
    },

    // private
    animHide : function(callback){
        var b = Ext.get(this.animateTarget).getBox();
        this.proxy.show();
        this.proxy.setBounds(this.xy[0], this.xy[1], this.size.width, this.size.height);
        this.el.hide();
        this.proxy.setBounds(b.x, b.y, b.width, b.height, true, .35,
                    this.hideEl.createDelegate(this, [callback]));
    },

    /**
     * Hides the dialog.
     * @param {Function} callback (optional) Function to call when the dialog is hidden
     * @return {Ext.BasicDialog} this
     */
    hide : function(callback){
        if (this.fireEvent("beforehide", this) === false){
            return;
        }
        if(this.shadow){
            this.shadow.hide();
        }
        if(this.shim) {
          this.shim.hide();
        }
        if(this.animateTarget){
           this.animHide(callback);
        }else{
            this.el.hide();
            this.hideEl(callback);
        }
        return this;
    },

    // private
    hideEl : function(callback){
        this.proxy.hide();
        if(this.modal){
            this.mask.hide();
            Ext.get(document.body).removeClass("x-body-masked");
        }
        this.fireEvent("hide", this);
        if(typeof callback == "function"){
            callback();
        }
    },

    // private
    hideAction : function(){
        this.setLeft("-10000px");
        this.setTop("-10000px");
        this.setStyle("visibility", "hidden");
    },

    // private
    refreshSize : function(){
        this.size = this.el.getSize();
        this.xy = this.el.getXY();
        Ext.state.Manager.set(this.stateId || this.el.id + "-state", this.el.getBox());
    },

    // private
    // z-index is managed by the DialogManager and may be overwritten at any time
    setZIndex : function(index){
        if(this.modal){
            this.mask.setStyle("z-index", index);
        }
        if(this.shim){
            this.shim.setStyle("z-index", ++index);
        }
        if(this.shadow){
            this.shadow.setZIndex(++index);
        }
        this.el.setStyle("z-index", ++index);
        if(this.proxy){
            this.proxy.setStyle("z-index", ++index);
        }
        if(this.resizer){
            this.resizer.proxy.setStyle("z-index", ++index);
        }

        this.lastZIndex = index;
    },

    /**
     * Returns the element for this dialog
     * @return {Ext.Element} The underlying dialog Element
     */
    getEl : function(){
        return this.el;
    }
});

/**
 * @class Ext.DialogManager
 * Provides global access to BasicDialogs that have been created and
 * support for z-indexing (layering) multiple open dialogs.
 */
Ext.DialogManager = function(){
    var list = {};
    var accessList = [];
    var front = null;

    // private
    var sortDialogs = function(d1, d2){
        return (!d1._lastAccess || d1._lastAccess < d2._lastAccess) ? -1 : 1;
    };

    // private
    var orderDialogs = function(){
        accessList.sort(sortDialogs);
        var seed = Ext.DialogManager.zseed;
        for(var i = 0, len = accessList.length; i < len; i++){
            var dlg = accessList[i];
            if(dlg){
                dlg.setZIndex(seed + (i*10));
            }
        }
    };

    return {
        /**
         * The starting z-index for BasicDialogs (defaults to 9000)
         * @type Number The z-index value
         */
        zseed : 9000,

        // private
        register : function(dlg){
            list[dlg.id] = dlg;
            accessList.push(dlg);
        },

        // private
        unregister : function(dlg){
            delete list[dlg.id];
            if(!accessList.indexOf){
                for(var i = 0, len = accessList.length; i < len; i++){
                    if(accessList[i] == dlg){
                        accessList.splice(i, 1);
                        return;
                    }
                }
            }else{
                var i = accessList.indexOf(dlg);
                if(i != -1){
                    accessList.splice(i, 1);
                }
            }
        },

        /**
         * Gets a registered dialog by id
         * @param {String/Object} id The id of the dialog or a dialog
         * @return {Ext.BasicDialog} this
         */
        get : function(id){
            return typeof id == "object" ? id : list[id];
        },

        /**
         * Brings the specified dialog to the front
         * @param {String/Object} dlg The id of the dialog or a dialog
         * @return {Ext.BasicDialog} this
         */
        bringToFront : function(dlg){
            dlg = this.get(dlg);
            if(dlg != front){
                front = dlg;
                dlg._lastAccess = new Date().getTime();
                orderDialogs();
            }
            return dlg;
        },

        /**
         * Sends the specified dialog to the back
         * @param {String/Object} dlg The id of the dialog or a dialog
         * @return {Ext.BasicDialog} this
         */
        sendToBack : function(dlg){
            dlg = this.get(dlg);
            dlg._lastAccess = -(new Date().getTime());
            orderDialogs();
            return dlg;
        },

        /**
         * Hides all dialogs
         */
        hideAll : function(){
            for(var id in list){
                if(list[id] && typeof list[id] != "function" && list[id].isVisible()){
                    list[id].hide();
                }
            }
        }
    };
}();

/**
 * @class Ext.LayoutDialog
 * @extends Ext.BasicDialog
 * Dialog which provides adjustments for working with a layout in a Dialog.
 * Add your necessary layout config options to the dialog's config.<br>
 * Example usage (including a nested layout):
 * <pre><code>
if(!dialog){
    dialog = new Ext.LayoutDialog("download-dlg", {
        modal: true,
        width:600,
        height:450,
        shadow:true,
        minWidth:500,
        minHeight:350,
        autoTabs:true,
        proxyDrag:true,
        // layout config merges with the dialog config
        center:{
            tabPosition: "top",
            alwaysShowTabs: true
        }
    });
    dialog.addKeyListener(27, dialog.hide, dialog);
    dialog.setDefaultButton(dialog.addButton("Close", dialog.hide, dialog));
    dialog.addButton("Build It!", this.getDownload, this);

    // we can even add nested layouts
    var innerLayout = new Ext.BorderLayout("dl-inner", {
        east: {
            initialSize: 200,
            autoScroll:true,
            split:true
        },
        center: {
            autoScroll:true
        }
    });
    innerLayout.beginUpdate();
    innerLayout.add("east", new Ext.ContentPanel("dl-details"));
    innerLayout.add("center", new Ext.ContentPanel("selection-panel"));
    innerLayout.endUpdate(true);

    var layout = dialog.getLayout();
    layout.beginUpdate();
    layout.add("center", new Ext.ContentPanel("standard-panel",
                        {title: "Download the Source", fitToFrame:true}));
    layout.add("center", new Ext.NestedLayoutPanel(innerLayout,
               {title: "Build your own ext.js"}));
    layout.getRegion("center").showPanel(sp);
    layout.endUpdate();
}
</code></pre>
    * @constructor
    * @param {String/HTMLElement/Ext.Element} el The id of or container element
    * @param {Object} config configuration options
  */
Ext.LayoutDialog = function(el, config){
    config.autoTabs = false;
    Ext.LayoutDialog.superclass.constructor.call(this, el, config);
    this.body.setStyle({overflow:"hidden", position:"relative"});
    this.layout = new Ext.BorderLayout(this.body.dom, config);
    this.layout.monitorWindowResize = false;
    this.el.addClass("x-dlg-auto-layout");
    // fix case when center region overwrites center function
    this.center = Ext.BasicDialog.prototype.center;
    this.on("show", this.layout.layout, this.layout, true);
};
Ext.extend(Ext.LayoutDialog, Ext.BasicDialog, {
    /**
     * Ends update of the layout <strike>and resets display to none</strike>. Use standard beginUpdate/endUpdate on the layout.
     * @deprecated
     */
    endUpdate : function(){
        this.layout.endUpdate();
    },

    /**
     * Begins an update of the layout <strike>and sets display to block and visibility to hidden</strike>. Use standard beginUpdate/endUpdate on the layout.
     *  @deprecated
     */
    beginUpdate : function(){
        this.layout.beginUpdate();
    },

    /**
     * Get the BorderLayout for this dialog
     * @return {Ext.BorderLayout}
     */
    getLayout : function(){
        return this.layout;
    },

    showEl : function(){
        Ext.LayoutDialog.superclass.showEl.apply(this, arguments);
        if(Ext.isIE7){
            this.layout.layout();
        }
    },

    // private
    // Use the syncHeightBeforeShow config option to control this automatically
    syncBodyHeight : function(){
        Ext.LayoutDialog.superclass.syncBodyHeight.call(this);
        if(this.layout){this.layout.layout();}
    }
});
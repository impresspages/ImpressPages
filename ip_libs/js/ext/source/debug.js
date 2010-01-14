/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/*
 * These functions are only included in -debug files
 *
*/Ext.debug = {
    init : function(){
        var CP = Ext.ContentPanel;
        var bd = Ext.get(document.body);

        // create the dialog
        var dlg = new Ext.LayoutDialog('x-debug-browser', {
            autoCreate:true,
            width:800,
            height:450,
            title: 'Ext Debug Console &amp; Inspector',
            proxyDrag:true,
            shadow:true,
            center:{alwaysShowTabs:true},
            constraintoviewport:false
        });

        // prevent dialog events from bubbling
        dlg.el.swallowEvent('click');

        // build the layout
        var mainLayout = dlg.getLayout();
        mainLayout.beginUpdate();

        // create the nested layouts
        var clayout = mainLayout.add('center',
            new Ext.debug.InnerLayout('x-debug-console', 400, {
                title: 'Debug Console'
            }
        ));

        var ilayout = mainLayout.add('center',
            new Ext.debug.InnerLayout('x-debug-inspector', 250, {
                title: 'DOM Inspector'
            }
        ));

        var scriptPanel = clayout.add('east', new CP({
            autoCreate:{
                tag: 'div', children: [
                    {tag: 'div'},
                    {tag:'textarea'}
                ]
            },
            fitContainer:true,
            fitToFrame:true,
            title:'Script Console',
            autoScroll: Ext.isGecko, // hideous block for firefox missing cursor AND bad sizing textareas
            setSize : function(w, h){
                Ext.ContentPanel.prototype.setSize.call(this, w, h);
                if(Ext.isGecko && Ext.isStrict){
                    var s = this.adjustForComponents(w, h);
                    this.resizeEl.setSize(s.width-2, s.height-2);
                }
            }
        }));
        var sel = scriptPanel.el;
        var script = sel.child('textarea');
        scriptPanel.resizeEl = script;
        var sctb = scriptPanel.toolbar = new Ext.Toolbar(sel.child('div'));
        sctb.add({
            text: 'Run',
            handler: function(){
                var s = script.dom.value;
                if(trap.checked){
                    try{
                        var rt = eval(s);
                        Ext.debug.dump(rt === undefined? '(no return)' : rt);
                    }catch(e){
                        Ext.debug.log(e.message || e.descript);
                    }
                }else{
                    var rt = eval(s);
                    Ext.debug.dump(rt === undefined? '(no return)' : rt);
                }
            }
        }, {
            text: 'Clear',
            handler: function(){
                script.dom.value = '';
                script.dom.focus();
            }
        });

        var trap = Ext.DomHelper.append(sctb.el, {tag:'input', type:'checkbox', checked: 'checked'});
        trap.checked = true;
        sctb.add('-', trap, 'Trap Errors');


        var stylesGrid = new Ext.grid.PropertyGrid(bd.createChild(), {
            nameText: 'Style',
            enableHdMenu: false,
            enableColumnResize: false
        });

        var stylePanel = ilayout.add('east', new Ext.GridPanel(stylesGrid,
            {title: '(No element selected)'}));

        stylesGrid.render();

        // hide the header
        stylesGrid.getView().mainHd.setDisplayed(false);

        clayout.tbar.add({
            text: 'Clear',
            handler: function(){
                Ext.debug.console.jsonData = [];
                Ext.debug.console.refresh();
            }
        });

        var treeEl = ilayout.main.getEl();
        // create main inspector toolbar
        var tb = ilayout.tbar;

        var inspectIgnore, inspecting;

        function inspectListener(e, t){
            if(!inspectIgnore.contains(e.getPoint())){
                findNode(t);
            }
        }

        function stopInspecting(e, t){
            if(!inspectIgnore.contains(e.getPoint())){
                inspect.toggle(false);
                if(findNode(t) !== false){
                    e.stopEvent();
                }
            }
        }

        function stopInspectingEsc(e, t){
            if(e.getKey() == e.ESC){
                inspect.toggle(false);
            }
        }

        var inspect = tb.addButton({
            text: 'Inspect',
            enableToggle: true,
            pressed:false,
            toggleHandler: function(n, pressed){
                var d = Ext.get(document);
                if(pressed){
                    d.on('mouseover', inspectListener, window, {buffer:50});
                    d.on('mousedown', stopInspecting);
                    d.on('keydown', stopInspectingEsc);
                    inspectIgnore = dlg.el.getRegion();
                    inspecting = true;
                }else{
                    d.un('mouseover', inspectListener);
                    d.un('mousedown', stopInspecting);
                    d.on('keydown', stopInspectingEsc);
                    inspecting = false;
                    var n = tree.getSelectionModel().getSelectedNode();
                    if(n && n.htmlNode){
                        onNodeSelect(tree, n, false);
                    }
                }
            }
        });

        tb.addSeparator();

        var frameEl = tb.addButton({
            text: 'Highlight Selection',
            enableToggle: true,
            pressed:false,
            toggleHandler: function(n, pressed){
                var n = tree.getSelectionModel().getSelectedNode();
                if(n && n.htmlNode){
                    n[pressed ? 'frame' : 'unframe']();
                }
            }
        });

        tb.addSeparator();

        var reload = tb.addButton({
            text: 'Refresh Children',
            disabled:true,
            handler: function(){
                var n = tree.getSelectionModel().getSelectedNode();
                if(n && n.reload){
                    n.reload();
                }
            }
        });

        tb.add( '-', {
            text: 'Collapse All',
            handler: function(){
                tree.root.collapse(true);
            }
        });

        // perform the main layout
        mainLayout.endUpdate();

        mainLayout.getRegion('center').showPanel(0);

        stylesGrid.on('propertychange', function(s, name, value){
            var node = stylesGrid.treeNode;
            if(styles){
                node.htmlNode.style[name] = value;
            }else{
                node.htmlNode[name] = value;
            }
            node.refresh(true);
        });

        // Create the style toolbar
        var stb = new Ext.Toolbar(stylesGrid.view.getHeaderPanel(true));

        var swap = stb.addButton({
            text: 'DOM Attributes',
            menu: {
                items: [
                    new Ext.menu.CheckItem({id:'dom', text:'DOM Attributes', checked: true, group:'xdb-styles'}),
                    new Ext.menu.CheckItem({id:'styles', text:'CSS Properties', group:'xdb-styles'})
                ]
            }
        });

        swap.menu.on('click', function(){
            styles = swap.menu.items.get('styles').checked;
            showAll[styles? 'show' : 'hide']();
            swap.setText(styles ? 'CSS Properties' : 'DOM Attributes');
            var n = tree.getSelectionModel().getSelectedNode();
            if(n){
                onNodeSelect(tree, n);
            }
        });
        
        var addStyle = stb.addButton({
            text: 'Add',
            disabled: true,
            handler: function(){
                Ext.MessageBox.prompt('Add Property', 'Property Name:', function(btn, v){
                    // store.store is disgusting TODO: clean up the API
                    var store = stylesGrid.store.store;
                    if(btn == 'ok' && v && !store.getById(v)){
                        var r = new Ext.grid.PropertyRecord({name:v, value: ''}, v);
                        store.add(r);
                        stylesGrid.startEditing(store.getCount()-1, 1);
                    }
                });
            }
        });

        var showAll = stb.addButton({
            text: 'Computed Styles',
            hidden: true,
            pressed: false,
            enableToggle: true,
            toggleHandler: function(){
                var n = tree.getSelectionModel().getSelectedNode();
                if(n){
                    onNodeSelect(tree, n);
                }
            }
        });

        // tree related stuff
        var styles = false, hnode;
        var nonSpace = /^\s*$/;
        var html = Ext.util.Format.htmlEncode;
        var ellipsis = Ext.util.Format.ellipsis;
        var styleRe = /\s?([a-z\-]*)\:([^;]*)(?:[;\s\n\r]*)/gi;

        function findNode(n){
            if(!n || n.nodeType != 1 || n == document.body || n == document){
                return false;
            }
            var pn = [n], p = n;
            while((p = p.parentNode) && p.nodeType == 1 && p.tagName.toUpperCase() != 'HTML'){
                pn.unshift(p);
            }
            var cn = hnode;
            for(var i = 0, len = pn.length; i < len; i++){
                cn.expand();
                cn = cn.findChild('htmlNode', pn[i]);
                if(!cn){ // in this dialog?
                    return false;
                }
            }
            cn.select();
            var a = cn.ui.anchor;
            treeEl.dom.scrollTop = Math.max(0 ,a.offsetTop-10);
            //treeEl.dom.scrollLeft = Math.max(0 ,a.offsetLeft-10); no likey
            cn.highlight();
            return true;
        }

        function nodeTitle(n){
            var s = n.tagName;
            if(n.id){
                s += '#'+n.id;
            }else if(n.className){
                s += '.'+n.className;
            }
            return s;
        }

        function onNodeSelect(t, n, last){
            if(last && last.unframe){
                last.unframe();
            }
            var props = {};
            if(n && n.htmlNode){
                if(frameEl.pressed){
                    n.frame();
                }
                if(inspecting){
                    return;
                }
                addStyle.enable();
                reload.setDisabled(n.leaf);
                var dom = n.htmlNode;
                stylePanel.setTitle(nodeTitle(dom));
                if(styles && !showAll.pressed){
                    var s = dom.style ? dom.style.cssText : '';
                    if(s){
                        var m;
                        while ((m = styleRe.exec(s)) != null){
                            props[m[1].toLowerCase()] = m[2];
                        }
                    }
                }else if(styles){
                    var cl = Ext.debug.cssList;
                    var s = dom.style, fly = Ext.fly(dom);
                    if(s){
                        for(var i = 0, len = cl.length; i<len; i++){
                            var st = cl[i];
                            var v = s[st] || fly.getStyle(st);
                            if(v != undefined && v !== null && v !== ''){
                                props[st] = v;
                            }
                        }
                    }
                }else{
                    for(var a in dom){
                        var v = dom[a];
                        if((isNaN(a+10)) && v != undefined && v !== null && v !== '' && !(Ext.isGecko && a[0] == a[0].toUpperCase())){
                            props[a] = v;
                        }
                    }
                }
            }else{
                if(inspecting){
                    return;
                }
                addStyle.disable();
                reload.disabled();
            }
            stylesGrid.setSource(props);
            stylesGrid.treeNode = n;
            stylesGrid.view.fitColumns();
        }

        // lets build a list of nodes to filter from the tree
        // this is gonna be nasty
        var filterIds = '^(?:';
        var eds = stylesGrid.colModel.editors;
        for(var edType in eds){
            filterIds += eds[edType].id +'|';
        }
        Ext.each([dlg.shim? dlg.shim.id : 'noshim', dlg.proxyDrag.id], function(id){
             filterIds += id +'|';
        });
        filterIds += dlg.el.id;
        filterIds += ')$';
        var filterRe = new RegExp(filterIds);

        var loader = new Ext.tree.TreeLoader();
        loader.load = function(n, cb){
            var isBody = n.htmlNode == bd.dom;
            var cn = n.htmlNode.childNodes;
            for(var i = 0, c; c = cn[i]; i++){
                if(isBody && filterRe.test(c.id)){
                    continue;
                }
                if(c.nodeType == 1){
                    n.appendChild(new Ext.debug.HtmlNode(c));
                }else if(c.nodeType == 3 && !nonSpace.test(c.nodeValue)){
                    n.appendChild(new Ext.tree.TreeNode({
                        text:'<em>' + ellipsis(html(String(c.nodeValue)), 35) + '</em>',
                        cls: 'x-tree-noicon'
                    }));
                }
            }
            cb();
        };

        var tree = new Ext.tree.TreePanel(treeEl, {
            enableDD:false ,
            loader: loader,
            lines:false,
            rootVisible:false,
            animate:false,
            hlColor:'ffff9c'
        });
        tree.getSelectionModel().on('selectionchange', onNodeSelect, null, {buffer:250});

        var root = tree.setRootNode(new Ext.tree.TreeNode('Ext'));

        hnode = root.appendChild(new Ext.debug.HtmlNode(
                document.getElementsByTagName('html')[0]
        ));

        tree.render();

        Ext.debug.console = new Ext.JsonView(clayout.main.getEl(),
                '<pre><xmp>> {msg}</xmp></pre>');
        Ext.debug.console.jsonData = [];

        Ext.debug.dialog = dlg;
    },

    show : function(){
        var d = Ext.debug;
        if(!d.dialog){
            d.init();
        }
        if(!d.dialog.isVisible()){
            d.dialog.show();
        }
    },

    hide : function(){
        if(Ext.debug.dialog){
            Ext.debug.dialog.hide();
        }
    },

    /**
     * Debugging function. Prints all arguments to a resizable, movable, scrolling region without
     * the need to include separate js or css. Double click it to hide it.
     * @param {Mixed} arg1
     * @param {Mixed} arg2
     * @param {Mixed} etc
     * @method print
     */
    log : function(arg1, arg2, etc){
       Ext.debug.show();
        var m = "";
        for(var i = 0, len = arguments.length; i < len; i++){
            m += (i == 0 ? "" : ", ") + arguments[i];
        }
        var cn = Ext.debug.console;
        cn.jsonData.unshift({msg: m});
        cn.refresh();
    },

    /**
     * Applies the passed C#/DomHelper style format (e.g. "The variable {0} is equal to {1}") before calling Ext.debug.log
     * @param {String} format
     * @param {Mixed} arg1
     * @param {Mixed} arg2
     * @param {Mixed} etc
     * @method printf
     */
    logf : function(format, arg1, arg2, etc){
        Ext.debug.log(String.format.apply(String, arguments));
    },

    /**
     * Dumps an object to Ext.debug.log
     * @param {Object} o
     * @method dump
     */
    dump : function(o){
        if(typeof o == 'string' || typeof o == 'number' || typeof o == 'undefined' || o instanceof Date){
            Ext.debug.log(o);
        }else if(!o){
            Ext.debug.log("null");
        }else if(typeof o != "object"){
            Ext.debug.log('Unknown return type');
        }else if(o instanceof Array){
            Ext.debug.log('['+o.join(',')+']');
        }else{
            var b = ["{\n"];
            for(var key in o){
                var to = typeof o[key];
                if(to != "function" && to != "object"){
                    b.push(String.format("  {0}: {1},\n", key, o[key]));
                }
            }
            var s = b.join("");
            if(s.length > 3){
                s = s.substr(0, s.length-2);
            }
            Ext.debug.log(s + "\n}");
        }
    },

    _timers : {},
    /**
     * Starts a timer.
     * @param {String} name (optional)
     * @method timer
     */
    time : function(name){
        name = name || "def";
        Ext.debug._timers[name] = new Date().getTime();
    },

    /**
     * Ends a timer, returns the results (formatted "{1} ms") and optionally prints them to Ext.print()
     * @param {String} name (optional)
     * @param {Boolean} printResults (optional) false to stop printing the results to Ext.print
     * @method timerEnd
     */
    timeEnd : function(name, printResults){
        var t = new Date().getTime();
        name = name || "def";
        var v = String.format("{0} ms", t-Ext.debug._timers[name]);
        Ext.debug._timers[name] = new Date().getTime();
        if(printResults !== false){
            Ext.debug.log('Timer ' + (name == "def" ? v : name + ": " + v));
        }
        return v;
    }
};

// highly unusual class declaration
Ext.debug.HtmlNode = function(){
    var html = Ext.util.Format.htmlEncode;
    var ellipsis = Ext.util.Format.ellipsis;
    var nonSpace = /^\s*$/;

    var attrs = [
        {n: 'id', v: 'id'},
        {n: 'className', v: 'class'},
        {n: 'name', v: 'name'},
        {n: 'type', v: 'type'},
        {n: 'src', v: 'src'},
        {n: 'href', v: 'href'}
    ];

    function hasChild(n){
        for(var i = 0, c; c = n.childNodes[i]; i++){
            if(c.nodeType == 1){
                return true;
            }
        }
        return false;
    }

    function renderNode(n, leaf){
        var tag = n.tagName.toLowerCase();
        var s = '&lt;' + tag;
        for(var i = 0, len = attrs.length; i < len; i++){
            var a = attrs[i];
            var v = n[a.n];
            if(v && !nonSpace.test(v)){
                s += ' ' + a.v + '=&quot;<i>' + html(v) +'</i>&quot;';
            }
        }
        var style = n.style ? n.style.cssText : '';
        if(style){
            s += ' style=&quot;<i>' + html(style.toLowerCase()) +'</i>&quot;';
        }
        if(leaf && n.childNodes.length > 0){
            s+='&gt;<em>' + ellipsis(html(String(n.innerHTML)), 35) + '</em>&lt;/'+tag+'&gt;';
        }else if(leaf){
            s += ' /&gt;';
        }else{
            s += '&gt;';
        }
        return s;
    }

    var HtmlNode = function(n){
        var leaf = !hasChild(n);
        this.htmlNode = n;
        this.tagName = n.tagName.toLowerCase();
        var attr = {
            text : renderNode(n, leaf),
            leaf : leaf,
            cls: 'x-tree-noicon'
        };
        HtmlNode.superclass.constructor.call(this, attr);
        this.attributes.htmlNode = n; // for searching
        if(!leaf){
            this.on('expand', this.onExpand,  this);
            this.on('collapse', this.onCollapse,  this);
        }
    };


    Ext.extend(HtmlNode, Ext.tree.AsyncTreeNode, {
        cls: 'x-tree-noicon',
        preventHScroll: true,
        refresh : function(highlight){
            var leaf = !hasChild(this.htmlNode);
            this.setText(renderNode(this.htmlNode, leaf));
            if(highlight){
                Ext.fly(this.ui.textNode).highlight();
            }
        },

        onExpand : function(){
            if(!this.closeNode && this.parentNode){
                this.closeNode = this.parentNode.insertBefore(new Ext.tree.TreeNode({
                    text:'&lt;/' + this.tagName + '&gt;',
                    cls: 'x-tree-noicon'
                }), this.nextSibling);
            }else if(this.closeNode){
                this.closeNode.ui.show();
            }
        },

        onCollapse : function(){
            if(this.closeNode){
                this.closeNode.ui.hide();
            }
        },

        render : function(bulkRender){
            HtmlNode.superclass.render.call(this, bulkRender);
        },

        highlightNode : function(){
            //Ext.fly(this.htmlNode).highlight();
        },

        highlight : function(){
            //Ext.fly(this.ui.textNode).highlight();
        },

        frame : function(){
            this.htmlNode.style.border = '1px solid #0000ff';
            //this.highlightNode();
        },

        unframe : function(){
            //Ext.fly(this.htmlNode).removeClass('x-debug-frame');
            this.htmlNode.style.border = '';
        }
    });

    return HtmlNode;
}();

// subclass for the standard layout panels
Ext.debug.InnerLayout = function(id, w, cfg){
    // console layout
    var el = Ext.DomHelper.append(document.body, {id:id});
    var layout = new Ext.BorderLayout(el, {
        north: {
            initialSize:28
        },
        center: {
            titlebar: false
        },
        east: {
            split:true,
            initialSize:w,
            titlebar:true
        }
    });
    Ext.debug.InnerLayout.superclass.constructor.call(this, layout, cfg);

    layout.beginUpdate();

    var tbPanel = layout.add('north', new Ext.ContentPanel({
            autoCreate:true, fitToFrame:true}));

    this.main = layout.add('center', new Ext.ContentPanel({
            autoCreate:true, fitToFrame:true, autoScroll:true}));

    this.tbar = new Ext.Toolbar(tbPanel.el);

    var mtbEl = tbPanel.resizeEl = tbPanel.el.child('div.x-toolbar');
    mtbEl.setStyle('border-bottom', '0 none');
    layout.endUpdate(true);
};

Ext.extend(Ext.debug.InnerLayout, Ext.NestedLayoutPanel, {
    add : function(){
        return this.layout.add.apply(this.layout, arguments);
    }
});

Ext.debug.cssList = ['background-color','border','border-color','border-spacing',
'border-style','border-top','border-right','border-bottom','border-left','border-top-color',
'border-right-color','border-bottom-color','border-left-color','border-top-width','border-right-width',
'border-bottom-width','border-left-width','border-width','bottom','color','font-size','font-size-adjust',
'font-stretch','font-style','height','left','letter-spacing','line-height','margin','margin-top',
'margin-right','margin-bottom','margin-left','marker-offset','max-height','max-width','min-height',
'min-width','orphans','outline','outline-color','outline-style','outline-width','overflow','padding',
'padding-top','padding-right','padding-bottom','padding-left','quotes','right','size','text-indent',
'top','width','word-spacing','z-index','opacity','outline-offset'];

if(typeof console == 'undefined'){
    console = Ext.debug;
}
/*
if(Ext.isSafari || Ext.isIE || Ext.isOpera){
    window.onerror = function(msg, url, line){
        Ext.log.apply(Ext, arguments);
    };
}*/

// attach shortcut key
Ext.EventManager.on(window, 'load', function(){
    Ext.get(document).on('keydown', function(e){
        if(e.ctrlKey && e.shiftKey && e.getKey() == e.HOME){
            Ext.debug.show();
        }
    });
});

// backwards compat
Ext.print = Ext.log = Ext.debug.log;
Ext.printf = Ext.logf = Ext.debug.logf;
Ext.dump = Ext.debug.dump;
Ext.timer = Ext.debug.time;
Ext.timerEnd = Ext.debug.timeEnd;

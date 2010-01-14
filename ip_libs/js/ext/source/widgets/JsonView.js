/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/**
 * @class Ext.JsonView
 * @extends Ext.View
 * Shortcut class to create a JSON + {@link Ext.UpdateManager} template view. Usage:
<pre><code>
var view = new Ext.JsonView("my-element",
    '&lt;div id="{id}"&gt;{foo} - {bar}&lt;/div&gt;', // auto create template
    { multiSelect: true, jsonRoot: "data" }
);

// listen for node click?
view.on("click", function(vw, index, node, e){
    alert('Node "' + node.id + '" at index: ' + index + " was clicked.");
});

// direct load of JSON data
view.load("foobar.php");

// Example from my blog list
var tpl = new Ext.Template(
    '&lt;div class="entry"&gt;' +
    '&lt;a class="entry-title" href="{link}"&gt;{title}&lt;/a&gt;' +
    "&lt;h4&gt;{date} by {author} | {comments} Comments&lt;/h4&gt;{description}" +
    "&lt;/div&gt;&lt;hr /&gt;"
);

var moreView = new Ext.JsonView("entry-list", tpl, {
    jsonRoot: "posts"
});
moreView.on("beforerender", this.sortEntries, this);
moreView.load({
    url: "/blog/get-posts.php",
    params: "allposts=true",
    text: "Loading Blog Entries..."
});
</code></pre>
 * @constructor
 * Create a new JsonView
 * @param {String/HTMLElement/Element} container The container element where the view is to be rendered.
 * @param {Template} tpl The rendering template
 * @param {Object} config The config object
 */
Ext.JsonView = function(container, tpl, config){
    Ext.JsonView.superclass.constructor.call(this, container, tpl, config);

    var um = this.el.getUpdateManager();
    um.setRenderer(this);
    um.on("update", this.onLoad, this);
    um.on("failure", this.onLoadException, this);

    /**
     * @event beforerender
     * Fires before rendering of the downloaded JSON data.
     * @param {Ext.JsonView} this
     * @param {Object} data The JSON data loaded
     */
    /**
     * @event load
     * Fires when data is loaded.
     * @param {Ext.JsonView} this
     * @param {Object} data The JSON data loaded
     * @param {Object} response The raw Connect response object
     */
    /**
     * @event loadexception
     * Fires when loading fails.
     * @param {Ext.JsonView} this
     * @param {Object} response The raw Connect response object
     */
    this.addEvents({
        'beforerender' : true,
        'load' : true,
        'loadexception' : true
    });
};
Ext.extend(Ext.JsonView, Ext.View, {
    /**
     * The root property in the loaded JSON object that contains the data
     * @type {String}
     */
    jsonRoot : "",

    /**
     * Refreshes the view.
     */
    refresh : function(){
        this.clearSelections();
        this.el.update("");
        var html = [];
        var o = this.jsonData;
        if(o && o.length > 0){
            for(var i = 0, len = o.length; i < len; i++){
                var data = this.prepareData(o[i], i, o);
                html[html.length] = this.tpl.apply(data);
            }
        }else{
            html.push(this.emptyText);
        }
        this.el.update(html.join(""));
        this.nodes = this.el.dom.childNodes;
        this.updateIndexes(0);
    },

    /**
     * Performs an async HTTP request, and loads the JSON from the response. If <i>params</i> are specified it uses POST, otherwise it uses GET.
     * @param {Object/String/Function} url The URL for this request, or a function to call to get the URL, or a config object containing any of the following options:
     <pre><code>
     view.load({
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
     * are respectively shorthand for <i>disableCaching</i>, <i>indicatorText</i>, and <i>loadScripts</i> and are used to set their associated property on this UpdateManager instance.
     * @param {String/Object} params (optional) The parameters to pass, as either a URL encoded string "param1=1&amp;param2=2" or an object {param1: 1, param2: 2}
     * @param {Function} callback (optional) Callback when transaction is complete - called with signature (oElement, bSuccess)
     * @param {Boolean} discardUrl (optional) By default when you execute an update the defaultUrl is changed to the last used URL. If true, it will not store the URL.
     */
    load : function(){
        var um = this.el.getUpdateManager();
        um.update.apply(um, arguments);
    },

    render : function(el, response){
        this.clearSelections();
        this.el.update("");
        var o;
        try{
            o = Ext.util.JSON.decode(response.responseText);
            if(this.jsonRoot){
                o = eval("o." + this.jsonRoot);
            }
        } catch(e){
        }
        /**
         * The current JSON data or null
         */
        this.jsonData = o;
        this.beforeRender();
        this.refresh();
    },

/**
 * Get the number of records in the current JSON dataset
 * @return {Number}
 */
    getCount : function(){
        return this.jsonData ? this.jsonData.length : 0;
    },

/**
 * Returns the JSON object for the specified node(s)
 * @param {HTMLElement/Array} node The node or an array of nodes
 * @return {Object/Array} If you pass in an array, you get an array back, otherwise
 * you get the JSON object for the node
 */
    getNodeData : function(node){
        if(node instanceof Array){
            var data = [];
            for(var i = 0, len = node.length; i < len; i++){
                data.push(this.getNodeData(node[i]));
            }
            return data;
        }
        return this.jsonData[this.indexOf(node)] || null;
    },

    beforeRender : function(){
        this.snapshot = this.jsonData;
        if(this.sortInfo){
            this.sort.apply(this, this.sortInfo);
        }
        this.fireEvent("beforerender", this, this.jsonData);
    },

    onLoad : function(el, o){
        this.fireEvent("load", this, this.jsonData, o);
    },

    onLoadException : function(el, o){
        this.fireEvent("loadexception", this, o);
    },

/**
 * Filter the data by a specific property.
 * @param {String} property A property on your JSON objects
 * @param {String/RegExp} value Either string that the property values
 * should start with, or a RegExp to test against the property
 */
    filter : function(property, value){
        if(this.jsonData){
            var data = [];
            var ss = this.snapshot;
            if(typeof value == "string"){
                var vlen = value.length;
                if(vlen == 0){
                    this.clearFilter();
                    return;
                }
                value = value.toLowerCase();
                for(var i = 0, len = ss.length; i < len; i++){
                    var o = ss[i];
                    if(o[property].substr(0, vlen).toLowerCase() == value){
                        data.push(o);
                    }
                }
            } else if(value.exec){ // regex?
                for(var i = 0, len = ss.length; i < len; i++){
                    var o = ss[i];
                    if(value.test(o[property])){
                        data.push(o);
                    }
                }
            } else{
                return;
            }
            this.jsonData = data;
            this.refresh();
        }
    },

/**
 * Filter by a function. The passed function will be called with each
 * object in the current dataset. If the function returns true the value is kept,
 * otherwise it is filtered.
 * @param {Function} fn
 * @param {Object} scope (optional) The scope of the function (defaults to this JsonView)
 */
    filterBy : function(fn, scope){
        if(this.jsonData){
            var data = [];
            var ss = this.snapshot;
            for(var i = 0, len = ss.length; i < len; i++){
                var o = ss[i];
                if(fn.call(scope || this, o)){
                    data.push(o);
                }
            }
            this.jsonData = data;
            this.refresh();
        }
    },

/**
 * Clears the current filter.
 */
    clearFilter : function(){
        if(this.snapshot && this.jsonData != this.snapshot){
            this.jsonData = this.snapshot;
            this.refresh();
        }
    },


/**
 * Sorts the data for this view and refreshes it.
 * @param {String} property A property on your JSON objects to sort on
 * @param {String} direction (optional) "desc" or "asc" (defaults to "asc")
 * @param {Function} sortType (optional) A function to call to convert the data to a sortable value.
 */
    sort : function(property, dir, sortType){
        this.sortInfo = Array.prototype.slice.call(arguments, 0);
        if(this.jsonData){
            var p = property;
            var dsc = dir && dir.toLowerCase() == "desc";
            var f = function(o1, o2){
                var v1 = sortType ? sortType(o1[p]) : o1[p];
                var v2 = sortType ? sortType(o2[p]) : o2[p];
                ;
                if(v1 < v2){
                    return dsc ? +1 : -1;
                } else if(v1 > v2){
                    return dsc ? -1 : +1;
                } else{
                    return 0;
                }
            };
            this.jsonData.sort(f);
            this.refresh();
            if(this.jsonData != this.snapshot){
                this.snapshot.sort(f);
            }
        }
    }
});
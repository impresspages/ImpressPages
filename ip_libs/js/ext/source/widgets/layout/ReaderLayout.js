/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/**
 * @class Ext.ReaderLayout
 * @extends Ext.BorderLayout
 * This is a pre-built layout that represents a classic, 5-pane application.  It consists of a header, a primary
 * center region containing two nested regions (a top one for a list view and one for item preview below),
 * and regions on either side that can be used for navigation, application commands, informational displays, etc.
 * The setup and configuration work exactly the same as it does for a {@link Ext.BorderLayout} - this class simply
 * expedites the setup of the overall layout and regions for this common application style.
 * Example:
 <pre><code>
var reader = new Ext.ReaderLayout();
var CP = Ext.ContentPanel;  // shortcut for adding

reader.beginUpdate();
reader.add("north", new CP("north", "North"));
reader.add("west", new CP("west", {title: "West"}));
reader.add("east", new CP("east", {title: "East"}));

reader.regions.listView.add(new CP("listView", "List"));
reader.regions.preview.add(new CP("preview", "Preview"));
reader.endUpdate();
</code></pre>
* @constructor
* Create a new ReaderLayout
* @param {Object} config Configuration options
* @param {String/HTMLElement/Element} container (optional) The container this layout is bound to (defaults to
* document.body if omitted)
*/
Ext.ReaderLayout = function(config, renderTo){
    var c = config || {size:{}};
    Ext.ReaderLayout.superclass.constructor.call(this, renderTo || document.body, {
        north: c.north !== false ? Ext.apply({
            split:false,
            initialSize: 32,
            titlebar: false
        }, c.north) : false,
        west: c.west !== false ? Ext.apply({
            split:true,
            initialSize: 200,
            minSize: 175,
            maxSize: 400,
            titlebar: true,
            collapsible: true,
            animate: true,
            margins:{left:5,right:0,bottom:5,top:5},
            cmargins:{left:5,right:5,bottom:5,top:5}
        }, c.west) : false,
        east: c.east !== false ? Ext.apply({
            split:true,
            initialSize: 200,
            minSize: 175,
            maxSize: 400,
            titlebar: true,
            collapsible: true,
            animate: true,
            margins:{left:0,right:5,bottom:5,top:5},
            cmargins:{left:5,right:5,bottom:5,top:5}
        }, c.east) : false,
        center: Ext.apply({
            tabPosition: 'top',
            autoScroll:false,
            closeOnTab: true,
            titlebar:false,
            margins:{left:c.west!==false ? 0 : 5,right:c.east!==false ? 0 : 5,bottom:5,top:2}
        }, c.center)
    });

    this.el.addClass('x-reader');

    this.beginUpdate();

    var inner = new Ext.BorderLayout(Ext.get(document.body).createChild(), {
        south: c.preview !== false ? Ext.apply({
            split:true,
            initialSize: 200,
            minSize: 100,
            autoScroll:true,
            collapsible:true,
            titlebar: true,
            cmargins:{top:5,left:0, right:0, bottom:0}
        }, c.preview) : false,
        center: Ext.apply({
            autoScroll:false,
            titlebar:false,
            minHeight:200
        }, c.listView)
    });
    this.add('center', new Ext.NestedLayoutPanel(inner,
            Ext.apply({title: c.mainTitle || '',tabTip:''},c.innerPanelCfg)));

    this.endUpdate();

    this.regions.preview = inner.getRegion('south');
    this.regions.listView = inner.getRegion('center');
};

Ext.extend(Ext.ReaderLayout, Ext.BorderLayout);
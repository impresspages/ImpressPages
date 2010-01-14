/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/**
 * @class Ext.Toolbar
 * Basic Toolbar class.
 * @constructor
 * Creates a new Toolbar
 * @param {String/HTMLElement/Element} container The id or element that will contain the toolbar
 * @param {Array} buttons (optional) array of button configs or elements to add
 * @param {Object} config The config object
 */ 
Ext.Toolbar = function(container, buttons, config){
    if(container instanceof Array){ // omit the container for later rendering
        buttons = container;
        config = buttons;
        container = null;
    }
    Ext.apply(this, config);
    this.buttons = buttons;
    if(container){
        this.render(container);
    }
};

Ext.Toolbar.prototype = {

    render : function(ct){
        this.el = Ext.get(ct);
        if(this.cls){
            this.el.addClass(this.cls);
        }
        // using a table allows for vertical alignment
        this.el.update('<div class="x-toolbar x-small-editor"><table cellspacing="0"><tr></tr></table></div>');
        this.tr = this.el.child("tr", true);
        var autoId = 0;
        this.items = new Ext.util.MixedCollection(false, function(o){
            return o.id || ("item" + (++autoId));
        });
        if(this.buttons){
            this.add.apply(this, this.buttons);
            delete this.buttons;
        }
    },

    /**
     * Adds element(s) to the toolbar -- this function takes a variable number of 
     * arguments of mixed type and adds them to the toolbar.
     * @param {Mixed} arg1 If arg is a Toolbar.Button, it is added. If arg is a string, it is wrapped 
     * in a ytb-text element and added unless the text is "separator" in which case a separator
     * is added. Otherwise, it is assumed the element is an HTMLElement and it is added directly.
     * @param {Mixed} arg2
     * @param {Mixed} etc
     */
    add : function(){
        var a = arguments, l = a.length;
        for(var i = 0; i < l; i++){
            var el = a[i];
            if(el.applyTo){ // some kind of form field
                this.addField(el);
            }else if(el.render){ // some kind of Toolbar.Item
                this.addItem(el);
            }else if(typeof el == "string"){ // string
                if(el == "separator" || el == "-"){
                    this.addSeparator();
                }else if(el == " "){
                    this.addSpacer();
                }else if(el == "->"){
                    this.addFill();
                }else{
                    this.addText(el);
                }
            }else if(el.tagName){ // element
                this.addElement(el);
            }else if(typeof el == "object"){ // must be button config?
                this.addButton(el);
            }
        }
    },
    
    /**
     * Returns the Element for this toolbar.
     * @return {Ext.Element}
     */
    getEl : function(){
        return this.el;  
    },
    
    /**
     * Adds a separator
     * @return {Ext.Toolbar.Item} The separator item
     */
    addSeparator : function(){
        return this.addItem(new Ext.Toolbar.Separator());
    },

    /**
     * Adds a spacer element
     * @return {Ext.Toolbar.Spacer} The spacer item
     */
    addSpacer : function(){
        return this.addItem(new Ext.Toolbar.Spacer());
    },

    /**
     * Adds a fill element that forces subsequent additions to the right side of the toolbar
     * @return {Ext.Toolbar.Fill} The fill item
     */
    addFill : function(){
        return this.addItem(new Ext.Toolbar.Fill());
    },

    /**
     * Adds any standard HTML element to the toolbar
     * @param {String/HTMLElement/Element} el The element or id of the element to add
     * @return {Ext.Toolbar.Item} The element's item
     */
    addElement : function(el){
        return this.addItem(new Ext.Toolbar.Item(el));
    },
    
    /**
     * Adds any Toolbar.Item or subclass
     * @param {Toolbar.Item} item
     * @return {Ext.Toolbar.Item} The item
     */
    addItem : function(item){
        var td = this.nextBlock();
        item.render(td);
        this.items.add(item);
        return item;
    },
    
    /**
     * Adds a button (or buttons). See {@link Ext.Toolbar.Button} for more info on the config.
     * @param {Object/Array} config A button config or array of configs
     * @return {Ext.Toolbar.Button/Array}
     */
    addButton : function(config){
        if(config instanceof Array){
            var buttons = [];
            for(var i = 0, len = config.length; i < len; i++) {
                buttons.push(this.addButton(config[i]));
            }
            return buttons;
        }
        var b = config;
        if(!(config instanceof Ext.Toolbar.Button)){
            b = config.split ?
                new Ext.Toolbar.SplitButton(config) :
                new Ext.Toolbar.Button(config);
        }
        var td = this.nextBlock();
        b.render(td);
        this.items.add(b);
        return b;
    },
    
    /**
     * Adds text to the toolbar
     * @param {String} text The text to add
     * @return {Ext.Toolbar.Item} The element's item
     */
    addText : function(text){
        return this.addItem(new Ext.Toolbar.TextItem(text));
    },
    
    /**
     * Inserts any {@link Ext.Toolbar.Item}/{@link Ext.Toolbar.Button} at the specified index.
     * @param {Number} index The index where the item is to be inserted
     * @param {Object/Ext.Toolbar.Item/Ext.Toolbar.Button (may be Array)} item The button, or button config object to be inserted.
     * @return {Ext.Toolbar.Button/Item}
     */
    insertButton : function(index, item){
        if(item instanceof Array){
            var buttons = [];
            for(var i = 0, len = item.length; i < len; i++) {
               buttons.push(this.insertButton(index + i, item[i]));
            }
            return buttons;
        }
        if (!(item instanceof Ext.Toolbar.Button)){
           item = new Ext.Toolbar.Button(item);
        }
        var td = document.createElement("td");
        this.tr.insertBefore(td, this.tr.childNodes[index]);
        item.render(td);
        this.items.insert(index, item);
        return item;
    },
    
    /**
     * Adds a new element to the toolbar from the passed {@link Ext.DomHelper} config.
     * @param {Object} config
     * @return {Ext.Toolbar.Item} The element's item
     */
    addDom : function(config, returnEl){
        var td = this.nextBlock();
        Ext.DomHelper.overwrite(td, config);
        var ti = new Ext.Toolbar.Item(td.firstChild);
        ti.render(td);
        this.items.add(ti);
        return ti;
    },

    /**
     * Adds a dynamically rendered Ext.form field (TextField, ComboBox, etc). Note: the field should not have
     * been rendered yet. For a field that has already been rendered, use {@link #addElement}.
     * @param {Ext.form.Field} field
     * @return {Ext.ToolbarItem}
     */
    addField : function(field){
        var td = this.nextBlock();
        field.render(td);
        var ti = new Ext.Toolbar.Item(td.firstChild);
        ti.render(td);
        this.items.add(ti);
        return ti;
    },

    // private
    nextBlock : function(){
        var td = document.createElement("td");
        this.tr.appendChild(td);
        return td;
    },

    destroy : function(){
        if(this.items){ // rendered?
            Ext.destroy.apply(Ext, this.items.items);
        }
        Ext.Element.uncache(this.el, this.tr);
    }
};

/**
 * @class Ext.Toolbar.Item
 * The base class that other classes should extend in order to get some basic common toolbar item functionality.
 * @constructor
 * Creates a new Item
 * @param {HTMLElement} el 
 */
Ext.Toolbar.Item = function(el){
    this.el = Ext.getDom(el);
    this.id = Ext.id(this.el);
    this.hidden = false;
};

Ext.Toolbar.Item.prototype = {
    
    /**
     * Get this item's HTML Element
     * @return {HTMLElement}
     */
    getEl : function(){
       return this.el;  
    },

    // private
    render : function(td){
        this.td = td;
        td.appendChild(this.el);
    },
    
    /**
     * Removes and destroys this item.
     */
    destroy : function(){
        this.td.parentNode.removeChild(this.td);
    },
    
    /**
     * Shows this item.
     */
    show: function(){
        this.hidden = false;
        this.td.style.display = "";
    },
    
    /**
     * Hides this item.
     */
    hide: function(){
        this.hidden = true;
        this.td.style.display = "none";
    },
    
    /**
     * Convenience function for boolean show/hide.
     * @param {Boolean} visible true to show/false to hide
     */
    setVisible: function(visible){
        if(visible) {
            this.show();
        }else{
            this.hide();
        }
    },
    
    /**
     * Try to focus this item.
     */
    focus : function(){
        Ext.fly(this.el).focus();
    },
    
    /**
     * Disables this item.
     */
    disable : function(){
        Ext.fly(this.td).addClass("x-item-disabled");
        this.disabled = true;
        this.el.disabled = true;
    },
    
    /**
     * Enables this item.
     */
    enable : function(){
        Ext.fly(this.td).removeClass("x-item-disabled");
        this.disabled = false;
        this.el.disabled = false;
    }
};


/**
 * @class Ext.Toolbar.Separator
 * @extends Ext.Toolbar.Item
 * A simple toolbar separator class
 * @constructor
 * Creates a new Separator
 */
Ext.Toolbar.Separator = function(){
    var s = document.createElement("span");
    s.className = "ytb-sep";
    Ext.Toolbar.Separator.superclass.constructor.call(this, s);
};
Ext.extend(Ext.Toolbar.Separator, Ext.Toolbar.Item, {
    enable:Ext.emptyFn,
    disable:Ext.emptyFn,
    focus:Ext.emptyFn
});

/**
 * @class Ext.Toolbar.Spacer
 * @extends Ext.Toolbar.Item
 * A simple element that adds extra horizontal space to a toolbar.
 * @constructor
 * Creates a new Spacer
 */
Ext.Toolbar.Spacer = function(){
    var s = document.createElement("div");
    s.className = "ytb-spacer";
    Ext.Toolbar.Spacer.superclass.constructor.call(this, s);
};
Ext.extend(Ext.Toolbar.Spacer, Ext.Toolbar.Item, {
    enable:Ext.emptyFn,
    disable:Ext.emptyFn,
    focus:Ext.emptyFn
});


Ext.Toolbar.Fill = Ext.extend(Ext.Toolbar.Spacer, {
    // private
    render : function(td){
        td.style.width = '100%';
        Ext.Toolbar.Fill.superclass.render.call(this, td);
    }
});

/**
 * @class Ext.Toolbar.TextItem
 * @extends Ext.Toolbar.Item
 * A simple class that renders text directly into a toolbar.
 * @constructor
 * Creates a new TextItem
 * @param {String} text
 */
Ext.Toolbar.TextItem = function(text){
    var s = document.createElement("span");
    s.className = "ytb-text";
    s.innerHTML = text;
    Ext.Toolbar.TextItem.superclass.constructor.call(this, s);
};
Ext.extend(Ext.Toolbar.TextItem, Ext.Toolbar.Item, {
    enable:Ext.emptyFn,
    disable:Ext.emptyFn,
    focus:Ext.emptyFn
});

/**
 * @class Ext.Toolbar.Button
 * @extends Ext.Button
 * A button that renders into a toolbar.
 * @constructor
 * Creates a new Button
 * @param {Object} config A standard {@link Ext.Button} config object
 */
Ext.Toolbar.Button = function(config){
    Ext.Toolbar.Button.superclass.constructor.call(this, null, config);
};
Ext.extend(Ext.Toolbar.Button, Ext.Button, {
    render : function(td){
        this.td = td;
        Ext.Toolbar.Button.superclass.render.call(this, td);
    },
    
    /**
     * Removes and destroys this button
     */
    destroy : function(){
        Ext.Toolbar.Button.superclass.destroy.call(this);
        this.td.parentNode.removeChild(this.td);
    },
    
    /**
     * Shows this button
     */
    show: function(){
        this.hidden = false;
        this.td.style.display = "";
    },
    
    /**
     * Hides this button
     */
    hide: function(){
        this.hidden = true;
        this.td.style.display = "none";
    },

    /**
     * Disables this item
     */
    disable : function(){
        Ext.fly(this.td).addClass("x-item-disabled");
        this.disabled = true;
    },

    /**
     * Enables this item
     */
    enable : function(){
        Ext.fly(this.td).removeClass("x-item-disabled");
        this.disabled = false;
    }
});
// backwards compat
Ext.ToolbarButton = Ext.Toolbar.Button;

/**
 * @class Ext.Toolbar.SplitButton
 * @extends Ext.SplitButton
 * A menu button that renders into a toolbar.
 * @constructor
 * Creates a new SplitButton
 * @param {Object} config A standard {@link Ext.SplitButton} config object
 */
Ext.Toolbar.SplitButton = function(config){
    Ext.Toolbar.SplitButton.superclass.constructor.call(this, null, config);
};
Ext.extend(Ext.Toolbar.SplitButton, Ext.SplitButton, {
    render : function(td){
        this.td = td;
        Ext.Toolbar.SplitButton.superclass.render.call(this, td);
    },
    
    /**
     * Removes and destroys this button
     */
    destroy : function(){
        Ext.Toolbar.SplitButton.superclass.destroy.call(this);
        this.td.parentNode.removeChild(this.td);
    },
    
    /**
     * Shows this button
     */
    show: function(){
        this.hidden = false;
        this.td.style.display = "";
    },
    
    /**
     * Hides this button
     */
    hide: function(){
        this.hidden = true;
        this.td.style.display = "none";
    }
});

// backwards compat
Ext.Toolbar.MenuButton = Ext.Toolbar.SplitButton;
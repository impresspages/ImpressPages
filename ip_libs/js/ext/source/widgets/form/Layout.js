/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/**
 * @class Ext.form.Layout
 * @extends Ext.Component
 * Creates a container for layout and rendering of fields in an {@link Ext.form.Form}.
 * @constructor
 * @param {Object} config Configuration options
 */
Ext.form.Layout = function(config){
    Ext.form.Layout.superclass.constructor.call(this, config);
    this.stack = [];
};

Ext.extend(Ext.form.Layout, Ext.Component, {
    /**
     * @cfg {String/Object} autoCreate
     * A DomHelper element spec used to autocreate the layout (defaults to {tag: 'div', cls: 'x-form-ct'})
     */
    /**
     * @cfg {String/Object/Function} style
     * A style specification string, e.g. "width:100px", or object in the form {width:"100px"}, or
     * a function which returns such a specification.
     */
    /**
     * @cfg {String} labelAlign
     * Valid values are "left," "top" and "right" (defaults to "left")
     */
    /**
     * @cfg {Number} labelWidth
     * Fixed width in pixels of all field labels (defaults to undefined)
     */
    /**
     * @cfg {Boolean} clear
     * True to add a clearing element at the end of this layout, equivalent to CSS clear: both (defaults to true)
     */
    clear : true,
    /**
     * @cfg {String} labelSeparator
     * The separator to use after field labels (defaults to ':')
     */
    labelSeparator : ':',
    /**
     * @cfg {Boolean} hideLabels
     * True to suppress the display of field labels in this layout (defaults to false)
     */
    hideLabels : false,

    // private
    defaultAutoCreate : {tag: 'div', cls: 'x-form-ct'},

    // private
    onRender : function(ct, position){
        if(this.el){ // from markup
            this.el = Ext.get(this.el);
        }else {  // generate
            var cfg = this.getAutoCreate();
            this.el = ct.createChild(cfg, position);
        }
        if(this.style){
            this.el.applyStyles(this.style);
        }
        if(this.labelAlign){
            this.el.addClass('x-form-label-'+this.labelAlign);
        }
        if(this.hideLabels){
            this.labelStyle = "display:none";
            this.elementStyle = "padding-left:0;";
        }else{
            if(typeof this.labelWidth == 'number'){
                this.labelStyle = "width:"+this.labelWidth+"px;";
                this.elementStyle = "padding-left:"+((this.labelWidth+(typeof this.labelPad == 'number' ? this.labelPad : 5))+'px')+";";
            }
            if(this.labelAlign == 'top'){
                this.labelStyle = "width:auto;";
                this.elementStyle = "padding-left:0;";
            }
        }
        var stack = this.stack;
        var slen = stack.length;
        if(slen > 0){
            if(!this.fieldTpl){
                var t = new Ext.Template(
                    '<div class="x-form-item {5}">',
                        '<label for="{0}" style="{2}">{1}{4}</label>',
                        '<div class="x-form-element" id="x-form-el-{0}" style="{3}">',
                        '</div>',
                    '</div><div class="x-form-clear-left"></div>'
                );
                t.disableFormats = true;
                t.compile();
                Ext.form.Layout.prototype.fieldTpl = t;
            }
            for(var i = 0; i < slen; i++) {
                if(stack[i].isFormField){
                    this.renderField(stack[i]);
                }else{
                    this.renderComponent(stack[i]);
                }
            }
        }
        if(this.clear){
            this.el.createChild({cls:'x-form-clear'});
        }
    },

    // private
    renderField : function(f){
       this.fieldTpl.append(this.el, [
               f.id, f.fieldLabel,
               f.labelStyle||this.labelStyle||'',
               this.elementStyle||'',
               typeof f.labelSeparator == 'undefined' ? this.labelSeparator : f.labelSeparator,
               f.itemCls||this.itemCls||''
       ]);
    },

    // private
    renderComponent : function(c){
        c.render(this.el);
    }
});

/**
 * @class Ext.form.Column
 * @extends Ext.form.Layout
 * Creates a column container for layout and rendering of fields in an {@link Ext.form.Form}.
 * @constructor
 * @param {Object} config Configuration options
 */
Ext.form.Column = function(config){
    Ext.form.Column.superclass.constructor.call(this, config);
};

Ext.extend(Ext.form.Column, Ext.form.Layout, {
    /**
     * @cfg {Number/String} width
     * The fixed width of the column in pixels or CSS value (defaults to "auto")
     */
    /**
     * @cfg {String/Object} autoCreate
     * A DomHelper element spec used to autocreate the column (defaults to {tag: 'div', cls: 'x-form-ct x-form-column'})
     */

    // private
    defaultAutoCreate : {tag: 'div', cls: 'x-form-ct x-form-column'},

    // private
    onRender : function(ct, position){
        Ext.form.Column.superclass.onRender.call(this, ct, position);
        if(this.width){
            this.el.setWidth(this.width);
        }
    }
});

/**
 * @class Ext.form.FieldSet
 * @extends Ext.form.Layout
 * Creates a fieldset container for layout and rendering of fields in an {@link Ext.form.Form}.
 * @constructor
 * @param {Object} config Configuration options
 */
Ext.form.FieldSet = function(config){
    Ext.form.FieldSet.superclass.constructor.call(this, config);
};

Ext.extend(Ext.form.FieldSet, Ext.form.Layout, {
    /**
     * @cfg {String} legend
     * The text to display as the legend for the FieldSet (defaults to '')
     */
    /**
     * @cfg {String/Object} autoCreate
     * A DomHelper element spec used to autocreate the fieldset (defaults to {tag: 'fieldset', cn: {tag:'legend'}})
     */

    // private
    defaultAutoCreate : {tag: 'fieldset', cn: {tag:'legend'}},

    // private
    onRender : function(ct, position){
        Ext.form.FieldSet.superclass.onRender.call(this, ct, position);
        if(this.legend){
            this.setLegend(this.legend);
        }
    },

    // private
    setLegend : function(text){
        if(this.rendered){
            this.el.child('legend').update(text);
        }
    }
});
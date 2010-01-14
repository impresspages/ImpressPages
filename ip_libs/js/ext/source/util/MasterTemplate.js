/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/**
 * @class Ext.MasterTemplate
 * @extends Ext.Template
 * Provides a template that can have child templates. The syntax is:
<pre><code>
var t = new Ext.MasterTemplate(
	'&lt;select name="{name}"&gt;',
		'&lt;tpl name="options"&gt;&lt;option value="{value:trim}"&gt;{text:ellipsis(10)}&lt;/option&gt;&lt;/tpl&gt;',
	'&lt;/select&gt;'
);
t.add('options', {value: 'foo', text: 'bar'});
// or you can add multiple child elements in one shot
t.addAll('options', [
    {value: 'foo', text: 'bar'},
    {value: 'foo2', text: 'bar2'},
    {value: 'foo3', text: 'bar3'}
]);
// then append, applying the master template values
t.append('my-form', {name: 'my-select'});
</code></pre>
* A name attribute for the child template is not required if you have only one child
* template or you want to refer to them by index.
 */
Ext.MasterTemplate = function(){
    Ext.MasterTemplate.superclass.constructor.apply(this, arguments);
    this.originalHtml = this.html;
    var st = {};
    var m, re = this.subTemplateRe;
    re.lastIndex = 0;
    var subIndex = 0;
    while(m = re.exec(this.html)){
        var name = m[1], content = m[2];
        st[subIndex] = {
            name: name,
            index: subIndex,
            buffer: [],
            tpl : new Ext.Template(content)
        };
        if(name){
            st[name] = st[subIndex];
        }
        st[subIndex].tpl.compile();
        st[subIndex].tpl.call = this.call.createDelegate(this);
        subIndex++;
    }
    this.subCount = subIndex;
    this.subs = st;
};
Ext.extend(Ext.MasterTemplate, Ext.Template, {
    /**
    * The regular expression used to match sub templates
    * @type RegExp
    * @property
    */
    subTemplateRe : /<tpl(?:\sname="([\w-]+)")?>((?:.|\n)*?)<\/tpl>/gi,

    /**
     * Applies the passed values to a child template.
     * @param {String/Number} name (optional) The name or index of the child template
     * @param {Array/Object} values The values to be applied to the template
     * @return {MasterTemplate} this
     */
     add : function(name, values){
        if(arguments.length == 1){
            values = arguments[0];
            name = 0;
        }
        var s = this.subs[name];
        s.buffer[s.buffer.length] = s.tpl.apply(values);
        return this;
    },

    /**
     * Applies all the passed values to a child template.
     * @param {String/Number} name (optional) The name or index of the child template
     * @param {Array} values The values to be applied to the template, this should be an array of objects.
     * @param {Boolean} reset (optional) True to reset the template first
     * @return {MasterTemplate} this
     */
    fill : function(name, values, reset){
        var a = arguments;
        if(a.length == 1 || (a.length == 2 && typeof a[1] == "boolean")){
            values = a[0];
            name = 0;
            reset = a[1];
        }
        if(reset){
            this.reset();
        }
        for(var i = 0, len = values.length; i < len; i++){
            this.add(name, values[i]);
        }
        return this;
    },

    /**
     * Resets the template for reuse
     * @return {MasterTemplate} this
     */
     reset : function(){
        var s = this.subs;
        for(var i = 0; i < this.subCount; i++){
            s[i].buffer = [];
        }
        return this;
    },

    applyTemplate : function(values){
        var s = this.subs;
        var replaceIndex = -1;
        this.html = this.originalHtml.replace(this.subTemplateRe, function(m, name){
            return s[++replaceIndex].buffer.join("");
        });
        return Ext.MasterTemplate.superclass.applyTemplate.call(this, values);
    },

    apply : function(){
        return this.applyTemplate.apply(this, arguments);
    },

    compile : function(){return this;}
});

/**
 * Alias for fill().
 * @method
 */
Ext.MasterTemplate.prototype.addAll = Ext.MasterTemplate.prototype.fill;
 /**
 * Creates a template from the passed element's value (display:none textarea, preferred) or innerHTML. e.g.
 * var tpl = Ext.MasterTemplate.from('element-id');
 * @param {String/HTMLElement} el
 * @param {Object} config
 * @static
 */
Ext.MasterTemplate.from = function(el, config){
    el = Ext.getDom(el);
    return new Ext.MasterTemplate(el.value || el.innerHTML, config || '');
};
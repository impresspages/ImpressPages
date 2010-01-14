/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

Ext.grid.PropertyRecord = Ext.data.Record.create([
    {name:'name',type:'string'}, 'value'
]);

Ext.grid.PropertyStore = function(grid, source){
    this.grid = grid;
    this.store = new Ext.data.Store({
        recordType : Ext.grid.PropertyRecord
    });
    this.store.on('update', this.onUpdate,  this);
    if(source){
        this.setSource(source);
    }
    Ext.grid.PropertyStore.superclass.constructor.call(this);
};
Ext.extend(Ext.grid.PropertyStore, Ext.util.Observable, {
    setSource : function(o){
        this.source = o;
        this.store.removeAll();
        var data = [];
        for(var k in o){
            if(this.isEditableValue(o[k])){
                data.push(new Ext.grid.PropertyRecord({name: k, value: o[k]}, k));
            }
        }
        this.store.loadRecords({records: data}, {}, true);
    },

    onUpdate : function(ds, record, type){
        if(type == Ext.data.Record.EDIT){
            var v = record.data['value'];
            var oldValue = record.modified['value'];
            if(this.grid.fireEvent('beforepropertychange', this.source, record.id, v, oldValue) !== false){
                this.source[record.id] = v;
                record.commit();
                this.grid.fireEvent('propertychange', this.source, record.id, v, oldValue);
            }else{
                record.reject();
            }
        }
    },

    getProperty : function(row){
       return this.store.getAt(row);
    },

    isEditableValue: function(val){
        if(val && val instanceof Date){
            return true;
        }else if(typeof val == 'object' || typeof val == 'function'){
            return false;
        }
        return true;
    },

    setValue : function(prop, value){
        this.source[prop] = value;
        this.store.getById(prop).set('value', value);
    },

    getSource : function(){
        return this.source;
    }
});

Ext.grid.PropertyColumnModel = function(grid, store){
    this.grid = grid;
    var g = Ext.grid;
    g.PropertyColumnModel.superclass.constructor.call(this, [
        {header: this.nameText, sortable: true, dataIndex:'name', id: 'name'},
        {header: this.valueText, resizable:false, dataIndex: 'value', id: 'value'}
    ]);
    this.store = store;
    this.bselect = Ext.DomHelper.append(document.body, {
        tag: 'select', style:'display:none', cls: 'x-grid-editor', children: [
            {tag: 'option', value: 'true', html: 'true'},
            {tag: 'option', value: 'false', html: 'false'}
        ]
    });
    Ext.id(this.bselect);
    var f = Ext.form;
    this.editors = {
        'date' : new g.GridEditor(new f.DateField({selectOnFocus:true})),
        'string' : new g.GridEditor(new f.TextField({selectOnFocus:true})),
        'number' : new g.GridEditor(new f.NumberField({selectOnFocus:true, style:'text-align:left;'})),
        'boolean' : new g.GridEditor(new f.Field({el:this.bselect,selectOnFocus:true}))
    };
    this.renderCellDelegate = this.renderCell.createDelegate(this);
    this.renderPropDelegate = this.renderProp.createDelegate(this);
};

Ext.extend(Ext.grid.PropertyColumnModel, Ext.grid.ColumnModel, {
    nameText : 'Name',
    valueText : 'Value',
    dateFormat : 'm/j/Y',
    renderDate : function(dateVal){
        return dateVal.dateFormat(this.dateFormat);
    },

    renderBool : function(bVal){
        return bVal ? 'true' : 'false';
    },

    isCellEditable : function(colIndex, rowIndex){
        return colIndex == 1;
    },

    getRenderer : function(col){
        return col == 1 ?
            this.renderCellDelegate : this.renderPropDelegate;
    },

    renderProp : function(v){
        return this.getPropertyName(v);
    },

    renderCell : function(val){
        var rv = val;
        if(val instanceof Date){
            rv = this.renderDate(val);
        }else if(typeof val == 'boolean'){
            rv = this.renderBool(val);
        }
        return Ext.util.Format.htmlEncode(rv);
    },

    getPropertyName : function(name){
        var pn = this.grid.propertyNames;
        return pn && pn[name] ? pn[name] : name;
    },

    getCellEditor : function(colIndex, rowIndex){
        var p = this.store.getProperty(rowIndex);
        var n = p.data['name'], val = p.data['value'];
        if(this.grid.customEditors[n]){
            return this.grid.customEditors[n];
        }
        if(val instanceof Date){
            return this.editors['date'];
        }else if(typeof val == 'number'){
            return this.editors['number'];
        }else if(typeof val == 'boolean'){
            return this.editors['boolean'];
        }else{
            return this.editors['string'];
        }
    }
});

Ext.grid.PropertyGrid = function(container, config){
    config = config || {};
    var store = new Ext.grid.PropertyStore(this);
    this.store = store;
    var cm = new Ext.grid.PropertyColumnModel(this, store);
    store.store.sort('name', 'ASC');
    Ext.grid.PropertyGrid.superclass.constructor.call(this, container, Ext.apply({
        ds: store.store,
        cm: cm,
        enableColLock:false,
        enableColumnMove:false,
        stripeRows:false,
        trackMouseOver: false,
        clicksToEdit:1
    }, config));
    this.getGridEl().addClass('x-props-grid');
    this.lastEditRow = null;
    this.on('columnresize', this.onColumnResize, this);
    this.addEvents({
        beforepropertychange: true,
        propertychange: true
    });
    this.customEditors = this.customEditors || {};
};
Ext.extend(Ext.grid.PropertyGrid, Ext.grid.EditorGrid, {
    render : function(){
        Ext.grid.PropertyGrid.superclass.render.call(this);
        this.autoSize.defer(100, this);
    },

    autoSize : function(){
        Ext.grid.PropertyGrid.superclass.autoSize.call(this);
        if(this.view){
            this.view.fitColumns();
        }
    },

    onColumnResize : function(){
        this.colModel.setColumnWidth(1, this.container.getWidth(true)-this.colModel.getColumnWidth(0));
        this.autoSize();
    },

    setSource : function(source){
        this.store.setSource(source);
        //this.autoSize();
    },

    getSource : function(){
        return this.store.getSource();
    }
});
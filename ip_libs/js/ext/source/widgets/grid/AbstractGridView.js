/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

Ext.grid.AbstractGridView = function(){
	this.grid = null;
	
	this.events = {
	    "beforerowremoved" : true,
	    "beforerowsinserted" : true,
	    "beforerefresh" : true,
	    "rowremoved" : true,
	    "rowsinserted" : true,
	    "rowupdated" : true,
	    "refresh" : true
	};
    Ext.grid.AbstractGridView.superclass.constructor.call(this);
};

Ext.extend(Ext.grid.AbstractGridView, Ext.util.Observable, {
    rowClass : "x-grid-row",
    cellClass : "x-grid-cell",
    tdClass : "x-grid-td",
    hdClass : "x-grid-hd",
    splitClass : "x-grid-hd-split",
    
	init: function(grid){
        this.grid = grid;
		var cid = this.grid.getGridEl().id;
        this.colSelector = "#" + cid + " ." + this.cellClass + "-";
        this.tdSelector = "#" + cid + " ." + this.tdClass + "-";
        this.hdSelector = "#" + cid + " ." + this.hdClass + "-";
        this.splitSelector = "#" + cid + " ." + this.splitClass + "-";
	},
	
	getColumnRenderers : function(){
    	var renderers = [];
    	var cm = this.grid.colModel;
        var colCount = cm.getColumnCount();
        for(var i = 0; i < colCount; i++){
            renderers[i] = cm.getRenderer(i);
        }
        return renderers;
    },
    
    getColumnIds : function(){
    	var ids = [];
    	var cm = this.grid.colModel;
        var colCount = cm.getColumnCount();
        for(var i = 0; i < colCount; i++){
            ids[i] = cm.getColumnId(i);
        }
        return ids;
    },
    
    getDataIndexes : function(){
    	if(!this.indexMap){
            this.indexMap = this.buildIndexMap();
        }
        return this.indexMap.colToData;
    },
    
    getColumnIndexByDataIndex : function(dataIndex){
        if(!this.indexMap){
            this.indexMap = this.buildIndexMap();
        }
    	return this.indexMap.dataToCol[dataIndex];
    },
    
    /**
     * Set a css style for a column dynamically. 
     * @param {Number} colIndex The index of the column
     * @param {String} name The css property name
     * @param {String} value The css value
     */
    setCSSStyle : function(colIndex, name, value){
        var selector = "#" + this.grid.id + " .x-grid-col-" + colIndex;
        Ext.util.CSS.updateRule(selector, name, value);
    },
    
    generateRules : function(cm){
        var ruleBuf = [], rulesId = this.grid.id + '-cssrules';
        Ext.util.CSS.removeStyleSheet(rulesId);
        for(var i = 0, len = cm.getColumnCount(); i < len; i++){
            var cid = cm.getColumnId(i);
            ruleBuf.push(this.colSelector, cid, " {\n", cm.config[i].css, "}\n",
                         this.tdSelector, cid, " {\n}\n",
                         this.hdSelector, cid, " {\n}\n",
                         this.splitSelector, cid, " {\n}\n");
        }
        return Ext.util.CSS.createStyleSheet(ruleBuf.join(""), rulesId);
    }
});
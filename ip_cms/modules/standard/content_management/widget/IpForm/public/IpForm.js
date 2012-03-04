/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */


/**
 * Widget initialization
 */
function IpWidget_IpForm(widgetObject) {
    this.widgetObject = widgetObject;

    this.prepareData = prepareData;
    this.manageInit = manageInit;
    this.addField = addField;


    function manageInit() {
        var instanceData = this.widgetObject.data('ipWidget');
        var container = this.widgetObject.find('.ipWidget_ipForm_container');
        var options = new Object;
        if (instanceData.data.fields) {
            options.fields = instanceData.data.fields;
        } else {
            options.fields = new Array();
        }        

        options.fieldTemplate = this.widgetObject.find('.ipaFieldTemplate');
        
        options.optionsPopup = this.widgetObject.find(".ipaOptionsPopup").ipWidget_ipForm_options({fieldTypes : instanceData.data.fieldTypes});
        container.ipWidget_ipForm_container(options);
        this.widgetObject.find(".ipaFormAddField").validator().submit(function (e){e.preventDefault(); $(this).trigger('addFieldClicked.ipForm');});
        this.widgetObject.bind('addFieldClicked.ipForm', this.addField);
        
        
    }
    
    
    function addField(e) {
        var $this = $(this);
        var $container = $this.find('.ipWidget_ipForm_container');
        $container.ipWidget_ipForm_container('addField');
    }
    

    
    function prepareData() {
        var data = Object();
        var container = this.widgetObject.find('.ipWidget_ipForm_container');
        
        data.fields = new Array();
        var $fields = container.ipWidget_ipForm_container('getFields');
        $fields.each(function(index) {
            var $this = $(this);
            var tmpField = new Object();
            tmpField.label = $this.ipWidget_ipForm_field('getLabel');
            tmpField.type = $this.ipWidget_ipForm_field('getType');
            tmpField.options = $this.ipWidget_ipForm_field('getOptions');
            var status = $this.ipWidget_ipForm_field('getStatus');
            if (status != 'deleted') {
                data.fields.push(tmpField);
            }

        });


        $(this.widgetObject).trigger('preparedWidgetData.ipWidget', [ data ]);
    }


};



/**
 * Fields container
 */
(function($) {

    var methods = {
        init : function(options) {
            return this.each(function() {
                var $this = $(this);
                var data = $this.data('ipWidget_ipForm_container');
                // If the plugin hasn't been initialized yet
                var fields = null;
                if (options.fields) {
                    fields = options.fields;
                } else {
                    fields = new Array();
                }
                
                if (!data) {
                    $this.data('ipWidget_ipForm_container', {
                        fields : fields,
                        fieldTemplate : options.fieldTemplate,
                        optionsPopup : options.optionsPopup
                    });
                    
                    if (! fields instanceof Array) {
                        fields = new Array();
                    }
                    
                    for (var i in fields) {
                        $this.ipWidget_ipForm_container('addField', fields[i]); 
                    }
                    $this.bind('removeField.ipWidget_ipForm', function(event, fieldObject) {
                        var $fieldObject = $(fieldObject);
                        $fieldObject.ipWidget_ipForm_container('removeField', $fieldObject);
                    });
                    
                    $this.sortable();
                    $this.sortable('option', 'handle', '.ipaFieldMove');
                    
                }
            });
        },
        
        addField : function (fieldData) {
            var $this = this;
            if (typeof fieldData !== 'object') {
                fieldData = {};
            }
            var data = fieldData;
            data.optionsPopup = $this.data('ipWidget_ipForm_container').optionsPopup;
            var $newFieldRecord = $this.data('ipWidget_ipForm_container').fieldTemplate.clone();
            $newFieldRecord.ipWidget_ipForm_field(data);
            
            $this.append($newFieldRecord);
            
        },
        
        removeField : function ($fieldObject) {
            $fieldObject.hide();
            $fieldObject.ipWidget_ipForm_field('setStatus', 'deleted');
            
        },
        
        getFields : function () {
            var $this = this;
            return $this.find('.ipaFieldTemplate');
        }
    };

    $.fn.ipWidget_ipForm_container = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipAdminWidgetButton');
        }

    };

})(jQuery);




/**
 * Genral Field
 */
(function($) {

    var methods = {
        init : function(options) {
            if (typeof options !== 'object') {
                options = {};
            }
            
            return this.each(function() {
    
                var $this = $(this);
    
                var data = $this.data('ipWidget_ipForm_field');
    
                
                // If the plugin hasn't been initialized yet
                if (!data) {
                    var data = {
                        label : '',
                        type : '',
                        status : 'new',
                        options : null
                    };
                    if (options.label) {
                        data.label = options.label;
                    }
                    if (options.type) {
                        data.type = options.type;
                    }
                    if (options.status) {
                        data.status = options.status;
                    }
                    if (options.options) {
                        data.options = options.options;
                    }
                    
                    $this.data('ipWidget_ipForm_field', {
                        label : data.label,
                        type : data.type,
                        status : data.status,
                        optionsPopup : options.optionsPopup,
                        options : data.options
                    });
                    $this.find('.ipaFieldLabel').val(data.label);
                    $this.find('.ipaFieldType').val(data.type);
                    if (options.optionsPopup.ipWidget_ipForm_options('optionsAvailable', data.type)) {
                        $this.find('.ipaFieldOptions').bind('click', function() {$(this).trigger('optionsClick.ipWidget_ipForm'); return false;});
                        $this.bind('optionsClick.ipWidget_ipForm', function() {$(this).ipWidget_ipForm_field('openOptionsPopup');});
                    } else {
                        $this.find('.ipaFieldOptions').hide();
                    }
                }
                
                $this.find('.ipaFieldRemove').bind('click', function(event){
                    event.preventDefault();
                    $this = $(this);
                    $this.trigger('removeClick.ipWidget_ipForm');
                });
                $this.bind('removeClick.ipWidget_ipForm', function(event) {
                    $this.trigger('removeField.ipWidget_ipForm', this);
                });
                return $this;
            });
        },
        
        openOptionsPopup : function () {
            $this = this;
            var data = $this.data('ipWidget_ipForm_field');
            $thisForEvent = $this;
            data.optionsPopup.bind('saveOptions.ipWidget_ipForm', function(e,options){
                $this = $(this); //we are in popup context
                $this.unbind('saveOptions.ipWidget_ipForm') 
                $thisForEvent.ipWidget_ipForm_field('setOptions', options);
            });
            
            data.optionsPopup.ipWidget_ipForm_options('showOptions', data.type, data.options);
        },
        
        setOptions : function (options) {
            var $this = this;
            var data = $this.data('ipWidget_ipForm_field');
            data.options = options;
            $this.data('ipWidget_ipForm_field', data);
        },
        
        getOptions : function () {
            var $this = $(this);
            var data = $this.data('ipWidget_ipForm_field');
            if (data.options) {
                return data.options;
            } else {
                return null;
            }
        },
        
        getLabel : function() {
            var $this = this;
            return $this.find('.ipaFieldLabel').val();
        },
        
        getType : function() {
            var $this = this;
            return $this.find('.ipaFieldType').val();
        },
            
        getStatus : function() {
            var $this = this;
            var tmpData = $this.data('ipWidget_ipForm_field');
            return tmpData.status;
        },
        
        setStatus : function(newStatus) {
            var $this = $this;
            var tmpData = $this.data('ipWidget_ipForm_field');
            tmpData.status = newStatus;
            $this.data('ipWidget_ipForm_field', tmpData);
            
        }
    };
    
    
    

    $.fn.ipWidget_ipForm_field = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipAdminWidgetButton');
        }

    };

})(jQuery);



/**
 * Options popup
 */
(function($) {

    var methods = {
        init : function(options) {
            if (!options) {
                options = {};
            }
            
            return this.each(function() {
                var $this = $(this);
                var data = $this.data('ipWidget_ipForm_options');
                // If the plugin hasn't been initialized yet
                if (!data) {
                    var data = {
                        fieldTypes : options.fieldTypes
                    };
                    $this.data('ipWidget_ipForm_options', data);
                }
                
                return $this;
            });
        },
        
        showOptions : function(fieldType, currentOptions) {
            var $this = this;
            var fieldType = $this.ipWidget_ipForm_options('getFieldType', fieldType);
            $this.html(fieldType.optionsHtml);
            $this.dialog({
                modal: true,
                buttons: {
                    "Save": function() {
                        var $this = $(this);
                        eval ('var options = ' + fieldType.optionsSaveFunction + '($this);');
                        $this.dialog( "close" );
                        $this.trigger('saveOptions.ipWidget_ipForm', [options]);
                    },
                    "Cancel": function() {
                        $( this ).dialog( "close" );
                    }
                }
                
            });
            eval ('' + fieldType.optionsInitFunction + '($this, currentOptions);');
        },
        
        
        
        getFieldType : function (fieldType) {
            var $this = this;
            var data = $this.data('ipWidget_ipForm_options');
            return data.fieldTypes[fieldType];
        },
        
        optionsAvailable : function (fieldTypeKey) {
            var $this = this;
            var fieldType = $this.ipWidget_ipForm_options('getFieldType', fieldTypeKey);
            return (fieldType && (fieldType.optionsInitFunction || fieldType.optionsHtml));
            
        }
        
        
    };
    

    
    $.fn.ipWidget_ipForm_options = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipAdminWidgetButton');
        }

    };

})(jQuery);

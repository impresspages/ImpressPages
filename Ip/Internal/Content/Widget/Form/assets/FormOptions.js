/**
 * @package ImpressPages
 *
 */


/**
 * Options popup
 */
(function($) {
    "use strict";

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
            $.error('Method ' + method + ' does not exist on jQuery.ipWidget_ipForm_options');
        }
    };

})(ip.jQuery);

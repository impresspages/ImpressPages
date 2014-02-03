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
            var $optionsModal = $this;
            var fieldType = $this.ipWidget_ipForm_options('getFieldType', fieldType);
            $this.find('.modal-body').html(fieldType.optionsHtml);

            var $confirm = $this.find('.ipsConfirm');
            //$('#ipWidgetFormPopup').modal('hide');
            $('#ipWidgetFormPopup').hide();


            $confirm.off().on('click', function() {
                var options = window[fieldType.optionsSaveFunction]($optionsModal);
                //$('#ipWidgetFormPopup').modal('show');
                $('#ipWidgetFormPopup').show();
                $optionsModal.modal( "hide" );
                $optionsModal.trigger('saveOptions.ipWidget_ipForm', [options]);
            });

            $optionsModal.on('hide.bs.modal', function() {
                //$('#ipWidgetFormPopup').modal('show');
                $('#ipWidgetFormPopup').show();
            });


            $this.modal();

            window[fieldType.optionsInitFunction]($this, currentOptions);
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

/**
 * @package ImpressPages
 *
 */


/**
 * Fields container
 */
(function($) {
    "use strict";

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
                    $this.html('');
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
                    $this.sortable({
                        handle: '.ipsFieldMove',
                        cancel: false
                    });
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



        getFields : function () {
            var $this = this;
            return $this.find('.ipsFieldTemplate');
        },

        destroy : function () {
            return this.each(function() {
                $.removeData(this, 'ipWidget_ipForm_container');
            });
        }

    };

    $.fn.ipWidget_ipForm_container = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipWidget_ipForm_container');
        }

    };

})(jQuery);

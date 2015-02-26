/**
 * @package ImpressPages
 *
 */


/**
 * General Field
 */
(function($) {
    "use strict";

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
                    data = {
                        label : '',
                        type : 'Text',
                        required : false,
                        status : 'new',
                        options : {}
                    };
                    if (options.label) {
                        data.label = options.label;
                    }
                    if (options.type) {
                        data.type = options.type;
                    }
                    if (options.required && options.required != '0' && options.required != 'false') {
                        data.required = true;
                    } else {
                        data.required = false;
                    }
                    if (options.status) {
                        data.status = options.status;
                    }
                    $this.data('ipWidget_ipForm_field', {
                        label : data.label,
                        type : data.type,
                        required : data.required,
                        status : data.status,
                        optionsPopup : options.optionsPopup
                    });

                    $this.find('.ipsFieldLabel').val(data.label);
                    $this.find('.ipsFieldType').val(data.type);
                    $this.find('.ipsFieldType').on('change', function (e) {
                        $.proxy(setFieldType, $this)($(this).val());
                    });
                    $this.find('.ipsFieldRequired').on('click', function () {
                        $this.find('.ipsFieldRequired').addClass('hidden');
                        $this.find('.ipsFieldOptional').removeClass('hidden');
                    });
                    $this.find('.ipsFieldOptional').on('click', function () {
                        $this.find('.ipsFieldOptional').addClass('hidden');
                        $this.find('.ipsFieldRequired').removeClass('hidden');
                    });

                    $.proxy(setFieldType, $this)(data.type);

                    if (options.options) {
                        $.proxy(setOptions, $this)(options.options);
                    }

                    if (options.required && options.required != 0) {
                        if (options.required) {
                            $this.find('.ipsFieldRequired').removeClass('hidden');
                            $this.find('.ipsFieldOptional').addClass('hidden');
                        } else {
                            $this.find('.ipsFieldRequired').addClass('hidden');
                            $this.find('.ipsFieldOptional').removeClass('hidden');
                        }
                    }
                }

                var $thisForEvent = $this;
                $this.find('.ipsFieldRemove').bind('click', function(event){
                    $thisForEvent.ipWidget_ipForm_field('setStatus', 'deleted');
                    $thisForEvent.hide();
                    event.preventDefault();

                    var $modal = $this.closest('.modal');
                    var $backdrop = $modal.children('.modal-backdrop');
                    var $dialog = $modal.children('.modal-dialog');

                    if($modal.height() < $dialog.outerHeight(true)) {
                        $backdrop.css('height', 0).css('height', $dialog.outerHeight(true));
                    } else {
                        $backdrop.css('height', 0).css('height', $modal.outerHeight(true));
                    }
                });
                return $this;
            });
        },



        getOptions : function () {
            var $this = $(this);
            var data = $this.data('ipWidget_ipForm_field');
            if (data.options && data.options[$this.ipWidget_ipForm_field('getType')]) {
                //store separte options for each type. Just to avoid accidental removal of options on type change
                //nevertheless only one type options will be stored to the database
                return data.options[$this.ipWidget_ipForm_field('getType')];
            } else {
                return null;
            }
        },

        getLabel : function() {
            var $this = this;
            return $this.find('.ipsFieldLabel').val();
        },

        getType : function() {
            var $this = this;
            return $this.find('.ipsFieldType').val();
        },


        getStatus : function() {
            var $this = this;
            var tmpData = $this.data('ipWidget_ipForm_field');
            return tmpData.status;
        },

        setStatus : function(newStatus) {
            var $this = this;
            var tmpData = $this.data('ipWidget_ipForm_field');
            tmpData.status = newStatus;
            $this.data('ipWidget_ipForm_field', tmpData);

        },

        getRequired : function () {
            var $this = $(this);
            return !$this.find('.ipsFieldRequired').hasClass('hidden');
        }
    };

    var openOptionsPopup = function () {
        var $this = $(this);
        var data = $this.data('ipWidget_ipForm_field');
        var $fieldContext = $this;
        data.optionsPopup.bind('saveOptions.ipWidget_ipForm', function(e,options){
            $this = $(this); //we are in popup context
            $this.unbind('saveOptions.ipWidget_ipForm');
            $.proxy(setOptions, $fieldContext)(options);
        });

        data.optionsPopup.ipWidget_ipForm_options('showOptions', data.type, $this.ipWidget_ipForm_field('getOptions'));
    };



    var setFieldType = function (type) {
        var $this = this;
        var data = $this.data('ipWidget_ipForm_field');
        if (data.optionsPopup.ipWidget_ipForm_options('optionsAvailable', type)) {
            //$this.find('.ipsFieldOptions').removeClass('disabled');
            $this.find('.ipsFieldOptions').removeClass('hidden');
            $this.find('.ipsFieldOptions').on('click', $.proxy(openOptionsPopup, this));
        } else {
            $this.find('.ipsFieldOptions').addClass('hidden');
            //$this.find('.ipsFieldOptions').addClass('disabled');
        }
        data.type = type;
        $this.data('ipWidget_ipForm_field', data);
    };


    var setOptions = function (options) {
        var $this = this;

        var data = $this.data('ipWidget_ipForm_field');
        if (!data.options) {
            data.options = {};
        }
        data.options[$this.ipWidget_ipForm_field('getType')] = options; //store separte options for each type. Just to avoid accidental removal of options on type change
        $this.data('ipWidget_ipForm_field', data);
    };

    $.fn.ipWidget_ipForm_field = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipWidget_ipForm_field');
        }

    };

})(jQuery);

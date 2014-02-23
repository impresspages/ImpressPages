// defining global variables
var validatorConfig = '';
var ipValidator;

(function($){
    "use strict";

    ipValidator = new function() {
        this.init = function() {
            $.each(ipValidatorTranslations, function(key, value) {
                if (validatorConfig === '') {
                    validatorConfig = createConfig(key);
                }
                $.tools.validator.localize(key, value);
            });
        };

        var createConfig = function(translationsKey) {
            var config = {
                'lang' : ip.languageUrl,
                //'errorClass' : 'ipmControlError',
                'messageClass' : 'ipmErrorMessage',
                //'position' : 'bottom left',
                //'offset' : [-3, 0],
                'onFail' : function(e, errors) {
                    $.each(errors, function() {
                        var err = this;
                        var $control = this.input;
                        $control.parents('.form-group')
                            .addClass('has-error')
                            .find('.help-error').html(this.messages.join(' '));
                        if (this.messages.join('') == '') {
                            //hide error if no error text present
                            $control.parents('.form-group').find('.help-error').hide()
                        } else {
                            $control.parents('.form-group').find('.help-error').show()
                        }
                    });
                },
                'onSuccess' : function(e, valids) {
                    $.each(valids, function() {
                        var $control = $(this);
                        $control.parents('.form-group').removeClass('has-error');
                    });
                }
            };
            return config;
        };

    };

    ipValidator.init();
})(jQuery);

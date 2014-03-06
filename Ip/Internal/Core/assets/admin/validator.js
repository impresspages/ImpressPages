// defining global variables
var validatorConfigAdmin = '';


(function($){
    "use strict";

    var createConfig = function(translationsKey) {
        var config = {
            'lang' : ip.languageUrl,
            //'errorClass' : 'ipmControlError',
            'messageClass' : 'hidden', //hide default jqueryTools absolutely positioned error message
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

    $.each(ipValidatorTranslations, function(key, value) {
        if (validatorConfigAdmin === '') {
            validatorConfigAdmin = createConfig(key);
        }
        $.tools.validator.localize(key, value);
    });


    $('.ipsModuleFormAdmin.ipsAjaxSubmit').validator(validatorConfigAdmin);
    $('.ipsModuleFormAdmin.ipsAjaxSubmit').submit(function (e) {
        var $form = $(this);

        // client-side validation OK.
        if (!e.isDefaultPrevented()) {
            $.ajax({
                url: ip.baseUrl,
                dataType: 'json',
                type : 'POST',
                data: $form.serialize(),
                success: function (response) {
                    $form.trigger('ipSubmitResponse', [response]);
                    //PHP controller says there are some errors
                    if (response.errors) {
                        form.data("validator").invalidate(response.errors);
                    }
                    if (response.redirectUrl) {
                        window.location = response.redirectUrl;
                    }
                },
                error: function (response) {
                    if (ip.developmentEnvironment || ip.debugMode) {
                        console.log(response);
                        alert('Server response: ' + response.responseText);
                    }
                }
            });
        }
        e.preventDefault();
    });


})(ip.jQuery);

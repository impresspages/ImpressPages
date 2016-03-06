// defining global variables
var validatorConfigAdmin = '';


(function ($) {
    "use strict";

    var createConfig = function (translationsKey) {
        var config = {
            'lang': ip.languageCode,
            //'errorClass' : 'ipmControlError',
            'messageClass': 'hidden', //hide default jqueryTools absolutely positioned error message
            //'position' : 'bottom left',
            //'offset' : [-3, 0],
            'onFail': function (e, errors) {
                $.each(errors, function () {
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

                if (!isScrolledIntoView(errors[0].input) && !$(errors[0].input).hasClass('ipsDisableAutoscrollOnError')) {
                    $('html, body').animate({
                        scrollTop: Math.max($(errors[0].input).offset().top - 70, 0)
                    }, 300);

                }
                $(e.target).trigger('ipOnFail', [e, errors]);
            },
            'onSuccess': function (e, valids) {
                $.each(valids, function () {
                    var $control = $(this);
                    $control.parents('.form-group').removeClass('has-error');
                });
            }
        };
        return config;
    };


    var isScrolledIntoView = function (elem)
    {
        var docViewTop = $(window).scrollTop();
        var docViewBottom = docViewTop + $(window).height();

        var elemTop = $(elem).offset().top;
        var elemBottom = elemTop + $(elem).height();

        return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
    }

    $.each(ipValidatorTranslations, function (key, value) {
        if (validatorConfigAdmin === '') {
            validatorConfigAdmin = createConfig(key);
        }
        $.tools.validator.localize(key, value);
    });


})(jQuery);

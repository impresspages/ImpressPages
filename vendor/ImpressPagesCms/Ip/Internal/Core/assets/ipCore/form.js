/*!
 * ImpressPages form initialization function
 *
 */

// defining global variables
var ipModuleFormPublic;

(function ($) {
    "use strict";

    function isScrolledIntoView(elem)
    {
        var docViewTop = $(window).scrollTop();
        var docViewBottom = docViewTop + $(window).height();

        var elemTop = $(elem).offset().top;
        var elemBottom = elemTop + $(elem).height();

        return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
    }


    ipModuleFormPublic = new function () {
        this.init = function () {

            //if interactive file upload input found, load file upload javascript
            if ($('.ipsModuleFormPublic .ipsFileContainer').length && (typeof(plupload) === "undefined")) {
                $('body').append($('<script type="text/javascript" src="' + ipFileUrl('Ip/Internal/Core/assets/ipCore/plupload/plupload.full.js') + '"></script>'));
            }


            if (($('.ipsModuleFormPublic .type-color').length || $('.ipsModuleFormAdmin .type-color').length) && !$.fn.colorpicker) {
                $('body').append('<script type="text/javascript" src="' + ipFileUrl('Ip/Internal/Core/assets/js/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js') + '"></script>');
                $('head').append('<link rel="stylesheet" href="' + ipFileUrl('Ip/Internal/Core/assets/js/bootstrap-colorpicker/css/bootstrap-colorpicker.css') + '" type="text/css" />');
            }

            if (($('.ipsModuleFormPublic .type-richText').length || $('.ipsModuleFormPublic .type-richTextLang').length) && (typeof(ipTinyMceConfigPublic) === "undefined")) {
                $('body').append('<script type="text/javascript" src="' + ipFileUrl('Ip/Internal/Core/assets/js/tiny_mce/jquery.tinymce.min.js') + '"></script>');
                $('body').append('<script type="text/javascript" src="' + ipFileUrl('Ip/Internal/Core/assets/js/tiny_mce/tinymce.min.js') + '"></script>');
                $('body').append('<script type="text/javascript" src="' + ipFileUrl('Ip/Internal/Core/assets/tinymce/defaultPublic.js') + '"></script>');
            }

            $('.ipsModuleFormPublic .ipsFileContainer').ipFormFile();
            $('.ipsModuleFormPublic .type-richText').ipFormRichtext();
            $('.ipsModuleFormPublic .type-richTextLang .input-group').ipFormRichtext();
            $('.ipsModuleFormPublic .type-color').ipFormColor();

            $('.ipsModuleFormAdmin .ipsFileContainer').ipFormFile();
            $('.ipsModuleFormAdmin .type-richText').ipFormRichtext();
            $('.ipsModuleFormAdmin .type-richTextLang .input-group').ipFormRichtext();
            $('.ipsModuleFormAdmin .type-color').ipFormColor();
            $('.ipsModuleFormAdmin .ipsRepositoryFileContainer').ipFormRepositoryFile();
            $('.ipsModuleFormAdmin .type-url').ipFormUrl();

            $(document).trigger('ipInitForms');


            // adding dumb submit element for 'enter' to trigger form submit
            $('.ipsModuleFormPublic, .ipsModuleFormAdmin').each(function () {
                var $form = $(this);
                if ($form.find(":submit").length == 0) {
                    $form.append('<input type="submit" style="position: absolute; left: -999999px; width: 1px; height: 1px; visibility: hidden;" tabindex="-1" />');
                }
            });

            $('.ipsModuleFormPublic.ipsAjaxSubmit').validator(validatorConfigPublic);
            if (typeof(validatorConfigAdmin) !== 'undefined') {
                $('.ipsModuleFormAdmin.ipsAjaxSubmit').validator(validatorConfigAdmin);
            }
            $('.ipsAjaxSubmit').off('submit.ipSubmit').on('submit.ipSubmit', function (e) {
                var $form = $(this);
                var type = 'GET';

                if ($form.attr('method') && $form.attr('method').toUpperCase() == 'POST') {
                    type = 'POST';
                }

                // client-side validation OK.
                if (!e.isDefaultPrevented()) {
                    $form.trigger('ipSubmit');
                    $.ajax({
                        url: $form.attr('action') ? $form.attr('action') : ip.baseUrl,
                        dataType: 'json',
                        type: type,
                        data: $form.serialize(),
                        success: function (response) {
                            $form.trigger('ipSubmitResponse', [response]);
                            //PHP controller says there are some errors
                            if (response.errors) {
                                $form.data("validator").invalidate(response.errors);
                            }
                            if (response.replaceHtml) {
                                $form.replaceWith(response.replaceHtml);
                                if (!isScrolledIntoView($form)) {
                                    $('html, body').animate({
                                        scrollTop: $form.offset().top
                                    }, 500);
                                }
                                ipInitForms();
                            }
                            if (response.redirectUrl) {
                                window.location = response.redirectUrl;
                            }
                            if (response.reload) {
                                window.location = window.location.href.split('#')[0];
                            }
                            if (response.alert) {
                                alert(response.alert);
                            }
                        },
                        error: function (response) {
                            if (ip.developmentEnvironment || ip.debugMode) {
                                alert('Error: ' + response.responseText);
                            }
                        }
                    });
                }
                e.preventDefault();
            });


        };
    };
})(jQuery);



/*!
 * ImpressPages form initialization function
 *
 */

// defining global variables
var ipModuleFormPublic;

(function($){
    "use strict";


    ipModuleFormPublic = new function () {
        this.init = function () {

            //if interactive file upload input found, load file upload javascript
            if ($('.ipsModuleFormPublic .ipsFileContainer').length && (typeof(plupload) === "undefined")) {
                $('body').append($('<script type="text/javascript" src="' + ipFileUrl('Ip/Internal/Core/assets/ipCore/plupload/plupload.full.js') + '"></script>'));
            }


            if ($('.ipsColorPicker').length && !$.spectrum) {
                $('body').append('<script type="text/javascript" src="' + ipFileUrl('Ip/Internal/Core/assets/ipCore/spectrum/spectrum.min.js') + '"></script>');
                $('head').append('<link rel="stylesheet" href="' + ipFileUrl('Ip/Internal/Core/assets/ipCore/spectrum/spectrum.css') + '" type="text/css" />');
            }


            $('.ipsModuleFormPublic .ipsFileContainer').ipFormFile();
            $('.ipsModuleFormPublic .type-richtext').ipFormRichtext();
            $('.ipsModuleFormPublic .type-color').ipFormColor();

            $('.ipsModuleFormAdmin .ipsFileContainer').ipFormFile();
            $('.ipsModuleFormAdmin .type-richtext').ipFormRichtext();
            $('.ipsModuleFormAdmin .type-color').ipFormColor();
            $('.ipsModuleFormAdmin .ipsRepositoryFileContainer').ipFormRepositoryFile();
            $('.ipsModuleFormAdmin .type-url').ipFormUrl();

            $(document).trigger('ipInitForms');



            // adding dumb submit element for 'enter' to trigger form submit
            $('.ipsModuleFormPublic, .ipsModuleFormAdmin').each(function(){
                var $form = $(this);
                if ($form.find(":submit").length==0) {
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
                    $.ajax({
                        url: ip.baseUrl,
                        dataType: 'json',
                        type : type,
                        data: $form.serialize(),
                        success: function (response) {
                            $form.trigger('ipSubmitResponse', [response]);
                            //PHP controller says there are some errors
                            if (response.errors) {
                                $form.data("validator").invalidate(response.errors);
                            }
                            if (response.replaceHtml) {
                                $form.replaceWith(response.replaceHtml);
                            }
                            if (response.redirectUrl) {
                                window.location = response.redirectUrl;
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



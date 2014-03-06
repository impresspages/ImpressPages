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
            //TODOX on some servers files are loaded in random order. Problem when plupload and file are loaded at the same time. Or color and spectrum.

            //if interactive file upload input found, load file upload javascript
            if ($('.ipsModuleFormPublic .ipsFileContainer').length && (typeof(plupload) === "undefined")) {
                $('body').append($('<script type="text/javascript" src="' + ipFileUrl('Ip/Internal/Core/assets/ipCore/plupload/plupload.full.js') + '"></script>'));
            }


            if ($('.ipsModuleFormPublic .ipsColorPicker').length && !$.spectrum) {
                $('body').append('<script type="text/javascript" src="' + ipFileUrl('Ip/Internal/Core/assets/ipCore/spectrum/spectrum.min.js') + '"></script>');
                $('head').append('<link rel="stylesheet" href="' + ipFileUrl('Ip/Internal/Core/assets/ipCore/spectrum/spectrum.css') + '" type="text/css" />');
            }


            $('.ipsModuleFormPublic .ipsFileContainer').ipFormFile();
            $('.ipsModuleFormPublic .type-richtext').ipFormRichtext();
            $('.ipsModuleFormPublic .type-color').ipFormColor();


            // adding dumb submit element for 'enter' to trigger form submit
            $('.ipsModuleFormPublic').each(function(){
                var $form = $(this);
                if ($form.find(":submit").length==0) {
                    $form.append('<input type="submit" style="position: absolute; left: -999999px; width: 1px; height: 1px; visibility: hidden;" tabindex="-1" />');
                }
            });

            $('.ipsModuleFormPublic.ipsAjaxSubmit').validator(validatorConfigPublic);
            $('.ipsModuleFormPublic.ipsAjaxSubmit').off('submit.ipSubmit').on('submit.ipSubmit', function (e) {
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
                                $form.data("validator").invalidate(response.errors);
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

        };
    };
})(jQuery);



/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */


"use strict";

$(document).ready(function () {
    $('.ipModuleInlineManagement').ipModuleInlineManagement();
});


(function ($) {

    var methods = {
        init:function (options) {

            return this.each(function () {
                var $this = $(this);

                $this.find('.ipmEdit').bind('click', function(event){ event.preventDefault(); $(this).trigger('ipModInlineManagement.openEditPopup');});
                $this.bind('ipModInlineManagement.openEditPopup', $.proxy(methods.openEditPopup, $this ));

            });
        },

        openEditPopup:function(event) {
            event.preventDefault();

            return this.each(function(){
                var $this = $(this);

                $('.ipModuleInlineManagementPopup').remove();

                $this.append('<div class="ipModuleInlineManagementPopup" ></div>');


                var $popup = $this.find('.ipModuleInlineManagementPopup');
                $popup.dialog({width: 600, height : 450, modal: false}); //modal true - makes plupload browse button don't work
                $popup.ipInlineManagementLogo('refresh');



            });

        },


        _popupContentResponse:function(response) {
            return this.each(function(){
                if (response.status == 'success') {
                    $('.ipModuleInlineManagementPopup').html(response.html);
                }
            });
        }





    };


    $.fn.ipModuleInlineManagement = function (method) {

        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipModuleInlineManagement');
        }

    };

})(jQuery);


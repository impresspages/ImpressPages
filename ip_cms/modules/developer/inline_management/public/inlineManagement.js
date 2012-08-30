/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */


"use strict";

$(document).ready(function () {
    $('.ipModuleInlineManagement').ipModuleInlineManagement();
    $('.ipModuleInlineManagementString').ipModuleInlineManagementString();
    $('.ipModuleInlineManagementText').ipModuleInlineManagementText();
    $('.ipModuleInlineManagementImage').ipModuleInlineManagementImage();
});


(function ($) {

    var methods = {
        init:function (options) {

            return this.each(function () {
                var $this = $(this);

                // Creating global controls block
                if (!$('.ipModuleInlineManagementControls').length) {
                    $('body')
                    .append(ipModInlineManagementControls);
                }
                // Enabling controls as tooltip
                $this.tooltip({
                    position : 'top left',
                    tip : '.ipModuleInlineManagementControls',
                    onShow : function() {
//                        $this.expose({
//                            zIndex: 98
//                        });
                    },
                    onHide : function() {
                        $.mask.close();
                    }
                });

                var data = $this.data('ipModuleInlineManagement');
                // If the plugin hasn't been initialized yet
                if ( ! data ) {
                    var $controls = $('.ipModuleInlineManagementControls');
                    $this.mouseenter(function(event){
                        $controls.find('.ipActionWidgetManage').unbind('click').bind('click', function(event){
                            event.preventDefault();
                            $this.trigger('ipModuleInlineManagement.openEditPopup');
                        });
                    });
                    $this.bind('ipModuleInlineManagement.openEditPopup', $.proxy(methods.openEditPopup, $this ));

                }

            });
        },

        openEditPopup:function(event) {
            event.preventDefault();

            return this.each(function(){
                var $this = $(this);

                $('.ipModuleInlineManagementPopup').remove();

                $this.append('<div class="ipModuleInlineManagementPopup" ></div>');


                var $popup = $this.find('.ipModuleInlineManagementPopup');
                $popup.dialog({width: 800, height : 450, modal: true});

                var options = {
                    cssClass: $this.data('cssclass')
                };


                $popup.ipInlineManagementLogo(options);
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


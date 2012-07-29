/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */


"use strict";

$(document).ready(function () {
    $('.ipModuleInlineManagementString').ipModuleInlineManagementString();
});


(function ($) {

    var methods = {
        init:function (options) {

            return this.each(function () {
                var $this = $(this)

                console.log($this.data());

                //    data = $this.data('tooltip'),


                // If the plugin hasn't been initialized yet
//                if (!data) {
//
//                    /*
//                     Do more setup stuff here
//                     */
//
//                    $(this).data('tooltip', {
//                        target:$this,
//                        tooltip:tooltip
//                    });
//
//                }
            });
        }
    };


    $.fn.ipModuleInlineManagementString = function (method) {

        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.tooltip');
        }

    };

})(jQuery);
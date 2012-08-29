/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

"use strict";


(function($) {

    var methods = {
        init : function(options) {
            return this.each(function() {
                var $this = $(this);
                var data = $this.data('ipInlineManagementString');
                // If the plugin hasn't been initialized yet
                if ( ! data ) {
                    $this.data('ipInlineManagementString', {
                        key: $this.data('key'),
                        cssClass: $this.data('cssclass'),
                        htmlTag: $this.data('htmltag')
                    });
                    $this.closest('.ipmEdit').bind('click', $.proxy(methods.openPopup, $this));
                }
            });
        },
        

        openPopup : function () {
            var $this = this;
            $this.find('.ipModuleInlineManagementPopupString').remove();

            $this.append('<div class="ipModuleInlineManagementPopupString" ></div>');


            var $popup = $this.find('.ipModuleInlineManagementPopupString');
            $popup.dialog({width: 800, height : 250, modal: true});

            $.proxy(methods.refresh, $this)();
        },

        refresh : function () {
            var $this = this;
            var data = Object();
            data.g = 'developer';
            data.m = 'inline_management';
            data.a = 'getManagementPopupString';

            data.key = $this.data('ipInlineManagementString').key;

            var urlParts = window.location.href.split('#');
            var postUrl = urlParts[0];

            $.ajax({
                type : 'POST',
                url : postUrl,
                data : data,
                context : $this,
                success : methods._refreshResponse,
                dataType : 'json'
            });
        },

        _refreshResponse : function (response) {
            var $this = this;
            if (response.status == 'success') {

                $('.ipModuleInlineManagementPopupString').html(response.html);
                $('.ipModuleInlineManagementPopupString').tabs('destroy');
                $('.ipModuleInlineManagementPopupString').tabs();


            }

            $('.ipModuleInlineManagementPopupString').find('.ipaConfirm').bind('click', jQuery.proxy(methods._confirm, $this));
            $('.ipModuleInlineManagementPopupString').find('.ipaCancel').bind('click', jQuery.proxy(methods._cancel, $this));
        },


        _confirm : function (event) {
            event.preventDefault();
            var $this = $(this);
            $this.trigger('ipInlineManagement.logoConfirm');
            var data = Object();
            data.g = 'developer';
            data.m = 'inline_management';
            data.a = 'saveString';

            data.cssClass = $this.data('ipInlineManagementString').cssClass;
            data.key = $this.data('ipInlineManagementString').key;
            data.htmlTag = $this.data('ipInlineManagementString').htmlTag;

            $('.ipModuleInlineManagementPopupString').find('textarea').each(
                function () {
                    if (data['values'] == undefined) {
                        data['values'] = {};
                    }
                    data['values'][$(this).data('languageid')] = $(this).val();
                }
            );

            var urlParts = window.location.href.split('#');
            var postUrl = urlParts[0];


            //SAVE
            $.ajax({
                type : 'POST',
                url : postUrl,
                data : data,
                context : $this,
                success : methods._confirmResponse,
                dataType : 'json'
            });
        },

        _confirmResponse : function (answer) {
            $this = this;

            if (answer && answer.status == 'success') {
                if (answer.stringHtml) {
                    $this.closest('.ipmEdit').replaceWith(answer.stringHtml);
                }
                $this.trigger('ipInlineManagement.stringConfirm');
                $('.ipModuleInlineManagementPopupString').dialog('close');
            }
        },

        _cancel : function (event) {
            event.preventDefault();
            var $this = this;
            $this.trigger('ipInlineManagement.stringCancel');
            $('.ipModuleInlineManagementPopupString').dialog('close');
        }
        
        

        
    };
    
    

    $.fn.ipModuleInlineManagementString = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipInlineManagementString');
        }
    };
    
    

})(jQuery);
/**
 * @package ImpressPages
 *
 */

(function($) {
    "use strict";

    var methods = {
        init : function(options) {
            return this.each(function() {
                var $this = $(this);
                var data = $this.data('ipInlineManagementString');
                // If the plugin hasn't been initialized yet
                if ( ! data ) {
                    $this
                    .data('ipInlineManagementString', {
                        key: $this.data('key'),
                        cssClass: $this.data('cssclass'),
                        htmlTag: $this.data('htmltag'),
                        defaultValue: $this.data('defaultvalue')
                    })
                    .ipModuleInlineManagementControls({
                        'Manage' : function() {
                            $this.trigger('ipModuleInlineManagement.openEditPopup');
                        }
                    })
                    .bind('ipModuleInlineManagement.openEditPopup', $.proxy(methods.openPopup, $this ));

                }
            });
        },

        openPopup : function () {
            var $this = this;
            $this.find('.ipModuleInlineManagementPopup.ipmString').remove();

            $this.append('<div class="ipModuleInlineManagementPopup ipmString" ></div>');

            var $popup = $this.find('.ipModuleInlineManagementPopup.ipmString');
            $popup.dialog({width: 800, height : 250, modal: true});

            $.proxy(methods.refresh, $this)();
        },

        refresh : function () {
            var $this = this;
            var data = Object();
            data.aa = 'InlineManagement.getManagementPopupString';
            data.securityToken = ip.securityToken;
            data.key = $this.data('ipInlineManagementString').key;
            data.defaultValue = $this.data('ipInlineManagementString').defaultValue;



            $.ajax({
                type : 'POST',
                url : ip.baseUrl,
                data : data,
                context : $this,
                success : methods._refreshResponse,
                dataType : 'json'
            });
        },

        _refreshResponse : function (response) {
            var $this = this;
            if (response.status == 'success') {
                $('.ipModuleInlineManagementPopup.ipmString').html(response.html);
                $('.ipModuleInlineManagementPopup.ipmString').tabs();
                $('.ipModuleInlineManagementPopup.ipmString').tabs('select', 'ipInlineManagementStringTabs-' + response.curLanguageId);

            }

            $('.ipModuleInlineManagementPopup.ipmString').find('.ipaConfirm').bind('click', $.proxy(methods._confirm, $this));
            $('.ipModuleInlineManagementPopup.ipmString').find('.ipaCancel').bind('click', $.proxy(methods._cancel, $this));
        },

        _confirm : function (event) {
            event.preventDefault();
            var $this = $(this);
            $this.trigger('ipInlineManagement.logoConfirm');
            var data = Object();
            data.aa = 'InlineManagement.saveString';
            data.securityToken = ip.securityToken;

            data.cssClass = $this.data('ipInlineManagementString').cssClass;
            data.key = $this.data('ipInlineManagementString').key;
            data.htmlTag = $this.data('ipInlineManagementString').htmlTag;

            $('.ipModuleInlineManagementPopup.ipmString').find('textarea').each(
                function () {
                    if (data['values'] == undefined) {
                        data['values'] = {};
                    }
                    data['values'][$(this).data('languageid')] = $(this).val();
                }
            );



            //SAVE
            $.ajax({
                type : 'POST',
                url : ip.baseUrl,
                data : data,
                context : $this,
                success : methods._confirmResponse,
                dataType : 'json'
            });
        },

        _confirmResponse : function (answer) {
            var $this = this;

            if (answer && answer.status == 'success') {
                if (answer.stringHtml) {
                    var $newElement = $(answer.stringHtml)
                    $this.replaceWith($newElement);
                    $newElement.ipModuleInlineManagementString();
                }
                $this.trigger('ipInlineManagement.stringConfirm');
                $('.ipModuleInlineManagementPopup.ipmString').dialog('close');
            }
        },

        _cancel : function (event) {
            event.preventDefault();
            var $this = this;
            $this.trigger('ipInlineManagement.stringCancel');
            $('.ipModuleInlineManagementPopup.ipmString').dialog('close');
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

})(ip.jQuery);

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
                var data = $this.data('ipInlineManagementText');
                // If the plugin hasn't been initialized yet
                if ( ! data ) {
                    $this
                    .data('ipInlineManagementText', {
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
            $this.find('.ipModuleInlineManagementPopupText').remove();

            $this.append('<div class="ipModuleInlineManagementPopupText" ></div>');

            var $popup = $this.find('.ipModuleInlineManagementPopupText');
            $popup.dialog({width: 800, height : 450, modal: true});

            $.proxy(methods.refresh, $this)();
        },

        refresh : function () {
            var $this = this;
            var data = Object();
            data.aa = 'InlineManagement.getManagementPopupText';
            data.securityToken = ip.securityToken;
            data.key = $this.data('ipInlineManagementText').key;
            data.defaultValue = $this.data('ipInlineManagementText').defaultValue;



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

                $('.ipModuleInlineManagementPopupText').html(response.html);
                $('.ipModuleInlineManagementPopupText').tabs();
                $('.ipModuleInlineManagementPopupText').tabs('select', 'ipInlineManagementTextTabs-' + response.curLanguageId);


                var tinyMceConfig = ipTinyMceConfig();
                tinyMceConfig.plugins = tinyMceConfig.plugins.replace(',autoresize', '').replace('autoresize,', '').replace('autoresize', '');
                tinyMceConfig.height = 300;
                $('.ipModuleInlineManagementPopupText').find('textarea').tinymce(tinyMceConfig);
            }

            $('.ipModuleInlineManagementPopupText').find('.ipaConfirm').bind('click', $.proxy(methods._confirm, $this));
            $('.ipModuleInlineManagementPopupText').find('.ipaCancel').bind('click', $.proxy(methods._cancel, $this));
        },

        _confirm : function (event) {
            event.preventDefault();
            var $this = $(this);
            $this.trigger('ipInlineManagement.logoConfirm');
            var data = Object();
            data.aa = 'InlineManagement.saveText';
            data.securityToken = ip.securityToken;

            data.cssClass = $this.data('ipInlineManagementText').cssClass;
            data.key = $this.data('ipInlineManagementText').key;
            data.htmlTag = $this.data('ipInlineManagementText').htmlTag;

            $('.ipModuleInlineManagementPopupText').find('textarea').each(
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
                    $newElement.ipModuleInlineManagementText();
                }
                $this.trigger('ipInlineManagement.stringConfirm');
                $('.ipModuleInlineManagementPopupText').dialog('close');
            }
        },

        _cancel : function (event) {
            event.preventDefault();
            var $this = this;
            $this.trigger('ipInlineManagement.textCancel');
            $('.ipModuleInlineManagementPopupText').dialog('close');
        }

    };

    $.fn.ipModuleInlineManagementText = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipInlineManagementText');
        }
    };

})(ip.jQuery);

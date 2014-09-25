/**
 * @package ImpressPages
 *
 */

(function ($) {
    "use strict";

    var methods = {
        init: function (options) {
            return this.each(function () {
                var $this = $(this);
                var data = $this.data('ipInlineManagementText');
                // If the plugin hasn't been initialized yet
                if (!data) {
                    $this.data('ipInlineManagementText', {
                        key: $this.data('key'),
                        cssClass: $this.data('cssclass'),
                        htmlTag: $this.data('htmltag'),
                        defaultValue: $this.data('defaultvalue')
                    });
                    var cssClass = $this.data('ipInlineManagementText').cssClass;
                    var key = $this.data('ipInlineManagementText').key;
                    var htmlTag = $this.data('ipInlineManagementText').htmlTag;
                    var customTinyMceConfig = $this.data('tinyMceConfig');
                    if (!customTinyMceConfig) {
                        if (typeof(ipInlineManagementTinyMceConfig) !== 'undefined') {
                            var options = {
                                id: $this.data('key'),
                                class: $this.data('cssclass'),
                                tag: $this.data('htmltag'),
                                default: $this.data('defaultvalue')
                            }
                            customTinyMceConfig = ipInlineManagementTinyMceConfig(options);
                        }
                        if (!customTinyMceConfig) {
                            customTinyMceConfig = ipTinyMceConfig();
                        }
                    }
                    customTinyMceConfig.setup = function (ed, l) {
                        ed.on('change', function (e) {
                            save($this.html(), key, cssClass, htmlTag);
                        })
                    };
                    $this.tinymce(customTinyMceConfig);
                }
            });
        }






    };

    var save = function (html, key, cssClass, htmlTag) {
        var $this = $(this);
        var data = Object();
        data.aa = 'InlineManagement.saveText';
        data.securityToken = ip.securityToken;

        data.cssClass = cssClass;
        data.key = key;
        data.htmlTag = htmlTag;

        data.value = html;
        data.languageId = ip.languageId;

        //SAVE
        $.ajax({
            type: 'POST',
            url: ip.baseUrl,
            data: data,
            context: $this,
            success: saveResponse,
            dataType: 'json'
        });
    };

    var saveResponse = function (answer) {
        var $this = this;

        if (answer && answer.status == 'success') {
            if (answer.stringHtml) {
                var $newElement = $(answer.stringHtml);
                $this.replaceWith($newElement);
                $newElement.ipModuleInlineManagementText();
            }
            $this.trigger('ipInlineManagement.stringConfirm');
            $('.ipModuleInlineManagementPopupText').dialog('close');
        }
    };


    $.fn.ipModuleInlineManagementText = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipInlineManagementText');
        }
    };

})(jQuery);

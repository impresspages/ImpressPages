/**
 * @package ImpressPages
 *
 */


    var IpWidget_Text = function() {
        "use strict";
        this.$widgetObject = null;

        this.init = function($widgetObject, data) {
            var customTinyMceConfig = ipTinyMceConfig();
            this.$widgetObject = $widgetObject;
            customTinyMceConfig.setup = function(ed, l) {ed.on('change', function(e) {
                $widgetObject.save({text: $widgetObject.find('.ipsContainer').html()});
            })};

            $widgetObject.find('.ipsContainer').tinymce(customTinyMceConfig);
            $widgetObject.find('.ipsContainer').attr('spellcheck', true);

            // hiding active editor to make sure it doesn't appear on top of repository window
            $(document).on('ipWidgetAdded', function(e, data) {
                if (tinymce.activeEditor.theme.panel) {
                    tinymce.activeEditor.theme.panel.hide();
                }
            });
        };

        this.onAdd = function () {
            this.$widgetObject.find('.ipsContainer').focus();
        };

        this.splitParts = function () {
            return this.$widgetObject.find('.ipsContainer > *');
        };

        this.splitData = function (curData, position) {
            //we ignore curData value as it holds data from the database. While actual data in editor might be already changed
            var recentData = {text: this.$widgetObject.find('.ipsContainer').html()};
            var paragraphs = this.splitParts();

            var text1 = '';
            var text2 = '';
            $.each(paragraphs, function(key, paragraph) {
                var $paragraph = $(paragraph);
                var $div = $('<div></div>').append($paragraph);
                if (key < position - 1) {
                    text1 = text1 + $div.html();
                } else {
                    text2 = text2 + $div.html();
                }
            });

            return [{text: text1}, {text: text2}];
        }


    };

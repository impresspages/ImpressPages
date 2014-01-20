/**
 * @package ImpressPages
 *
 */
var IpWidget_Title;

(function($){
    "use strict";

    IpWidget_Title = function() {
        this.$widgetObject = null;
        this.data = null;
        this.$header = null;
        this.$controls = null;

        this.init = function ($widgetObject, data) {
            var thisScope = this;
            this.$widgetObject = $widgetObject;
            this.data = data;
            this.$header = $widgetObject.find('h1,h2,h3,h4,h5,h6');
            this.$controls = $('#ipWidgetTitleControls');

            this.$header.tinymce(this.tinyMceConfig());

            this.$header.on('focus', $.proxy(this.focus, this));
            if (!data.level) {
                this.data.level = 1;
            }
            this.$widgetObject.on('remove', $.proxy(this.destroy, this));

            if (!this.$header.css('min-height') || this.$header.css('min-height') == '0px') {
                this.$header.css('min-height', this.$header.css('font-size')); //Firefox can't handle focus without min height defined
            }

        };

        this.onAdd = function () {
            var $headTag = this.$widgetObject.find('h1,h2,h3,h4,h5,h6');

            $headTag.focus();
        }

        this.focus = function () {
            this.initControls();
        }

        this.blur = function(e) {
            if ($.contains(this.$widgetObject[0], e.target) || this.$widgetObject[0] == e.target) {
                console.log('inside');
                //mouse click inside the widget
                return;
            } else {
                //mouse click outside of the widget
                if ($.contains(this.$controls[0], e.target) || $.contains($('#ipWidgetTitleOptions')[0], e.target)) {
                    //widget toolbar click or widget popup click
                    //do nothing
                } else {
                    this.removeControls()
                }
            }

        };

        this.removeControls = function() {
            this.$controls.addClass('hide');
            this.$controls.find('.ipsH').off();
            this.$controls.find('.ipsOptions').off();
            $('body').off('click.ipWidgetTitle');
        }

        this.destroy = function() {
            this.removeControls();
        };

        this.saveOptions = function (data) {
            this.data.anchor = data.anchor;
            this.save(false);
        }

        this.openOptions = function () {
            var $modal = $('#ipWidgetTitleOptions');
            $modal.removeClass('hide');
            $modal.ipWidgetTitleModal({anchor: this.data.anchor, saveCallback: $.proxy(this.saveOptions, this)});
        };

        this.initControls = function () {
            var $controls = this.$controls;
            var $widgetObject = this.$widgetObject;
            $controls.removeClass('hide');
            $controls.css('left', $widgetObject.offsetLeft);
            $controls.css('top', $widgetObject.offsetTop);
            $controls.css('position', 'absolute');
            $controls.css('left', $widgetObject.offset().left);
            $controls.css('top', $widgetObject.offset().top - $controls.height() - 5);
            $controls.find('.ipsH').on('click', $.proxy(this.levelPressed, this));

            $controls.find('.ipsH').removeClass('active');
            $controls.find('.ipsH[data-level="' + this.data.level + '"]').addClass('active');
            $controls.find('.ipsOptions').on('click', $.proxy(this.openOptions, this));
            $('body').on('click.ipWidgetTitle', $.proxy(this.blur, this));
        };

        this.levelPressed = function (e) {
            this.removeControls();
            this.data.level = $(e.currentTarget).data('level');
            this.save(true);
        };

        this.save = function (refresh) {
            var saveData = {
                title: this.$widgetObject.find('h1,h2,h3,h4,h5,h6').text(),
                level: this.data.level,
                anchor: this.data.anchor
            };
            this.$widgetObject.save(saveData, refresh, function($widget){
                $widget.find('h1,h2,h3,h4,h5,h6').focus();
            });
        };

        this.tinyMceConfig = function () {
            var self = this;
            var customTinyMceConfig = ipTinyMceConfig();
            customTinyMceConfig.menubar = false;
            customTinyMceConfig.toolbar = false;
            customTinyMceConfig.setup = function(ed, l) {
                ed.on('change', function(e){$.proxy(self.save, self)(false)});
                //ed.on('init', function(){$(this).trigger('init.tinymce')});
            };
            customTinyMceConfig.paste_as_text = true;
            customTinyMceConfig.valid_elements = '';
            customTinyMceConfig.custom_shortcuts = false;
            return customTinyMceConfig;
        };

    };

})(ip.jQuery);

/**
 * @package ImpressPages
 *
 */


    var IpWidget_Heading = function() {
        "use strict";
        this.$widgetObject = null;
        this.data = null;
        this.$header = null;
        this.$controls = null;
        var ctrlKeyDown = false;

        this.init = function ($widgetObject, data) {
            var thisScope = this;
            this.$widgetObject = $widgetObject;
            this.data = data;
            this.$header = $widgetObject.find('h1,h2,h3,h4,h5,h6');
            this.$controls = $('#ipWidgetHeadingControls');

            this.$header.tinymce(this.tinyMceConfig());

            this.$header.on('focus', $.proxy(this.focus, this));
            if (!data.level) {
                this.data.level = 1;
            }
            this.$widgetObject.on('remove', $.proxy(this.destroy, this));

            if (!this.$header.css('min-height') || this.$header.css('min-height') == '0px') {
                this.$header.css('min-height', this.$header.css('font-size')); //Firefox can't handle focus without min height defined
            }


            this.$header.on('keyup', function(e) {
                if (event.which == 13 && !e.shiftKey==1) {
                    ipContent.createWidget(thisScope.$widgetObject.closest('.ipBlock').data('ipBlock').name, 'Text', thisScope.$widgetObject.index() + 1);
                }
            });

        };

        this.onAdd = function () {
            if (this.$widgetObject.index() != 0) {
                this.data.level = 2;
                this.save(true);
            } else {
                var $headTag = this.$widgetObject.find('h1,h2,h3,h4,h5,h6');
                $headTag.focus();
            }
        };

        this.focus = function () {
            $.proxy(this.initControls, this)();
            this.$widgetObject.find('h1,h2,h3,h4,h5,h6').attr('spellcheck', true);
        };

        this.blur = function(e) {
            var $target = $(e.target);
            var $closestWidget = $target.closest('.ipWidget-Heading');

            if (!$target.hasClass('ipWidget-Heading') && !$closestWidget.hasClass('ipWidget-Heading')) {
                this.removeControls()
            }


//            if (this.$widgetObject[0] && ($.contains(this.$widgetObject[0], e.target) || this.$widgetObject[0] == e.target)) {
//                //mouse click inside the widget
//                return;
//            } else {
//                //mouse click outside of the widget
//                if (this.$controls[0] && ($.contains(this.$controls[0], e.target) || $.contains($('#ipWidgetHeadingOptions')[0], e.target))) {
//                    //widget toolbar click or widget popup click
//                    //do nothing
//                } else {
//                    this.removeControls()
//                }
//            }

        };

        this.removeControls = function() {
            this.$controls.addClass('hidden');
            this.$controls.find('.ipsH').off();
            this.$controls.find('.ipsOptions').off();
            $('body').off('click.ipWidgetHeading');
        };

        this.destroy = function() {
            this.removeControls();
        };

        this.saveOptions = function (data) {
            this.data.anchor = data.anchor;
            this.data.link = data.link;
            this.data.blank = data.blank;
            this.save(false);
        };

        this.openOptions = function () {
            var $modal = $('#ipWidgetHeadingOptions');
            $modal.removeClass('hidden');
            $modal.ipWidgetHeadingModal({
                anchor: this.data.anchor,
                link: this.data.link,
                blank: this.data.blank,
                saveCallback: $.proxy(this.saveOptions, this)
            });
        };

        this.initControls = function () {
            var $controls = this.$controls;
            var $widgetObject = this.$widgetObject;
            this.$controls.find('.ipsH').off();
            this.$controls.find('.ipsOptions').off();

            $controls.removeClass('hidden');
            $controls.css('left', $widgetObject.offsetLeft);
            $controls.css('top', $widgetObject.offsetTop);
            $controls.css('position', 'absolute');
            $controls.css('left', $widgetObject.offset().left);
            $controls.css('top', $widgetObject.offset().top - $controls.height());
            $controls.find('.ipsH').on('click', $.proxy(this.levelPressed, this));

            $controls.find('.ipsH').removeClass('active');
            $controls.find('.ipsH[data-level="' + this.data.level + '"]').addClass('active');
            $controls.find('.ipsOptions').on('click', $.proxy(this.openOptions, this));
            $('body').on('click.ipWidgetHeading', $.proxy(this.blur, this));
        };

        this.levelPressed = function (e) {
            this.removeControls();
            this.data.level = $(e.currentTarget).data('level');
            this.save(true);
        };

        this.save = function (refresh) {
            var saveData = {
                title: this.$widgetObject.find('h1,h2,h3,h4,h5,h6').html(),
                level: this.data.level,
                anchor: this.data.anchor,
                link: this.data.link,
                blank: this.data.blank
            };
            this.$widgetObject.save(saveData, refresh, function($widget){
                if (refresh) {
                    $widget.find('h1,h2,h3,h4,h5,h6').focus();
                }
            });
        };

        this.tinyMceConfig = function () {
            var self = this;
            var customTinyMceConfig = ipTinyMceConfig();
            customTinyMceConfig.menubar = false;
            customTinyMceConfig.toolbar = false;
            customTinyMceConfig.toolbar1 = false;
            customTinyMceConfig.toolbar2 = false;
            customTinyMceConfig.setup = function(ed, l) {
                ed.on('change', function(e){$.proxy(self.save, self)(false)});
                //ed.on('init', function(){$(this).trigger('init.tinymce')});
            };
            customTinyMceConfig.paste_as_text = true;
            customTinyMceConfig.valid_elements = 'br';
            customTinyMceConfig.custom_shortcuts = false;
            return customTinyMceConfig;
        };

    };


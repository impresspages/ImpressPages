/**
 * @package ImpressPages
 *
 *
 */
function IpWidget_IpTitle() {
    "use strict";
    this.$widgetObject = null;
    this.data = null;
    this.$header = null;
    this.$controls = null;
    this.savedRange = null;

    this.init = function ($widgetObject, data) {
        var thisScope = this;
        this.$widgetObject = $widgetObject;
        this.data = data;
        this.$header = $widgetObject.find('h1,h2,h3,h4,h5,h6');
        this.$controls = $('#ipWidgetTitleControls');

        this.$header.tinymce(this.tinyMceConfig());

        this.$header.on('focus', $.proxy(this.focus, this));
        this.$header.on('blur', $.proxy(this.blur, this));
        if (!data.level) {
            this.data.level = 1;
        }
        this.$widgetObject.on('remove', $.proxy(this.destroy, this));

    };



    this.focus = function () {
        this.initControls();
    }

    this.blur = function(e) {
        if ($(e.relatedTarget).hasClass('ipsH')) {
            return;
        }
        this.removeControls();
    };

    this.removeControls = function() {
        this.$controls.addClass('hide');
        this.$controls.find('.ipsH').off();
        this.$controls.find('.ipsOptions').off();
    }

    this.destroy = function() {
        this.removeControls();
    };


    this.initOptions = function () {
        var $self = this.$widgetObject;
        $self.find('.ipsTitleOptionsButton').on('click', function (e) {
            $self.find('.ipsTitleOptions').toggle(getChildern);
            e.preventDefault();
            e.stopPropagation();
            return false;
        });
        $self.find('.ipsAnchor').on('keydown', $.proxy(updateAnchor, this));
        $self.find('.ipsAnchor').on('change', $.proxy(updateAnchor, this));
        $self.find('.ipsAnchor').on('keyup', $.proxy(updateAnchor, this));

    };

    this.updateAnchor = function () {
        var  $preview = this.$widgetObject.find('.ipsAnchorPreview');
        var curText = $preview.text();
        var newText = curText.split('#')[0] + '#' + this.$widgetObject.find('.ipsAnchor').val();
        $preview.text(newText);
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
    };




    this.levelPressed = function (e) {
        this.removeControls();
        this.data.level = $(e.currentTarget).data('level');
        this.save(true);
    };




    this.save = function (refresh, callback) {
        var saveData = {
            title: this.$widgetObject.find('h1,h2,h3,h4,h5,h6').html(),
            level: this.data.level
        };
        this.$widgetObject.save(saveData, refresh, function($widget){
            $widget.find('h1,h2,h3,h4,h5,h6').focus();
        });
    };



    this.tinyMceConfig = function () {
        var $controller = this;
        var customTinyMceConfig = ipTinyMceConfig();
        customTinyMceConfig.menubar = false;
        customTinyMceConfig.toolbar = false;
        customTinyMceConfig.setup = function(ed, l) {
            ed.on('change', function(){$.proxy(save, $controller)(false)});
        };
        customTinyMceConfig.paste_as_text = true;
        customTinyMceConfig.valid_elements = '';
        customTinyMceConfig.custom_shortcuts = false;
        return customTinyMceConfig;
    };

};


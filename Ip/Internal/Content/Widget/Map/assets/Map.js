/**
 * @package ImpressPages
 *
 */
var IpWidget_Map;

(function($){
    "use strict";

    IpWidget_Map = function() {
        var controllerScope = this;
        this.$widgetObject = null;
        this.data = null;

        this.init = function($widgetObject, data) {console.log('init');
            this.$widgetObject = $widgetObject;
            this.data = data;
            var context = this;
            var $map = this.$widgetObject.find('.ipsMap');

            if (jQuery.fn.ipWidgetMap && typeof(google) !== 'undefined' && typeof(google.maps) !== 'undefined' && typeof(google.maps.LatLng) !== 'undefined') {
                jQuery(this.$widgetObject.get()).ipWidgetMap();
            }

//            var $widgetOverlay = $('<div></div>')
//                .css('position', 'absolute')
//                .css('z-index', 5)
//                .width(this.$widgetObject.width())
//                .height(this.$widgetObject.height());
//            this.$widgetObject.prepend($widgetOverlay);
//            $widgetOverlay.on('click', $.proxy(this.focusMap, context));

            var $resizeContainer = $('<div></div>');
            $map.replaceWith($resizeContainer);
            $resizeContainer.append($map);



            $resizeContainer.resizable({
                aspectRatio: false,
                maxWidth: context.$widgetObject.width(),
                resize: function(event, ui) {
                    $map.width(ui.size.width);
                    $map.height(ui.size.height);
                    jQuery(context.$widgetObject.get()).ipWidgetMap('refresh');
                }
            });
//
//            this.$controls = $('#ipWidgetMapMenu');
//            this.$widgetObject.on('click', $.proxy(this.focusMap, this));
//
//            $('body').on('click', $.proxy(function(e) { //detect mouse click outside of the map
//                var $target = $(e.target);
//                var $closestWidget = $target.closest('.ipWidget-Map');
//
//                if (!$target.hasClass('ipWidget-Map') && !$closestWidget.hasClass('ipWidget-Map')) {
//                    $.proxy(this.blurMap, this)();
//                }
//
//
//            }, this));






        };

        this.focusMap = function (e) {
            var context = this;
            e.preventDefault();

            var $item = $(e.currentTarget);
            var $img = $item.find('.ipsMap');
            var $controls = this.$controls;

            $controls.removeClass('hidden');
            $controls.css('position', 'absolute');
            $controls.css('left', $img.offset().left + 5);
            $controls.css('top', $img.offset().top + 5);

            $controls.find('.ipsDelete').off().on('click', function(e) {
                $.proxy(context.deleteMap, context)();
            });
            $controls.find('.ipsEdit').off().on('click', function(e) {
                $.proxy(context.editMap, context)();
            });
            $controls.find('.ipsLink').off().on('click', function(e) {
                $.proxy(linkPopup, context)();
            });
            $controls.find('.ipsSettings').off().on('click', function(e) {
                $.proxy(settingsPopup, context)();
            });
        };

        this.blurMap = function () {
            this.$controls.addClass('hidden');
        };


        this.editMap = function (position) {
            var thisContext = this;
            var $modal = $('#ipWidgetImageEditPopup');
            var options = new Object;
            var data = this.data;

            $modal.modal();

            //init map


            $modal.find('.ipsConfirm').off().on('click', function () {
                var var1 = 'xxx';
                $.proxy(thisContext.updateMap, thisContext)(var1, var1, var1);
                $modal.modal('hide');
            });
        }

        this.updateMap = function (x1, y1, x2, y2, image, callback) {
            var data = {
                method: 'update',
                fileName: image,
                cropX1: x1,
                cropY1: y1,
                cropX2: x2,
                cropY2: y2
            };


            this.$widgetObject.save(data, 1, function($widget){
                $widget.click();
                if (callback) {
                    callback($widget);
                }
            });
        }



        this.resize = function(width, height) {
            var $this = $(this);

            var data = {
                method: 'resize',
                width: width,
                height: height
            };

            if (this.$widgetObject.width() - width <= 2) {
                data = {
                    method: 'autosize'
                }
            }

            this.$widgetObject.save(data, 0);
        }




        var settingsPopup = function () {
            var data = this.data;
            var context = this;
            this.settingsPopup = $('#ipWidgetMapSettingsPopup');
            this.confirmButton = this.settingsPopup.find('.ipsConfirm');
            this.title = this.settingsPopup.find('input[name=title]');
            this.description = this.settingsPopup.find('textarea[name=description]');

            this.title.val(data.title);
            this.description.val(data.description);

            this.settingsPopup.modal(); // open modal popup

            this.confirmButton.off().on('click', $.proxy(saveSettings, context));
        };

        var saveSettings = function () {
            var data = {
                method: 'saveSettings',
                title: this.title.val(),
                description: this.description.val()
            };

            this.$widgetObject.save(data, 1); // save and reload widget
            this.settingsPopup.modal('hide');
        };

    };

})(ip.jQuery);

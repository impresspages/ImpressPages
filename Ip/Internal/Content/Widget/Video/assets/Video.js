/**
 * @package ImpressPages
 *
 */
var IpWidget_Video;

(function($){
    "use strict";

    IpWidget_Video = function() {

        this.widgetObject = null;
        this.confirmButton = null;
        this.popup = null;
        this.data = {};
        this.textarea = null;

        this.init = function (widgetObject, data) {

            this.widgetObject = widgetObject;
            this.data = data;

            var container = this.widgetObject.find('.ipsContainer');

            if (this.data.html) { // TODOXX check if not safe mode #129
                container.html(this.data.html);
            }

            var context = this; // set this so $.proxy would work below
            container.click(function () {
                $.proxy(openPopup, context)();
            });
        };

        this.onAdd = function () {
            $.proxy(openPopup, this)();
        };

        var openPopup = function () {
            this.popup = $('#ipWidgetVideoPopup');
            this.confirmButton = this.popup.find('.ipsConfirm');
            this.url = this.popup.find('input[name=url]');

            if (this.data.url) {
                this.url.val(this.data.url);
            } else {
                this.url.val(''); // cleanup value if it was set before
            }

            this.popup.modal(); // open modal popup

            this.confirmButton.off(); // ensure we will not bind second time
            this.confirmButton.on('click', $.proxy(save, this));
        };

        var save = function () {
            var data = {
                url: this.url.val()
            };

            this.widgetObject.save(data, 1); // save and reload widget
            this.popup.modal('hide');
        };



    };

})(ip.jQuery);

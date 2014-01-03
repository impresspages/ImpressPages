/**
 * @package ImpressPages
 *
 */
var IpWidget_IpText;

(function($){
    "use strict";

    IpWidget_IpText = function() {
        this.$widgetObject = null;

        this.init = function($widgetObject, data) {
            var customTinyMceConfig = ipTinyMceConfig();
            this.$widgetObject = $widgetObject;
            customTinyMceConfig.setup = function(ed, l) {ed.on('change', function(e) {
                $widgetObject.save({text: $widgetObject.find('.ipsContent').html()});
            })};

            $widgetObject.find('.ipsContent').tinymce(customTinyMceConfig);
        };

        this.onAdd = function () {
            this.$widgetObject.find('.ipsContent').focus();
        }
    };

})(ip.jQuery);

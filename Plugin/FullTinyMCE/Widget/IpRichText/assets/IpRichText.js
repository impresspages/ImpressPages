/**
 * @package ImpressPages
 *
 */
var IpWidget_IpRichText;

(function($){
    "use strict";

    IpWidget_IpRichText = function() {
        this.$widgetObject = null;

        this.init = function($widgetObject, data) {
            var customTinyMceConfig = ipTinyMceConfig();
            customTinyMceConfig.plugins = customTinyMceConfig.plugins + " advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table contextmenu paste";
            customTinyMceConfig.toolbar = "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image";
            customTinyMceConfig.menubar = true;

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



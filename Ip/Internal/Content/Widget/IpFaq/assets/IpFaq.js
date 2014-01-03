/**
 * @package ImpressPages
 *
 */
var IpWidget_IpFaq;

(function($){
    "use strict";

    IpWidget_IpFaq = function(widgetObject) {
        this.widgetObject = widgetObject;

        this.manageInit = manageInit;
        this.prepareData = prepareData;

        function manageInit() {
            var instanceData = this.widgetObject.data('ipWidget');
            this.widgetObject.find('textarea').tinymce(ipTinyMceConfig());
        }

        function prepareData() {
            var data = Object();

            data.answer = this.widgetObject.find('.ipAdminTextarea').html();
            data.question = this.widgetObject.find('.ipAdminInput').val();
            $(this.widgetObject).trigger('preparedWidgetData.ipWidget', [ data ]);
        }

    };

})(ip.jQuery);

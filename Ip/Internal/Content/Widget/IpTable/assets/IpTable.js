/**
 * @package ImpressPages
 *
 */
var IpWidget_IpTable;

(function($){
    "use strict";

    IpWidget_IpTable = function(widgetObject) {
        this.widgetObject = widgetObject;

        this.manageInit = manageInit;
        this.prepareData = prepareData;

        function manageInit() {
            var instanceData = this.widgetObject.data('ipWidget');
            this.widgetObject.find('textarea').tinymce(ipTinyMceConfigTable);
        }

        function prepareData() {
            var data = Object();

            data.text = this.widgetObject.find('textarea').html();
            $(this.widgetObject).trigger('preparedWidgetData.ipWidget', [ data ]);
        }

    };

})(ip.jQuery);

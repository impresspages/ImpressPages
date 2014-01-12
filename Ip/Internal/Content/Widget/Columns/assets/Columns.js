/**
 * 
 * Columns Widget Controller
 * 
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */
var IpWidget_Columns;

(function($){
    "use strict";

    IpWidget_Columns = function(widgetObject) {

        this.widgetObject = widgetObject;
        this.manageInit = manageInit;
        this.prepareData = prepareData;

        function manageInit() {
            //get widget data currently stored in the database
            var instanceData = this.widgetObject.data('ipWidget').data;

            //if widget has been already initialized
            if (instanceData.baseId) {
                //set input value
                this.widgetObject.find('input[name="baseId"]').val(instanceData.baseId);
            } else {
                //leave input empty
            }
        }

        function prepareData() {
            //create simple data object. It will be returned as the data to be stored.
            var data = {};
            data.baseId = this.widgetObject.find('input[name="baseId"]').val();
            data.columns = this.widgetObject.find('input[name="columns"]').val();
            $(this.widgetObject).trigger('preparedWidgetData.ipWidget', [ data ]);
        }

    }

})(ip.jQuery);

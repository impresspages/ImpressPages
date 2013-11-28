/**
 * @package ImpressPages
 *
 *
 */

function IpWidget_IpText(widgetObject) {
    "use strict";
    this.widgetObject = widgetObject;

    this.manageInit = manageInit;
    this.prepareData = prepareData;


    function manageInit() {
        var instanceData = this.widgetObject.data('ipWidget');
        //this.widgetObject.find('textarea').tinymce(ipTinyMceConfigMin);

        //this.widgetObject.find('h1.ipwTitle').attr('contenteditable', 'true');
        this.widgetObject.find('.ipsContent').attr('contenteditable', 'true');


            //$('.ipWidget-IpText .ipsContent').tinymce(ipTinyMceConfigMin);

    }

    function prepareData() {

        var data = Object();

        data.text = this.widgetObject.find('textarea').html();
        $(this.widgetObject).trigger('preparedWidgetData.ipWidget', [ data ]);
    }

    

};



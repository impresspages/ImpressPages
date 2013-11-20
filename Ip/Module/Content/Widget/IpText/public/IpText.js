/**
 * @package ImpressPages
 *
 *
 */

function IpWidget_IpText(widgetObject) {
    this.widgetObject = widgetObject;

    this.manageInit = manageInit;
    this.prepareData = prepareData;


    function manageInit() {
        var instanceData = this.widgetObject.data('ipWidget');
        alert(this.widgetObject.find('textarea'));
        this.widgetObject.find('textarea').tinymce(ipTinyMceConfigMin);
    }

    function prepareData() {

        var data = Object();

        data.text = this.widgetObject.find('textarea').html();
        $(this.widgetObject).trigger('preparedWidgetData.ipWidget', [ data ]);
    }

    

};

      

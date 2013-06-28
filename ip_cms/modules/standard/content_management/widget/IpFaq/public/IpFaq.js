/**
 * @package ImpressPages
 *
 *
 */

function IpWidget_IpFaq(widgetObject) {
    this.widgetObject = widgetObject;

    this.manageInit = manageInit;
    this.prepareData = prepareData;


    function manageInit() {
        var instanceData = this.widgetObject.data('ipWidget');
        this.widgetObject.find('textarea').tinymce(ipTinyMceConfigMin);
    }

    function prepareData() {

        var data = Object();

        data.answer = this.widgetObject.find('.ipAdminTextarea').html();
        data.question = this.widgetObject.find('.ipAdminInput').val();
        $(this.widgetObject).trigger('preparedWidgetData.ipWidget', [ data ]);
    }



};


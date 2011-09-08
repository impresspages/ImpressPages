/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

function ipWidget_ipPicture(widgetObject) {
    this.widgetObject = widgetObject;

    this.prepareData = prepareData;
    this.manageInit = manageInit;
    this.uploadPicture = uploadPicture;

    function manageInit() {
        var instanceData = this.widgetObject.data('ipWidget');
        
        var options = new Object;
//        var options = {
//            crop: true,
//            aspectRatio: 500/60
//        };
        console.log('bbbbbbbb');
        this.widgetObject.find('.ipWidget_ipPicture_uploadPicture').ipUploadPicture(options);
        
        
    }

    function prepareData() {
        console.log('saving');

        var data = Object();

        //data.text = $(this.widgetObject).find('textarea').first().val();
        console.log(this.widgetObject);
        $(this.widgetObject).trigger('preparedWidgetData.ipWidget', [ data ]);
    }

    function fileUploaded(event) {

    }
    
    function uploadPicture() {
        
    }
    
    

};


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
        console.log(instanceData.data);
        var options = new Object;
        var options = {
            curPicture : instanceData.data.picture,
            crop : true,
            aspectRatio: 500/60
        };
        this.widgetObject.find('.ipWidget_ipPicture_uploadPicture').ipUploadPicture(options);
        
        
    }

    function prepareData() {

        var data = Object();

        var picture = this.widgetObject.find('.ipWidget_ipPicture_uploadPicture').ipUploadPicture('getCurPicture');
        
        data.picture = picture;
        console.log(picture);
        
        //data.text = $(this.widgetObject).find('textarea').first().val();
        $(this.widgetObject).trigger('preparedWidgetData.ipWidget', [ data ]);
    }

    function fileUploaded(event) {

    }
    
    function uploadPicture() {
        
    }
    
    

};


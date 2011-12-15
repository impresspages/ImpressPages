/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

function IpWidget_IpPicture(widgetObject) {
    this.widgetObject = widgetObject;

    this.prepareData = prepareData;
    this.manageInit = manageInit;


    function manageInit() {
        var instanceData = this.widgetObject.data('ipWidget');
        var options = new Object;
        
        if (instanceData.data.pictureOriginal) {
            options.picture = instanceData.data.pictureOriginal;
        }
        if (instanceData.data.cropX1) {
            options.cropX1 = instanceData.data.cropX1;
        }
        if (instanceData.data.cropY1) {
            options.cropY1 = instanceData.data.cropY1;
        }
        if (instanceData.data.cropX2) {
            options.cropX2 = instanceData.data.cropX2;
        }
        if (instanceData.data.cropY2) {
            options.cropY2 = instanceData.data.cropY2;
        }
        if (instanceData.data.pictureWindowWidth) {
            options.windowWidth = instanceData.data.pictureWindowWidth;
        }
        options.enableChangeHeight = true;
        options.enableChangeWidth = true;

        this.widgetObject.find('.ipaImage').ipUploadPicture(options);
        
        
    }

    function prepareData() {
        var data = Object();
        var ipUploadPicture = this.widgetObject.find('.ipaImage');
        if (ipUploadPicture.ipUploadPicture('getNewPictureUploaded')) {
            var newPicture = ipUploadPicture.ipUploadPicture('getCurPicture');
            if (newPicture) {
                data.newPicture = newPicture;
            }
        }
        
        if (ipUploadPicture.ipUploadPicture('getCropCoordinatesChanged') && ipUploadPicture.ipUploadPicture('getCurPicture') != false) {
            var cropCoordinates = ipUploadPicture.ipUploadPicture('getCropCoordinates');
            if (cropCoordinates) {
                data.cropX1 = cropCoordinates.x1;
                data.cropY1 = cropCoordinates.y1;
                data.cropX2 = cropCoordinates.x2;
                data.cropY2 = cropCoordinates.y2;
            }
        }
        
        var windowWidth = ipUploadPicture.ipUploadPicture('getWindowWidth');
        var maxWidth = this.widgetObject.find('.ipaImage').width();
        data.scale = windowWidth / maxWidth;
        data.pictureWindowWidth = windowWidth;
        data.title = this.widgetObject.find('.ipaImageTitle').val();
        
        $(this.widgetObject).trigger('preparedWidgetData.ipWidget', [ data ]);        
    }



    

};


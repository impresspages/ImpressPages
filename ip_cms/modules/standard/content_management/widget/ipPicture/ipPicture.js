/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

function ipWidget_IpPicture(widgetObject) {
    this.widgetObject = widgetObject;

    this.prepareData = prepareData;
    this.manageInit = manageInit;


    function manageInit() {
        var instanceData = this.widgetObject.data('ipWidget');
        var options = new Object;
        var options = {
            picture : instanceData.data.pictureOriginal,
            pictureWidth : instanceData.data.scaleWidth,
            pictureHeight : instanceData.data.scaleHeight,
            pictureCenterX : instanceData.data.centerX,
            pictureCenterY : instanceData.data.centerY,
            
            
            aspectRatio: 1/1
        };
        this.widgetObject.find('.ipWidget_ipPicture_uploadPicture').ipUploadPicture(options);
        
        
    }

    function prepareData() {
        var data = Object();
        var ipUploadPicture = this.widgetObject.find('.ipWidget_ipPicture_uploadPicture');
        if (ipUploadPicture.ipUploadPicture('getNewPictureUploaded')) {
            var newPicture = ipUploadPicture.ipUploadPicture('getCurPicture');
            if (newPicture) {
                data.newPicture = newPicture;
            }
        }
        
        if (ipUploadPicture.ipUploadPicture('getCropCoordinatesChanged')) {
            var cropCoordinates = ipUploadPicture.ipUploadPicture('getCropCoordinates');
            if (cropCoordinates) {
                data.cropX1 = cropCoordinates.x1;
                data.cropY1 = cropCoordinates.y1;
                data.cropX2 = cropCoordinates.x2;
                data.cropY2 = cropCoordinates.y2;
            }
        }
        
        data.title = this.widgetObject.find('.ipWidget_ipPicture_title').val();
        
        $(this.widgetObject).trigger('preparedWidgetData.ipWidget', [ data ]);        
    }



    

};


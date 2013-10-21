/**
 * @package ImpressPages
 *
 *
 */

function IpWidget_IpImage(widgetObject, contentBody) {
    this.widgetObject = widgetObject;
    this.contentBody = contentBody;

    this.prepareData = prepareData;
    this.manageInit = manageInit;

    this.addError = addError;


    function manageInit() {
        var instanceData = this.widgetObject.data('ipWidget');
        var options = new Object;
        
        if (instanceData.data.imageOriginal) {
            options.image = instanceData.data.imageOriginal;
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
        if (instanceData.data.imageWindowWidth) {
            options.windowWidth = instanceData.data.imageWindowWidth;
        }
        options.maxWindowWidth = this.contentBody.width();
        options.enableChangeHeight = true;
        options.enableChangeWidth = true;
        options.enableUnderscale = true;

        var $imageUploader = this.widgetObject.find('.ipaImage');
        $imageUploader.ipUploadImage(options);
        this.widgetObject.bind('error.ipUploadImage', {widgetController: this}, this.addError);

    }
    

    function addError(event, errorMessage) {
        $(this).trigger('error.ipContentManagement', errorMessage);
    }
    
    function removeError () {
        this.widgetObject.find('.ipaErrorContainer .ipaError').remove();
    }

    function prepareData() {
        var data = Object();
        var ipUploadImage = this.widgetObject.find('.ipaImage');
        if (ipUploadImage.ipUploadImage('getNewImageUploaded')) {
            var newImage = ipUploadImage.ipUploadImage('getCurImage');
            if (newImage) {
                data.newImage = newImage;
            }
        }
        
        if (ipUploadImage.ipUploadImage('getCropCoordinatesChanged') && ipUploadImage.ipUploadImage('getCurImage') != false) {
            var cropCoordinates = ipUploadImage.ipUploadImage('getCropCoordinates');
            if (cropCoordinates) {
                data.cropX1 = cropCoordinates.x1;
                data.cropY1 = cropCoordinates.y1;
                data.cropX2 = cropCoordinates.x2;
                data.cropY2 = cropCoordinates.y2;
            }
        }
        
        var windowWidth = ipUploadImage.ipUploadImage('getWindowWidth');
        var maxWidth = this.contentBody.width();
        data.maxWidth = this.widgetObject.width();
        data.scale = windowWidth / maxWidth;
        data.imageWindowWidth = windowWidth;
        data.title = this.widgetObject.find('.ipaImageTitle').val();
        
        $(this.widgetObject).trigger('preparedWidgetData.ipWidget', [ data ]);        
    }



    

};


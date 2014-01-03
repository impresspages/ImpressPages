/**
 * @package ImpressPages
 *
 */
var IpWidget_IpImage;

(function($){
    "use strict";

    IpWidget_IpImage = function() {
        this.$widgetObject = null;
        this.$imageUploader = null;

        this.init = function($widgetObject, data) {
            this.$widgetObject = $widgetObject;

            var $imageUploader = $('<div class="ipsImage ip"></div>');
            this.$widgetObject.append($imageUploader);
            this.$imageUploader = $imageUploader;

            var options = new Object;

            if (data.imageOriginal) {
                options.image = data.imageOriginal;
            }
            if (data.cropX1) {
                options.cropX1 = data.cropX1;
            }
            if (data.cropY1) {
                options.cropY1 = data.cropY1;
            }
            if (data.cropX2) {
                options.cropX2 = data.cropX2;
            }
            if (data.cropY2) {
                options.cropY2 = data.cropY2;
            }
            options.enableChangeHeight = true;
            options.enableChangeWidth = true;
            options.enableUnderscale = true;

            var $img = this.$widgetObject.find('img');


            if ($img.length == 1) {
                options.windowWidth = $img.width();
                options.windowHeight = $img.height();
                $img.hide();
            }
            if (options.windowHeight == null) {
                options.windowHeight = 100;
            }

            this.$imageUploader.ipUploadImage(options);
            this.$imageUploader.on('error.ipUploadImage', $.proxy(addError, this));
            this.$imageUploader.on('change.ipUploadImage', $.proxy(save, this));

        }

        var addError = function (event, errorMessage) {
            $(this).trigger('error.ipContentManagement', errorMessage);
        }

        var save = function() {

            var data = Object();
            var ipUploadImage = this.$imageUploader;
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
                    data.width = ipUploadImage.ipUploadImage('width');
                    data.height = ipUploadImage.ipUploadImage('height');
                }
            }

            data.title = this.$widgetObject.find('.ipaImageTitle').val();
            this.$widgetObject.save(data);
        }

    };

})(ip.jQuery);

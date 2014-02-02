/**
 * @package ImpressPages
 *
 */
var IpWidget_Image;

(function($){
    "use strict";

    IpWidget_Image = function() {
        var controllerScope = this;
        this.$widgetObject = null;
        this.$imageUploader = null;

        this.init = function($widgetObject, data) {
            this.$widgetObject = $widgetObject;

            this.$widgetObject.find('.ipsImage').on('click', function() {
                var $this = $(this);
                $this.resizable({
                    aspectRatio: true,
                    maxWidth: controllerScope.$widgetObject.width(),
                    resize: function(event, ui) {
                        controllerScope.resize(Math.round(ui.size.width), Math.round(ui.size.height));
                    }
                });
            });
//
//            var $imageUploader = $('<div class="ipsImage ip"></div>');
//            this.$widgetObject.append($imageUploader);
//            this.$imageUploader = $imageUploader;
//
//            var options = new Object;
//
//            if (data.imageOriginal) {
//                options.image = data.imageOriginal;
//            }
//            if (data.cropX1) {
//                options.cropX1 = data.cropX1;
//            }
//            if (data.cropY1) {
//                options.cropY1 = data.cropY1;
//            }
//            if (data.cropX2) {
//                options.cropX2 = data.cropX2;
//            }
//            if (data.cropY2) {
//                options.cropY2 = data.cropY2;
//            }
//            options.enableChangeHeight = true;
//            options.enableChangeWidth = true;
//            options.enableUnderscale = true;
//
//            var $img = this.$widgetObject.find('img');
//
//
//            if ($img.length == 1) {
//                options.windowWidth = $img.width();
//                options.windowHeight = $img.height();
//                $img.hide();
//            }
//            if (options.windowHeight == null) {
//                options.windowHeight = 100;
//            }
//
//            this.$imageUploader.ipUploadImage(options);

        }


        this.onAdd = function (e) {
            var thisContext = this;
            var repository = new ipRepository({preview: 'thumbnails', filter: 'image'});
            repository.on('ipRepository.filesSelected', $.proxy(thisContext.filesSelected, thisContext));
            repository.on('ipModuleRepository.cancel', function () {
                ipContent.deleteWidget(thisContext.$widgetObject.data('widgetinstanceid'));
            });

        }


        this.filesSelected = function(event, files) {
            var $this = $(this);

            var data = {
                method: 'newImage'
            };
            $.each(files, function(key, value) {
                data.newImage = value.fileName;
            });

            this.$widgetObject.save(data, 1);
        }

        this.resize = function(width, height) {
            var $this = $(this);

            var data = {
                method: 'resize',
                width: width,
                height: height
            };
            this.$widgetObject.save(data, 0);
        }

//        var updateImage = function(newImage) {
//            var $this = $(this);
//
//            var data = this.data;
//            var data = {
//                method: 'add'
//            };
//            this.$widgetObject.save(data, 1, function($widget){
//                $widget.click();
//            });
//        }
//
//        var save = function() {
//
//            var data = Object();
//            var ipUploadImage = this.$imageUploader;
//            if (ipUploadImage.ipUploadImage('getNewImageUploaded')) {
//                var newImage = ipUploadImage.ipUploadImage('getCurImage');
//                if (newImage) {
//                    data.newImage = newImage;
//                }
//            }
//
//            if (ipUploadImage.ipUploadImage('getCropCoordinatesChanged') && ipUploadImage.ipUploadImage('getCurImage') != false) {
//                var cropCoordinates = ipUploadImage.ipUploadImage('getCropCoordinates');
//                if (cropCoordinates) {
//                    data.cropX1 = cropCoordinates.x1;
//                    data.cropY1 = cropCoordinates.y1;
//                    data.cropX2 = cropCoordinates.x2;
//                    data.cropY2 = cropCoordinates.y2;
//                    data.width = ipUploadImage.ipUploadImage('width');
//                    data.height = ipUploadImage.ipUploadImage('height');
//                }
//            }
//
//            data.title = this.$widgetObject.find('.ipsImageTitle').val();
//            this.$widgetObject.save(data);
//        }

    };

})(ip.jQuery);

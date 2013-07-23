/**
 * @package ImpressPages
 *
 *
 */

function IpWidget_IpImageGallery(widgetObject) {
    this.widgetObject = widgetObject;

    this.prepareData = prepareData;
    this.manageInit = manageInit;
    this.fileUploaded = fileUploaded;
    
    this.addError = addError;


    function manageInit() {
        var instanceData = this.widgetObject.data('ipWidget');

        this.widgetObject.find('.ipmBrowseButton').click(function(e){
            e.preventDefault();
            var repository = new ipRepository({preview: 'thumbnails', filter: 'image'});
            repository.bind('ipRepository.filesSelected', $.proxy(fileUploaded, widgetObject));
        });

        
        var container = this.widgetObject.find('.ipWidget_ipImageGallery_container');
        var options = new Object;
        if (instanceData.data.images) {
            options.images = instanceData.data.images;
        } else {
            options.images = new Array();
        }
        options.smallImageWidth = this.widgetObject.find('input[name="smallImageWidth"]').val();
        options.smallImageHeight = this.widgetObject.find('input[name="smallImageHeight"]').val();
        options.imageTemplate = this.widgetObject.find('.ipaImageTemplate');
        container.ipWidget_ipImageGallery_container(options);
        
        
        this.widgetObject.bind('fileUploaded.ipUploadFile', this.fileUploaded);
        this.widgetObject.bind('error.ipUploadImage', {widgetController: this}, this.addError);
        this.widgetObject.bind('error.ipUploadFile', {widgetController: this}, this.addError);
        
    }

    


    function addError(event, errorMessage) {
        $(this).trigger('error.ipContentManagement', [errorMessage]);
    }


    function fileUploaded(event, files) {
        var $this = $(this);

        var container = $this.find('.ipWidget_ipImageGallery_container');
        for(var index in files) {
            container.ipWidget_ipImageGallery_container('addImage', files[index].file, '', 'new');
        }
    }


    
    function prepareData() {
        var data = Object();
        var container = this.widgetObject.find('.ipWidget_ipImageGallery_container');
        
        data.images = new Array();
        $images = container.ipWidget_ipImageGallery_container('getImages');
        $images.each(function(index) {
            var $this = $(this);
            var tmpImage = new Object();
            tmpImage.title = $this.ipWidget_ipImageGallery_image('getTitle');
            tmpImage.fileName = $this.ipWidget_ipImageGallery_image('getFileName');
            tmpImage.status = $this.ipWidget_ipImageGallery_image('getStatus');
            var tmpCropCoordinates = $this.ipWidget_ipImageGallery_image('getCropCoordinates');
            tmpImage.cropX1 = tmpCropCoordinates.x1; 
            tmpImage.cropY1 = tmpCropCoordinates.y1; 
            tmpImage.cropX2 = tmpCropCoordinates.x2; 
            tmpImage.cropY2 = tmpCropCoordinates.y2; 
            
            
            data.images.push(tmpImage);

        });


        $(this.widgetObject).trigger('preparedWidgetData.ipWidget', [ data ]);
    }


};




(function($) {

    var methods = {
            
    init : function(options) {

        return this.each(function() {

            var $this = $(this);

            var data = $this.data('ipWidget_ipImageGallery_container');

            // If the plugin hasn't been initialized yet
            var images = null;
            if (options.images) {
                images = options.images;
            } else {
                images = new Array();
            }
            
            if (!data) {
                $this.data('ipWidget_ipImageGallery_container', {
                    images : images,
                    imageTemplate : options.imageTemplate,
                    smallImageWidth : options.smallImageWidth,
                    smallImageHeight : options.smallImageHeight
                });
                
                for (var i in images) {
                    var coordinates = new Object();
                    coordinates.cropX1 = images[i]['cropX1'];
                    coordinates.cropY1 = images[i]['cropY1'];
                    coordinates.cropX2 = images[i]['cropX2'];
                    coordinates.cropY2 = images[i]['cropY2'];
                    $this.ipWidget_ipImageGallery_container('addImage', images[i]['imageOriginal'], images[i]['title'], 'present', coordinates); 
                }
                $this.bind('removeImage.ipWidget_ipImageGallery', function(event, imageObject) {
                    var $imageObject = $(imageObject);
                    $imageObject.ipWidget_ipImageGallery_container('removeImage', $imageObject);
                });
                
                $( ".ipWidget_ipImageGallery_container" ).sortable();
                $( ".ipWidget_ipImageGallery_container" ).sortable('option', 'handle', '.ipaImageMove');

            }
        });
    },
    
    addImage : function (fileName, title, status, coordinates) {
        var $this = this;
        var data = $this.data('ipWidget_ipImageGallery_container');
        var $newImageRecord = $this.data('ipWidget_ipImageGallery_container').imageTemplate.clone();
        $newImageRecord.ipWidget_ipImageGallery_image({'smallImageWidth' : data.smallImageWidth, 'smallImageHeight' : data.smallImageHeight, 'status' : status, 'fileName' : fileName, 'title' : title, 'coordinates' : coordinates});
        var $uploader = $this.find('.ipmBrowseButton');
        if ($uploader.length > 0) {
            $($uploader).before($newImageRecord);
        } else {
            $this.append($newImageRecord);
        }
    },
    
    removeImage : function ($imageObject) {
        $imageObject.hide();
        $imageObject.ipWidget_ipImageGallery_image('setStatus', 'deleted');
        
    },
    
    getImages : function () {
        var $this = this;
        return $this.find('.ipaImageTemplate');
    }



    };

    $.fn.ipWidget_ipImageGallery_container = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipAdminWidgetButton');
        }

    };

})(jQuery);





(function($) {

    var methods = {
    init : function(options) {

        return this.each(function() {

            var $this = $(this);

            var data = $this.data('ipWidget_ipImageGallery_image');

            var status = 'new';
            if (options.status) {
                status = options.status;
            }
            
            // If the plugin hasn't been initialized yet
            if (!data) {
                var data = {
                    title : '',
                    fileName : '',
                    status : status,
                    smallImageWidth : options.smallImageWidth,
                    smallImageHeight : options.smallImageHeight
                };
                
                if (options.title) {
                    data.title = options.title;
                }
                if (options.fileName) {
                    data.fileName = options.fileName;
                }
                if (options.status) {
                    data.status = options.status;
                }
                
                $this.data('ipWidget_ipImageGallery_image', {
                    title : data.title,
                    fileName : data.fileName,
                    status : data.status
                });
                $this.find('.ipaImageTitle').val(data.title);
            }
            
            
            
            //$this.find('.ipaImage').attr('src', ip.baseUrl + data.fileName);
            var imageOptions = new Object;
            imageOptions.image = data.fileName;
            if (options.coordinates) {
                imageOptions.cropX1 = options.coordinates.cropX1;
                imageOptions.cropY1 = options.coordinates.cropY1;
                imageOptions.cropX2 = options.coordinates.cropX2;
                imageOptions.cropY2 = options.coordinates.cropY2;
            }
            imageOptions.windowWidth = options.smallImageWidth;
            imageOptions.windowHeight = options.smallImageHeight;
            imageOptions.enableChangeWidth = false;
            imageOptions.enableChangeHeight = false;

            $this.find('.ipaImage').ipUploadImage(imageOptions);
            
            $this.find('.ipaImageRemove').bind('click', 
                function(event){
                    $this = $(this);
                    $this.trigger('removeClick.ipWidget_ipImageGallery');
                    return false;
                }
            );
            $this.bind('removeClick.ipWidget_ipImageGallery', function(event) {
                $this.trigger('removeImage.ipWidget_ipImageGallery', this);
            });
            return $this;
        });
    },
    
    getTitle : function() {
        var $this = this;
        return $this.find('.ipaImageTitle').val();
    },
    
    getFileName : function() {
        var $this = this;
        var curImage = $this.find('.ipaImage').ipUploadImage('getCurImage');
        return curImage;
    },
    
    getCropCoordinates : function() {
        var $this = this;
        var ipUploadImage = $this.find('.ipaImage');
        var cropCoordinates = ipUploadImage.ipUploadImage('getCropCoordinates');
        return cropCoordinates;
    },
        
    getStatus : function() {
        var $this = this;
        
        var tmpData = $this.data('ipWidget_ipImageGallery_image');
        if (tmpData.status == 'deleted') {
            return tmpData.status;
        }
        
        var ipUploadImage = $this.find('.ipaImage');
        if (tmpData.status == 'new' || ipUploadImage.ipUploadImage('getNewImageUploaded')) {
            return 'new';
        } else {
            if (ipUploadImage.ipUploadImage('getCropCoordinatesChanged') && ipUploadImage.ipUploadImage('getCurImage') != false) {
                return 'coordinatesChanged';
            }
        }
        
        var tmpData = $this.data('ipWidget_ipImageGallery_image');
        //status, set on creation. Usually 'new' or 'present'
        return tmpData.status;
    },
    
    setStatus : function(newStatus) {
        var $this = $(this);
        var tmpData = $this.data('ipWidget_ipImageGallery_image');
        tmpData.status = newStatus;
        $this.data('ipWidget_ipImageGallery_image', tmpData);
    }
    



    };

    $.fn.ipWidget_ipImageGallery_image = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipAdminWidgetButton');
        }

    };

})(jQuery);


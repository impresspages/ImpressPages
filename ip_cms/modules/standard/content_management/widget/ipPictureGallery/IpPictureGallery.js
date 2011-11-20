/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

function ipWidget_IpPictureGallery(widgetObject) {
    this.widgetObject = widgetObject;

    this.prepareData = prepareData;
    this.manageInit = manageInit;
    this.fileUploaded = fileUploaded;


    function manageInit() {
        var instanceData = this.widgetObject.data('ipWidget');
        
        var uploader = this.widgetObject.find('.ipWidget_ipPictureGallery_uploadFile');
        var options = new Object;
        uploader.ipUploadFile(options);
        
        var container = this.widgetObject.find('.ipWidget_ipPictureGallery_container');
        var options = new Object;
        if (instanceData.data.pictures) {
            options.pictures = instanceData.data.pictures;
        } else {
            options.pictures = new Array();
        }
        options.pictureTemplate = this.widgetObject.find('.ipWidget_ipPictureGallery_pictureTemplate');
        container.ipWidget_ipPictureGallery_container(options);
        
        
        this.widgetObject.bind('fileUploaded.ipUploadFile', this.fileUploaded);
        
        
    }

    
    function fileUploaded(event, fileName) {
        /* we are in widgetObject context */
        var $this = $(this);
        var container = $this.find('.ipWidget_ipPictureGallery_container');
        container.ipWidget_ipPictureGallery_container('addPicture', fileName, '', 'new');
    }
    

    
    function prepareData() {
        var data = Object();
        var container = this.widgetObject.find('.ipWidget_ipPictureGallery_container');
        
        data.pictures = new Array();
        $pictures = container.ipWidget_ipPictureGallery_container('getPictures');
        $pictures.each(function(index) {
            var $this = $(this);
            var tmpPicture = new Object();
            tmpPicture.title = $this.ipWidget_ipPictureGallery_picture('getTitle');
            tmpPicture.fileName = $this.ipWidget_ipPictureGallery_picture('getFileName');
            tmpPicture.status = $this.ipWidget_ipPictureGallery_picture('getStatus');
            var tmpCropCoordinates = $this.ipWidget_ipPictureGallery_picture('getCropCoordinates');
            tmpPicture.cropX1 = tmpCropCoordinates.x1; 
            tmpPicture.cropY1 = tmpCropCoordinates.y1; 
            tmpPicture.cropX2 = tmpCropCoordinates.x2; 
            tmpPicture.cropY2 = tmpCropCoordinates.y2; 
            
            
            data.pictures.push(tmpPicture);

        });


        $(this.widgetObject).trigger('preparedWidgetData.ipWidget', [ data ]);
    }


};




(function($) {

    var methods = {
            
    init : function(options) {

        return this.each(function() {

            var $this = $(this);

            var data = $this.data('ipWidget_ipPictureGallery_container');

            // If the plugin hasn't been initialized yet
            var pictures = null;
            if (options.pictures) {
                pictures = options.pictures;
            } else {
                pictures = new Array();
            }
            
            if (!data) {
                $this.data('ipWidget_ipPictureGallery_container', {
                    pictures : pictures,
                    pictureTemplate : options.pictureTemplate
                });
                
                for (var i in pictures) {
                    $this.ipWidget_ipPictureGallery_container('addPicture', pictures[i]['fileName'], pictures[i]['title'], 'present'); 
                }
                $this.bind('removePicture.ipWidget_ipPictureGallery', function(event, pictureObject) {
                    var $pictureObject = $(pictureObject);
                    $pictureObject.ipWidget_ipPictureGallery_container('removePicture', $pictureObject);
                });
                
                $( ".ipWidget_ipPictureGallery_container" ).sortable();
                $( ".ipWidget_ipPictureGallery_container" ).sortable('option', 'handle', '.ipWidget_ipPictureGallery_pictureMoveHandle');
                $( ".ipWidget_ipPictureGallery_container" ).disableSelection();

            }
        });
    },
    
    addPicture : function (fileName, title, status) {
        var $this = this;
        var $newPictureRecord = $this.data('ipWidget_ipPictureGallery_container').pictureTemplate.clone();
        $newPictureRecord.ipWidget_ipPictureGallery_picture({'status' : status, 'fileName' : fileName, 'title' : title});
        $this.append($newPictureRecord);
        
    },
    
    removePicture : function ($pictureObject) {
        $pictureObject.hide();
        $pictureObject.ipWidget_ipPictureGallery_picture('setStatus', 'deleted');
        
    },
    
    getPictures : function () {
        var $this = this;
        return $this.find('.ipWidget_ipPictureGallery_pictureTemplate');
    }



    };

    $.fn.ipWidget_ipPictureGallery_container = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipWidgetButton');
        }

    };

})(jQuery);





(function($) {

    var methods = {
    init : function(options) {

        return this.each(function() {

            var $this = $(this);

            var data = $this.data('ipWidget_ipPictureGallery_picture');

            
            // If the plugin hasn't been initialized yet
            if (!data) {
                var data = {
                    title : '',
                    fileName : '',
                    status : 'new'
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
                
                $this.data('ipWidget_ipPictureGallery_picture', {
                    title : data.title,
                    fileName : data.fileName,
                    status : data.status
                });
                $this.find('.ipWidget_ipPictureGallery_pictureTitle').val(data.title);
            }
            
            
            
            //$this.find('.ipWidget_ipPictureGallery_picturePreview').attr('src', ip.baseUrl + data.fileName);
            var pictureOptions = new Object;
            pictureOptions.picture = data.fileName;
//            if (instanceData.data.cropX1) {
//                options.cropX1 = instanceData.data.cropX1;
//            }
//            if (instanceData.data.cropY1) {
//                options.cropY1 = instanceData.data.cropY1;
//            }
//            if (instanceData.data.cropX2) {
//                options.cropX2 = instanceData.data.cropX2;
//            }
//            if (instanceData.data.cropY2) {
//                options.cropY2 = instanceData.data.cropY2;
//            }
            pictureOptions.windowWidth = 200;
            pictureOptions.windowHeight = 100;
            pictureOptions.changeWidth = false;
            pictureOptions.changeHeight = false;

            $this.find('.ipWidget_ipPictureGallery_picturePreview').ipUploadPicture(pictureOptions);
            
//          handle uploading of new photo
//          if (ipUploadPicture.ipUploadPicture('getNewPictureUploaded')) {
//              var newPicture = ipUploadPicture.ipUploadPicture('getCurPicture');
//              if (newPicture) {
//                  data.newPicture = newPicture;
//              }
//          }
            
            
            
            
            $this.find('.ipWidget_ipPictureGallery_pictureRemove').bind('click', 
                function(event){
                    $this = $(this);
                    $this.trigger('removeClick.ipWidget_ipPictureGallery');
                }
            );
            $this.bind('removeClick.ipWidget_ipPictureGallery', function(event) {
                $this.trigger('removePicture.ipWidget_ipPictureGallery', this);
            });
            return $this;
        });
    },
    
    getTitle : function() {
        var $this = this;
        return $this.find('.ipWidget_ipPictureGallery_pictureTitle').val();
    },
    
    getFileName : function() {
        var $this = this;
        var tmpData = $this.data('ipWidget_ipPictureGallery_picture');
        return tmpData.fileName;
    },
    
    getCropCoordinates : function() {
        var ipUploadPicture = this.widgetObject.find('.ipWidget_ipPictureGallery_picturePreview');
        var cropCoordinates = ipUploadPicture.ipUploadPicture('getCropCoordinates');
        return cropCoordinates;
    },
        
    getStatus : function() {
        var $this = this;
        var tmpData = $this.data('ipWidget_ipPictureGallery_picture');
        return tmpData.status;
    },
    
    setStatus : function(newStatus) {
        console.log(this);
        var $this = $(this);
        var tmpData = $this.data('ipWidget_ipPictureGallery_picture');
        tmpData.status = newStatus;
        $this.data('ipWidget_ipPictureGallery_picture', tmpData);
        
    }
    



    };

    $.fn.ipWidget_ipPictureGallery_picture = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipWidgetButton');
        }

    };

})(jQuery);


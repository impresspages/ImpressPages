/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

function IpWidget_IpPictureGallery(widgetObject) {
    this.widgetObject = widgetObject;

    this.prepareData = prepareData;
    this.manageInit = manageInit;
    this.fileUploaded = fileUploaded;


    function manageInit() {
        var instanceData = this.widgetObject.data('ipWidget');
        
        var uploader = this.widgetObject.find('.ipaUpload');
        var options = new Object;
        uploader.ipUploadFile(options);
        
        var container = this.widgetObject.find('.ipWidget_ipPictureGallery_container');
        var options = new Object;
        if (instanceData.data.pictures) {
            options.pictures = instanceData.data.pictures;
        } else {
            options.pictures = new Array();
        }
        options.smallPictureWidth = this.widgetObject.find('input[name="smallPictureWidth"]').val();
        options.smallPictureHeight = this.widgetObject.find('input[name="smallPictureHeight"]').val();
        uploader.css('width',options.smallPictureWidth);
        uploader.css('height',options.smallPictureHeight);
        options.pictureTemplate = this.widgetObject.find('.ipaImageTemplate');
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
                    pictureTemplate : options.pictureTemplate,
                    smallPictureWidth : options.smallPictureWidth,
                    smallPictureHeight : options.smallPictureHeight
                });
                
                for (var i in pictures) {
                    var coordinates = new Object();
                    coordinates.cropX1 = pictures[i]['cropX1'];
                    coordinates.cropY1 = pictures[i]['cropY1'];
                    coordinates.cropX2 = pictures[i]['cropX2'];
                    coordinates.cropY2 = pictures[i]['cropY2'];
                    $this.ipWidget_ipPictureGallery_container('addPicture', pictures[i]['pictureOriginal'], pictures[i]['title'], 'present', coordinates); 
                }
                $this.bind('removePicture.ipWidget_ipPictureGallery', function(event, pictureObject) {
                    var $pictureObject = $(pictureObject);
                    $pictureObject.ipWidget_ipPictureGallery_container('removePicture', $pictureObject);
                });
                
                $( ".ipWidget_ipPictureGallery_container" ).sortable();
                $( ".ipWidget_ipPictureGallery_container" ).sortable('option', 'handle', '.ipaImageMove');

            }
        });
    },
    
    addPicture : function (fileName, title, status, coordinates) {
        var $this = this;
        var $newPictureRecord = $this.data('ipWidget_ipPictureGallery_container').pictureTemplate.clone();
        $newPictureRecord.ipWidget_ipPictureGallery_picture({'status' : status, 'fileName' : fileName, 'title' : title, 'coordinates' : coordinates});
        var $uploader = $this.find('.ipaUpload');
        if ($uploader.length > 0) {
            $($uploader).before($newPictureRecord);
        } else {
            $this.append($newPictureRecord);
        }
    },
    
    removePicture : function ($pictureObject) {
        $pictureObject.hide();
        $pictureObject.ipWidget_ipPictureGallery_picture('setStatus', 'deleted');
        
    },
    
    getPictures : function () {
        var $this = this;
        return $this.find('.ipaImageTemplate');
    }



    };

    $.fn.ipWidget_ipPictureGallery_container = function(method) {
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

            var data = $this.data('ipWidget_ipPictureGallery_picture');

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
                    smallPictureWidth : options.smallPictureWidth,
                    smallPictureHeight : options.smallPictureHeight
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
                $this.find('.ipaImageTitle').val(data.title);
            }
            
            
            
            //$this.find('.ipaImage').attr('src', ip.baseUrl + data.fileName);
            var pictureOptions = new Object;
            pictureOptions.picture = data.fileName;
            if (options.coordinates) {
                pictureOptions.cropX1 = options.coordinates.cropX1;
                pictureOptions.cropY1 = options.coordinates.cropY1;
                pictureOptions.cropX2 = options.coordinates.cropX2;
                pictureOptions.cropY2 = options.coordinates.cropY2;
            }
            console.log('options');
            console.log(options);
            console.log(pictureOptions);
            pictureOptions.windowWidth = options.smallPictureWidth;
            pictureOptions.windowHeight = options.smallPictureHeight;
            pictureOptions.enableChangeWidth = false;
            pictureOptions.enableChangeHeight = false;

            $this.find('.ipaImage').ipUploadPicture(pictureOptions);
            
            
//          handle uploading of new photo
//          if (ipUploadPicture.ipUploadPicture('getNewPictureUploaded')) {
//              var newPicture = ipUploadPicture.ipUploadPicture('getCurPicture');
//              if (newPicture) {
//                  data.newPicture = newPicture;
//              }
//          }
            
            
            
            
            $this.find('.ipaImageRemove').bind('click', 
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
        return $this.find('.ipaImageTitle').val();
    },
    
    getFileName : function() {
        var $this = this;
        var curPicture = $this.find('.ipaImage').ipUploadPicture('getCurPicture');
        return curPicture;
    },
    
    getCropCoordinates : function() {
        var $this = this;
        var ipUploadPicture = $this.find('.ipaImage');
        var cropCoordinates = ipUploadPicture.ipUploadPicture('getCropCoordinates');
        return cropCoordinates;
    },
        
    getStatus : function() {
        var $this = this;
        
        var tmpData = $this.data('ipWidget_ipPictureGallery_picture');
        if (tmpData.status == 'deleted') {
            return tmpData.status;
        }
        
        var ipUploadPicture = $this.find('.ipaImage');
        if (tmpData.status == 'new' || ipUploadPicture.ipUploadPicture('getNewPictureUploaded')) {
            return 'new';
        } else {
            if (ipUploadPicture.ipUploadPicture('getCropCoordinatesChanged') && ipUploadPicture.ipUploadPicture('getCurPicture') != false) {
                return 'coordinatesChanged';
            }
        }
        
        var tmpData = $this.data('ipWidget_ipPictureGallery_picture');
        //status, set on creation. Usually 'new' or 'present'
        return tmpData.status;
    },
    
    setStatus : function(newStatus) {
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
            $.error('Method ' + method + ' does not exist on jQuery.ipAdminWidgetButton');
        }

    };

})(jQuery);


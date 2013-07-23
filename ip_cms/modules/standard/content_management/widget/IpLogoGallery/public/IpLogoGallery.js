/**
 * @package ImpressPages
 *
 *
 */

function IpWidget_IpLogoGallery(widgetObject) {
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


        
        var container = this.widgetObject.find('.ipWidget_ipLogoGallery_container');
        var options = new Object;
        if (instanceData.data.logos) {
            options.logos = instanceData.data.logos;
        } else {
            options.logos = new Array();
        }
        options.logoWidth = this.widgetObject.find('input[name="logoWidth"]').val();
        options.logoHeight = this.widgetObject.find('input[name="logoHeight"]').val();
        options.logoTemplate = this.widgetObject.find('.ipaLogoTemplate');
        container.ipWidget_ipLogoGallery_container(options);
        
        
        this.widgetObject.bind('fileUploaded.ipUploadFile', this.fileUploaded);
        this.widgetObject.bind('error.ipUploadImage', {widgetController: this}, this.addError);
        this.widgetObject.bind('error.ipUploadFile', {widgetController: this}, this.addError);
        
        
    }

    function addError(event, errorMessage) {
        $(this).trigger('error.ipContentManagement', [errorMessage]);
    }    
    
    function fileUploaded(event, files) {
        var $this = $(this);

        var container = $this.find('.ipWidget_ipLogoGallery_container');
        for(var index in files) {
            container.ipWidget_ipLogoGallery_container('addLogo', files[index].file, '', '');
        }

    }
    

    
    function prepareData() {
        var data = Object();
        var container = this.widgetObject.find('.ipWidget_ipLogoGallery_container');
        
        data.logos = new Array();
        $logos = container.ipWidget_ipLogoGallery_container('getLogos');
        $logos.each(function(index) {
            var $this = $(this);
            var tmpLogo = new Object();
            tmpLogo.title = $this.ipWidget_ipLogoGallery_logo('getTitle');
            tmpLogo.link = $this.ipWidget_ipLogoGallery_logo('getLink');
            tmpLogo.fileName = $this.ipWidget_ipLogoGallery_logo('getFileName');
            tmpLogo.status = $this.ipWidget_ipLogoGallery_logo('getStatus');
            var tmpCropCoordinates = $this.ipWidget_ipLogoGallery_logo('getCropCoordinates');
            tmpLogo.cropX1 = tmpCropCoordinates.x1; 
            tmpLogo.cropY1 = tmpCropCoordinates.y1; 
            tmpLogo.cropX2 = tmpCropCoordinates.x2; 
            tmpLogo.cropY2 = tmpCropCoordinates.y2; 
            
            
            data.logos.push(tmpLogo);

        });


        $(this.widgetObject).trigger('preparedWidgetData.ipWidget', [ data ]);
    }


};




(function($) {

    var methods = {
            
    init : function(options) {

        return this.each(function() {

            var $this = $(this);

            var data = $this.data('ipWidget_ipLogoGallery_container');

            // If the plugin hasn't been initialized yet
            var logos = null;
            if (options.logos) {
                logos = options.logos;
            } else {
                logos = new Array();
            }

            
            if (!data) {
                $this.data('ipWidget_ipLogoGallery_container', {
                    logos : logos,
                    logoTemplate : options.logoTemplate,
                    logoWidth : options.logoWidth,
                    logoHeight : options.logoHeight
                });
                
                for (var i in logos) {
                    var coordinates = new Object();
                    coordinates.cropX1 = logos[i]['cropX1'];
                    coordinates.cropY1 = logos[i]['cropY1'];
                    coordinates.cropX2 = logos[i]['cropX2'];
                    coordinates.cropY2 = logos[i]['cropY2'];
                    $this.ipWidget_ipLogoGallery_container('addLogo', logos[i]['logoOriginal'], logos[i]['title'], logos[i]['link'], 'present', coordinates); 
                }
                $this.bind('removeLogo.ipWidget_ipLogoGallery', function(event, logoObject) {
                    var $logoObject = $(logoObject);
                    $logoObject.ipWidget_ipLogoGallery_container('removeLogo', $logoObject);
                });
                
                $( ".ipWidget_ipLogoGallery_container" ).sortable();
                $( ".ipWidget_ipLogoGallery_container" ).sortable('option', 'handle', '.ipaLogoMove');

            }
        });
    },
    
    addLogo : function (fileName, title, link, status, coordinates) {
        var $this = this;
        var data = $this.data('ipWidget_ipLogoGallery_container');
        var $newLogoRecord = $this.data('ipWidget_ipLogoGallery_container').logoTemplate.clone();
        $newLogoRecord.ipWidget_ipLogoGallery_logo({'logoWidth' : data.logoWidth, 'logoHeight' : data.logoHeight, 'status' : status, 'fileName' : fileName, 'title' : title, 'link' : link, 'coordinates' : coordinates});
        var $uploader = $this.find('.ipmBrowseButton');
        if ($uploader.length > 0) {
            $($uploader).before($newLogoRecord);
        } else {
            $this.append($newLogoRecord);
        }
    },
    
    removeLogo : function ($logoObject) {
        $logoObject.hide();
        $logoObject.ipWidget_ipLogoGallery_logo('setStatus', 'deleted');
        
    },
    
    getLogos : function () {
        var $this = this;
        return $this.find('.ipaLogoTemplate');
    }



    };

    $.fn.ipWidget_ipLogoGallery_container = function(method) {
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

            var data = $this.data('ipWidget_ipLogoGallery_logo');

            var status = 'new';
            if (options.status) {
                status = options.status;
            }
            
            // If the plugin hasn't been initialized yet
            if (!data) {
                var data = {
                    title : '',
                    link : '',
                    fileName : '',
                    status : status,
                    logoWidth : options.logoWidth,
                    logoHeight : options.logoHeight
                };
                
                if (options.title) {
                    data.title = options.title;
                }
                if (options.link) {
                    data.link = options.link;
                }
                if (options.fileName) {
                    data.fileName = options.fileName;
                }
                if (options.status) {
                    data.status = options.status;
                }
                
                $this.data('ipWidget_ipLogoGallery_logo', {
                    title : data.title,
                    link : data.link,
                    fileName : data.fileName,
                    status : data.status
                });
                $this.find('.ipaLogoTitle').val(data.title);
            }
            
            
            
            //$this.find('.ipaLogo').attr('src', ip.baseUrl + data.fileName);
            var logoOptions = new Object;
            logoOptions.image = data.fileName;
            if (options.coordinates) {
                logoOptions.cropX1 = options.coordinates.cropX1;
                logoOptions.cropY1 = options.coordinates.cropY1;
                logoOptions.cropX2 = options.coordinates.cropX2;
                logoOptions.cropY2 = options.coordinates.cropY2;
            }
            logoOptions.windowWidth = options.logoWidth;
            logoOptions.windowHeight = options.logoHeight;
            logoOptions.enableChangeWidth = false;
            logoOptions.enableChangeHeight = false;
            logoOptions.enableScale = false;
            logoOptions.enableFraming = false;
            logoOptions.enableUnderscale = true;
            logoOptions.autosizeType = 'fit';
            
            $this.find('.ipaLogo').ipUploadImage(logoOptions);
            
            

            
            
            $this.find('.ipaLogoRemove').bind('click', 
                function(event){
                    $this = $(this);
                    $this.trigger('removeClick.ipWidget_ipLogoGallery');
                    return false;
                }
            );
            $this.find('.ipaLogoLink').bind('click', 
                    function(event){
                        $this = $(this);
                        $this.trigger('linkClick.ipWidget_ipLogoGallery');
                        return false;
                    }
                );
            $this.bind('removeClick.ipWidget_ipLogoGallery', function(event) {
                $this.trigger('removeLogo.ipWidget_ipLogoGallery', this);
            });
            $this.bind('linkClick.ipWidget_ipLogoGallery', function(event) {
                $this = $(this);
                var data = $this.data('ipWidget_ipLogoGallery_logo');
                var newLink;
                newLink = prompt('Where this logo should link?', data.link)
                if (newLink !== null) {
                    data.link = newLink;
                    $this.data('ipWidget_ipLogoGallery_logo', data);
                }
            });
            return $this;
        });
    },
    
    getTitle : function() {
        var $this = this;
        return $this.find('.ipaLogoTitle').val();
    },
    
    
    getLink : function() {
        var $this = this;
        return $this.data('ipWidget_ipLogoGallery_logo').link;
    },
    
    getFileName : function() {
        var $this = this;
        var curImage = $this.find('.ipaLogo').ipUploadImage('getCurImage');
        return curImage;
    },
    
    getCropCoordinates : function() {
        var $this = this;
        var ipUploadLogo = $this.find('.ipaLogo');
        var cropCoordinates = ipUploadLogo.ipUploadImage('getCropCoordinates');
        return cropCoordinates;
    },
        
    getStatus : function() {
        var $this = this;
        
        var tmpData = $this.data('ipWidget_ipLogoGallery_logo');
        if (tmpData.status == 'deleted') {
            return tmpData.status;
        }
        
        var ipUploadLogo = $this.find('.ipaLogo');
        if (tmpData.status == 'new' || ipUploadLogo.ipUploadImage('getNewImageUploaded')) {
            return 'new';
        } else {
            if (ipUploadLogo.ipUploadImage('getCropCoordinatesChanged') && ipUploadLogo.ipUploadImage('getCurImage') != false) {
                return 'coordinatesChanged';
            }
        }
        
        var tmpData = $this.data('ipWidget_ipLogoGallery_logo');
        //status, set on creation. Usually 'new' or 'present'
        return tmpData.status;
    },
    
    setStatus : function(newStatus) {
        var $this = $(this);
        var tmpData = $this.data('ipWidget_ipLogoGallery_logo');
        tmpData.status = newStatus;
        $this.data('ipWidget_ipLogoGallery_logo', tmpData);
    }
    



    };

    $.fn.ipWidget_ipLogoGallery_logo = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipAdminWidgetButton');
        }

    };

})(jQuery);


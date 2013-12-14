/**
 * @package ImpressPages
 *
 *
 */

function IpWidget_IpImageGallery() {
    "use strict";
    this.$widgetObject = null;
    this.data = null;


    this.init = function($widgetObject, data) {
        var currentScope = this;
        this.$widgetObject = $widgetObject;
        this.data = data;

        this.$widgetObject.on('click', $.proxy(this.focus, this));
        this.$widgetObject.on('blur', $.proxy(this.blur, this));
        this.$widgetObject.find('a').on('click', function(e){e.preventDefault();});//turn off lightbox
        $('body').on('click', $.proxy(function(e) {
            var $target = $(e.target);
            if (!$target.hasClass('ipWidget-IpImageGallery')) {
                $target = $target.closest('.ipWidget-IpImageGallery');
            }
            if ($target.length == 0 || $target.data('widgetinstanceid') != this.$widgetObject.data('widgetinstanceid')) {
                $.proxy(this.blur, this)();
            }

        }, this));

        var $list = this.$widgetObject.find('ul');
        $list.sortable();
        $list.disableSelection();
        $list.on( "sortstart", function( event, ui ) {
            currentScope.dragItemOriginalPosition = $(ui.item).index();
        });
        $list.on( "sortstop", function( event, ui ) {
            var data = {};
            data.method = 'move';
            data.originalPosition = currentScope.dragItemOriginalPosition;
            data.newPosition = $(ui.item).index();
            if (data.newPosition != data.originalPosition) {
                currentScope.$widgetObject.save(data, true);
            }
        } );
    }


    this.focus = function () {
        var thisContext = this;
        if (this.$widgetObject.find('.ipsAdd').length) {
            //already initialized
            return;
        }
        var $addButton = $('#ipWidgetGallerySnippet').find('.ipsAdd').clone().detach();
        this.$widgetObject.append($addButton);
        $addButton.click(function(e){
            e.preventDefault();
            var repository = new ipRepository({preview: 'thumbnails', filter: 'image'});
            repository.bind('ipRepository.filesSelected', $.proxy(thisContext.filesSelected, thisContext));
        });
    }

    this.blur = function () {
        this.$widgetObject.find('.ipsAdd').remove();
    }


    this.filesSelected = function(event, files) {
        var $this = $(this);

        var data = this.data;
        var data = {
            method: 'add'
        };
        $.each(files, function(key, value) {
            if (!data.images) {
                data.images = [];
            }
            data.images[data.images.length] = { //AJAX skips arrays without integer key
                fileName: value.fileName,
                status: "new"
            };
        });

        this.$widgetObject.save(data, 1, function($widget){
            $widget.click();
        });
    }




    function addError(event, errorMessage) {
        $(this).trigger('error.ipContentManagement', [errorMessage]);
    }



    
    function prepareData() {
        var data = Object();
        var container = this.widgetObject.find('.ipWidget_ipImageGallery_container');
        
        data.images = new Array();
        var $images = container.ipWidget_ipImageGallery_container('getImages');
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




